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

namespace paygw_chargily\local;

use paygw_chargily\api\client;

/**
 * Handles one webhook call. Kept apart from webhook.php so it can be tested without HTTP.
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webhook {
    /**
     * Check and act on a webhook.
     *
     * @param string $body Raw request body.
     * @param string $signature The signature header.
     * @return int HTTP status to answer with.
     */
    public static function handle(string $body, string $signature): int {
        $event = json_decode($body, true);
        $checkoutid = is_array($event) ? (string) ($event['data']['id'] ?? '') : '';
        if ($checkoutid === '' || $signature === '') {
            return 400;
        }
        $row = checkout_manager::find($checkoutid);
        if (!$row) {
            // Not ours (another site on the same Chargily account): acknowledge so Chargily stops retrying.
            return 200;
        }
        $config = checkout_manager::account_config((int) $row->accountid);
        if (!client::valid_signature($body, $signature, (string) ($config['secretkey'] ?? ''))) {
            return 403;
        }
        try {
            checkout_manager::sync((int) $row->id);
        } catch (\moodle_exception $e) {
            // Chargily retries when it does not get a 200.
            return 503;
        }
        return 200;
    }
}
