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

namespace paygw_chargily\payhub;

use paygw_chargily\gateway;
use paygw_chargily\local\checkout_manager;
use paygw_payhub\request;

defined('MOODLE_INTERNAL') || die();

// The hub is optional: without it this class is not declared at all, so loading the file never fails.
if (!interface_exists(\paygw_payhub\provider::class)) {
    return;
}

/**
 * Lets the DevLMS payment hub offer Chargily Pay. Only the hub loads this class.
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \paygw_payhub\provider {
    /**
     * Gateway name.
     *
     * @return string
     */
    public static function name(): string {
        return 'chargily';
    }

    /**
     * Local cards in dinars.
     *
     * @return string
     */
    public static function region(): string {
        return self::LOCAL;
    }

    /**
     * Dinars only.
     *
     * @return string[]
     */
    public static function currencies(): array {
        return gateway::get_supported_currencies();
    }

    /**
     * The card types the account turned on.
     *
     * @param array $config
     * @return string[]
     */
    public static function methods(array $config): array {
        $out = [];
        foreach (gateway::methods($config) as $method) {
            $out[$method] = get_string('method:' . $method, 'paygw_chargily');
        }
        return $out;
    }

    /**
     * Start a Chargily checkout for the hub; Chargily's confirmation goes back to the hub.
     *
     * @param request $request
     * @return string
     */
    public static function start(request $request): string {
        return checkout_manager::start(
            $request->component,
            $request->paymentarea,
            $request->itemid,
            $request->userid,
            $request->method,
            $request->description,
            'paygw_payhub',
            $request->reference,
            $request->amount
        );
    }
}
