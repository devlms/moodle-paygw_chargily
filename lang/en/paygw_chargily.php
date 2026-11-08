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

/**
 * Strings for paygw_chargily.
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['back'] = 'Back';
$string['canceled'] = 'The payment was canceled. Nothing was charged.';
$string['checkagain'] = 'Check again';
$string['error:api'] = 'Chargily: {$a}';
$string['error:busy'] = 'This payment is being checked right now. Try again in a moment.';
$string['error:currency'] = 'Chargily Pay takes Algerian dinars (DZD) only.';
$string['error:generic'] = '{$a}';
$string['error:nomethod'] = 'Choose at least one payment method: EDAHABIA or CIB.';
$string['error:owner'] = 'The payment went through, but {$a} could not record it. Contact the site administrator.';
$string['error:secretkey'] = 'Enter a Chargily secret key. Test keys start with test_sk_, live keys with live_sk_.';
$string['event:paymentfailed'] = 'Chargily payment failed or canceled';
$string['event:paymentreceived'] = 'Chargily payment received';
$string['failed'] = 'The payment did not go through. Nothing was charged; you can try again.';
$string['gatewaydescription'] = 'EDAHABIA and CIB cards in Algerian dinars, through Chargily Pay.';
$string['gatewayname'] = 'EDAHABIA or CIB card (Chargily Pay)';
$string['method:cib'] = 'CIB';
$string['method:cib_help'] = 'Bank card (CIB)';
$string['method:edahabia'] = 'EDAHABIA';
$string['method:edahabia_help'] = 'Algérie Poste card';
$string['methods'] = 'Payment methods';
$string['paid'] = 'Payment received. Thank you.';
$string['paywith'] = 'Pay by card';
$string['pluginname'] = 'Chargily Pay';
$string['pluginname_desc'] = 'EDAHABIA and CIB cards in Algerian dinars, through Chargily Pay.';
$string['privacy:metadata:chargily'] = 'To take a payment, the amount, the payment method, a description of what is paid for and a reference are sent to Chargily Pay. The payer enters their card details on Chargily\'s page, not in Moodle.';
$string['privacy:metadata:chargily:amount'] = 'The amount to pay.';
$string['privacy:metadata:chargily:description'] = 'What is paid for.';
$string['privacy:metadata:chargily:metadata'] = 'A reference to this payment in Moodle.';
$string['privacy:metadata:paygw_chargily'] = 'Chargily checkouts started on this site.';
$string['privacy:metadata:paygw_chargily:amount'] = 'The amount charged, in dinars.';
$string['privacy:metadata:paygw_chargily:checkoutid'] = 'The checkout id at Chargily.';
$string['privacy:metadata:paygw_chargily:method'] = 'The card type used: EDAHABIA or CIB.';
$string['privacy:metadata:paygw_chargily:status'] = 'Whether the checkout is pending, paid, failed or canceled.';
$string['privacy:metadata:paygw_chargily:timecreated'] = 'When the checkout was started.';
$string['privacy:metadata:paygw_chargily:userid'] = 'The person paying.';
$string['redirectnote'] = 'You enter your card details on Chargily\'s secure page, then come back here.';
$string['secretkey'] = 'Secret key';
$string['secretkey_help'] = 'From the Developers Corner of your Chargily Pay dashboard. A test key (test_sk_...) uses Chargily\'s test mode, where no money moves; a live key (live_sk_...) takes real payments.';
$string['waiting'] = 'Chargily has not confirmed this payment yet. This usually takes a few seconds.';
$string['webhookurl'] = 'Webhook URL';
$string['webhookurl_help'] = 'Chargily is told about this address with each checkout; nothing to set up in the Chargily dashboard.';
