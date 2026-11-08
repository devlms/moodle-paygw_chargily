<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace paygw_chargily\api;

/**
 * Chargily Pay v2: create a checkout, read one back, check a webhook signature.
 *
 * The mode (test or live) follows the secret key: test keys start with test_sk_, live keys with live_sk_.
 * Every call goes through Moodle's curl, so the site's proxy and blocked hosts apply and PHPUnit can
 * mock the responses.
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client {
    /** @var string Base URL in test mode. */
    public const TEST_URL = 'https://pay.chargily.net/test/api/v2';
    /** @var string Base URL in live mode. */
    public const LIVE_URL = 'https://pay.chargily.net/api/v2';

    /** @var string */
    private string $base;

    /**
     * Constructor.
     *
     * @param string $secretkey test_sk_... or live_sk_...
     * @param \curl|null $curl For tests; a new curl per request otherwise.
     */
    public function __construct(
        /** @var string The secret key. */
        private readonly string $secretkey,
        /** @var \curl|null Curl to use, for tests. */
        private readonly ?\curl $curl = null
    ) {
        $mode = self::key_mode($secretkey);
        if (!$mode) {
            throw new api_exception(get_string('error:secretkey', 'paygw_chargily'));
        }
        $this->base = $mode === 'test' ? self::TEST_URL : self::LIVE_URL;
    }

    /**
     * Test or live, from the key's prefix; null when it is not a Chargily secret key.
     *
     * @param string $secretkey
     * @return string|null
     */
    public static function key_mode(string $secretkey): ?string {
        return match (true) {
            str_starts_with($secretkey, 'test_sk_') && strlen($secretkey) > 12 => 'test',
            str_starts_with($secretkey, 'live_sk_') && strlen($secretkey) > 12 => 'live',
            default => null,
        };
    }

    /**
     * Create a hosted checkout.
     *
     * @param int $amount Whole dinars.
     * @param string $method edahabia or cib
     * @param string $successurl Where Chargily sends the payer after paying.
     * @param string $failureurl Where Chargily sends the payer after a failure or cancel.
     * @param string $webhookurl Where Chargily posts the result.
     * @param string $description Shown on the checkout page.
     * @param string $locale ar, en or fr
     * @param array $metadata Echoed back on the checkout and in webhooks.
     * @return array The checkout, with at least id and checkout_url.
     */
    public function create_checkout(
        int $amount,
        string $method,
        string $successurl,
        string $failureurl,
        string $webhookurl,
        string $description,
        string $locale,
        array $metadata
    ): array {
        $checkout = $this->request('post', '/checkouts', [
            'amount' => $amount,
            'currency' => 'dzd',
            'payment_method' => $method,
            'success_url' => $successurl,
            'failure_url' => $failureurl,
            'webhook_endpoint' => $webhookurl,
            'description' => $description,
            'locale' => in_array($locale, ['ar', 'en', 'fr'], true) ? $locale : 'en',
            'metadata' => $metadata,
        ]);
        if (empty($checkout['id']) || empty($checkout['checkout_url'])) {
            throw new api_exception(get_string('error:api', 'paygw_chargily', 'no checkout url'));
        }
        return $checkout;
    }

    /**
     * Read a checkout back: the only source of truth for its status.
     *
     * @param string $id
     * @return array
     */
    public function get_checkout(string $id): array {
        if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', $id)) {
            throw new api_exception(get_string('error:api', 'paygw_chargily', 'bad checkout id'));
        }
        return $this->request('get', '/checkouts/' . $id);
    }

    /**
     * Whether a webhook body was signed with this secret key.
     *
     * @param string $body The raw request body, exactly as received.
     * @param string $signature The signature header.
     * @param string $secretkey
     * @return bool
     */
    public static function valid_signature(string $body, string $signature, string $secretkey): bool {
        return $signature !== '' && hash_equals(hash_hmac('sha256', $body, $secretkey), strtolower(trim($signature)));
    }

    /**
     * One API call.
     *
     * @param string $verb get or post
     * @param string $path
     * @param array|null $body Sent as JSON.
     * @return array The decoded JSON answer.
     */
    private function request(string $verb, string $path, ?array $body = null): array {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        $curl = $this->curl ?? new \curl();
        $curl->setHeader([
            'Authorization: Bearer ' . $this->secretkey,
            'Accept: application/json',
            'Content-Type: application/json',
        ]);
        $options = ['CURLOPT_TIMEOUT' => 20, 'CURLOPT_CONNECTTIMEOUT' => 8];
        $raw = $verb === 'post'
            ? $curl->post($this->base . $path, json_encode($body), $options)
            : $curl->get($this->base . $path, [], $options);
        if ($curl->get_errno()) {
            throw new api_exception(get_string('error:api', 'paygw_chargily', $curl->error));
        }
        $status = (int) ($curl->get_info()['http_code'] ?? 0);
        $data = json_decode((string) $raw, true);
        if ($status >= 400 || !is_array($data)) {
            $why = is_array($data) && !empty($data['message']) ? (string) $data['message'] : "HTTP $status";
            throw new api_exception(get_string('error:api', 'paygw_chargily', shorten_text($why, 200)));
        }
        return $data;
    }
}
