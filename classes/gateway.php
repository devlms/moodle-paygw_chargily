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

namespace paygw_chargily;

/**
 * Chargily Pay: EDAHABIA and CIB cards in Algerian dinars.
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_payment\gateway {
    /** @var string[] Payment methods Chargily accepts, in the order they are offered. */
    public const METHODS = ['edahabia', 'cib'];

    /**
     * Chargily Pay takes Algerian dinars only.
     *
     * @return string[]
     */
    public static function get_supported_currencies(): array {
        return ['DZD'];
    }

    /**
     * Settings for a payment account.
     *
     * @param \core_payment\form\account_gateway $form
     */
    public static function add_configuration_to_gateway_form(\core_payment\form\account_gateway $form): void {
        $mform = $form->get_mform();
        $mform->addElement('passwordunmask', 'secretkey', get_string('secretkey', 'paygw_chargily'));
        $mform->setType('secretkey', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('secretkey', 'secretkey', 'paygw_chargily');
        $mform->addElement(
            'advcheckbox',
            'method_edahabia',
            get_string('methods', 'paygw_chargily'),
            get_string('method:edahabia', 'paygw_chargily')
        );
        $mform->setDefault('method_edahabia', 1);
        $mform->addElement('advcheckbox', 'method_cib', '', get_string('method:cib', 'paygw_chargily'));
        $mform->setDefault('method_cib', 1);
        $mform->addElement(
            'static',
            'webhookurl',
            get_string('webhookurl', 'paygw_chargily'),
            \html_writer::tag('code', (new \moodle_url('/payment/gateway/chargily/webhook.php'))->out(false)) .
            \html_writer::div(get_string('webhookurl_help', 'paygw_chargily'), 'form-text text-muted')
        );
    }

    /**
     * A secret key is needed to enable it, and at least one payment method.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass $data
     * @param array $files
     * @param array $errors
     */
    public static function validate_gateway_form(
        \core_payment\form\account_gateway $form,
        \stdClass $data,
        array $files,
        array &$errors
    ): void {
        if (empty($data->enabled)) {
            return;
        }
        if (!api\client::key_mode((string) ($data->secretkey ?? ''))) {
            $errors['secretkey'] = get_string('error:secretkey', 'paygw_chargily');
        }
        if (empty($data->method_edahabia) && empty($data->method_cib)) {
            $errors['method_edahabia'] = get_string('error:nomethod', 'paygw_chargily');
        }
    }

    /**
     * The methods an account has turned on.
     *
     * @param array $config Gateway configuration of the account.
     * @return string[]
     */
    public static function methods(array $config): array {
        return array_values(array_filter(self::METHODS, fn($m) => !empty($config['method_' . $m])));
    }
}
