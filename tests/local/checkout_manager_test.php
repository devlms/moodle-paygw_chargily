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
 * Checkouts from start to delivery, with Chargily's answers mocked.
 *
 * @package    paygw_chargily
 * @category   test
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \paygw_chargily\local\checkout_manager
 * @covers     \paygw_chargily\local\webhook
 * @covers     \paygw_chargily\api\client
 * @covers     \paygw_chargily\gateway
 * @covers     \paygw_chargily\event\payment_received
 * @covers     \paygw_chargily\event\payment_failed
 */
final class checkout_manager_test extends \advanced_testcase {
    /** @var string Test secret key. */
    private const KEY = 'test_sk_0123456789abcdef';
    /** @var \stdClass */
    private \stdClass $course;
    /** @var \stdClass */
    private \stdClass $student;
    /** @var int enrol_fee instance id */
    private int $instanceid;

    protected function setUp(): void {
        global $CFG, $DB;
        parent::setUp();
        $this->resetAfterTest();
        require_once($CFG->libdir . '/filelib.php');
        set_config('enrol_plugins_enabled', 'manual,fee');
        \core\plugininfo\paygw::enable_plugin('chargily', 1);
        $gen = $this->getDataGenerator();
        $account = $gen->get_plugin_generator('core_payment')->create_payment_account();
        \core_payment\helper::save_payment_gateway((object) ['accountid' => $account->get('id'), 'gateway' => 'chargily',
            'enabled' => 1, 'config' => json_encode(['secretkey' => self::KEY, 'method_edahabia' => 1, 'method_cib' => 1])]);
        $this->course = $gen->create_course();
        $studentrole = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $this->instanceid = (int) enrol_get_plugin('fee')->add_instance($this->course, [
            'cost' => 2500.40, 'currency' => 'DZD', 'customint1' => $account->get('id'), 'roleid' => $studentrole,
        ]);
        $this->student = $gen->create_user();
        $this->setUser($this->student);
        checkout_manager::$curl = null;
    }

    /**
     * A curl that answers with these bodies in order.
     *
     * @param array ...$answers Decoded JSON answers.
     */
    private function answers(array ...$answers): void {
        foreach ($answers as $answer) {
            \curl::mock_response(json_encode($answer));
        }
    }

    /**
     * Start a checkout for the student and return its row.
     *
     * @return \stdClass
     */
    private function started(): \stdClass {
        global $DB;
        $this->answers(['id' => 'chk_1', 'checkout_url' => 'https://pay.chargily.net/test/checkout/chk_1', 'status' => 'pending']);
        $url = checkout_manager::start('enrol_fee', 'fee', $this->instanceid, (int) $this->student->id, 'cib', 'Physics');
        $this->assertSame('https://pay.chargily.net/test/checkout/chk_1', $url);
        return $DB->get_record('paygw_chargily', ['checkoutid' => 'chk_1'], '*', MUST_EXIST);
    }

    public function test_start_records_a_pending_checkout_in_whole_dinars(): void {
        $row = $this->started();
        $this->assertSame(checkout_manager::PENDING, $row->status);
        // 2500.40 rounds up: Chargily takes whole dinars, and the price is never undercharged.
        $this->assertEquals(2501, $row->amount);
        $this->assertSame('cib', $row->method);
        $this->assertSame(checkout_manager::SELF, $row->owner);
    }

    public function test_a_paid_checkout_enrols_the_student_once(): void {
        global $DB;
        $row = $this->started();
        $this->answers(['id' => 'chk_1', 'status' => 'paid', 'amount' => 2501, 'currency' => 'dzd']);
        $sink = $this->redirectEvents();
        $after = checkout_manager::sync((int) $row->id);
        $received = array_values(array_filter($sink->get_events(), fn($e) => $e instanceof \paygw_chargily\event\payment_received));
        $this->assertCount(1, $received);
        $this->assertEquals(2501, $received[0]->other['amount']);
        $this->assertEquals($this->student->id, $received[0]->relateduserid);
        $this->assertNotEmpty($received[0]->get_description());
        $sink->close();
        $this->assertSame(checkout_manager::PAID, $after->status);
        $this->assertTrue(is_enrolled(\context_course::instance($this->course->id), $this->student));
        $payment = $DB->get_record('payments', ['id' => $after->paymentid], '*', MUST_EXIST);
        $this->assertSame('chargily', $payment->gateway);
        $this->assertSame('DZD', $payment->currency);
        // A second webhook or return visit changes nothing and asks Chargily nothing.
        checkout_manager::sync((int) $row->id);
        $this->assertSame(1, $DB->count_records('payments'));
    }

    public function test_a_different_amount_is_not_delivered(): void {
        $row = $this->started();
        $this->answers(['id' => 'chk_1', 'status' => 'paid', 'amount' => 10, 'currency' => 'dzd']);
        $after = checkout_manager::sync((int) $row->id);
        $this->assertDebuggingCalled();
        $this->assertSame(checkout_manager::FAILED, $after->status);
        $this->assertFalse(is_enrolled(\context_course::instance($this->course->id), $this->student));
    }

    public function test_canceled_failed_and_still_pending(): void {
        $row = $this->started();
        $this->answers(['id' => 'chk_1', 'status' => 'pending']);
        $this->assertSame(checkout_manager::PENDING, checkout_manager::sync((int) $row->id)->status);
        $this->answers(['id' => 'chk_1', 'status' => 'canceled']);
        $this->assertSame(checkout_manager::CANCELED, checkout_manager::sync((int) $row->id)->status);
    }

    public function test_methods_the_account_turned_off_are_refused(): void {
        global $DB;
        $gw = $DB->get_record('payment_gateways', ['gateway' => 'chargily']);
        $DB->set_field(
            'payment_gateways',
            'config',
            json_encode(['secretkey' => self::KEY, 'method_edahabia' => 1]),
            ['id' => $gw->id]
        );
        $this->expectException(\moodle_exception::class);
        checkout_manager::start('enrol_fee', 'fee', $this->instanceid, (int) $this->student->id, 'cib', 'Physics');
    }

    public function test_webhook_checks_the_signature_then_reads_chargily(): void {
        $row = $this->started();
        $body = json_encode(['type' => 'checkout.paid', 'data' => ['id' => 'chk_1', 'status' => 'paid']]);
        $this->assertSame(400, webhook::handle('not json', 'x'));
        $this->assertSame(403, webhook::handle($body, hash_hmac('sha256', $body, 'test_sk_wrongwrongwrong')));
        $unknown = json_encode(['data' => ['id' => 'someone_else']]);
        $this->assertSame(200, webhook::handle($unknown, 'x'), 'Unknown checkouts are acknowledged.');
        $this->assertSame(checkout_manager::PENDING, checkout_manager::find('chk_1')->status, 'A forged webhook changed nothing.');
        $this->answers(['id' => 'chk_1', 'status' => 'paid', 'amount' => 2501, 'currency' => 'dzd']);
        $this->assertSame(200, webhook::handle($body, hash_hmac('sha256', $body, self::KEY)));
        $this->assertSame(checkout_manager::PAID, checkout_manager::find('chk_1')->status);
        $this->assertNotEmpty($row);
    }

    public function test_client_keys_signatures_and_errors(): void {
        $this->assertSame('test', client::key_mode(self::KEY));
        $this->assertSame('live', client::key_mode('live_sk_0123456789abcdef'));
        $this->assertNull(client::key_mode('pk_whatever'));
        $this->assertTrue(client::valid_signature('{}', hash_hmac('sha256', '{}', self::KEY), self::KEY));
        $this->assertFalse(client::valid_signature('{}', '', self::KEY));
        \curl::mock_response(json_encode(['message' => 'The amount must be at least 75.']));
        $curl = new \curl();
        $client = new client(self::KEY, $curl);
        try {
            $client->get_checkout('bad id!');
            $this->fail('Accepted a malformed id');
        } catch (\paygw_chargily\api\api_exception $e) {
            $this->assertStringContainsString('bad checkout id', $e->getMessage());
        }
        $this->assertSame(['DZD'], \paygw_chargily\gateway::get_supported_currencies());
        $this->assertSame(['edahabia'], \paygw_chargily\gateway::methods(['method_edahabia' => 1]));
    }

    public function test_through_the_hub_the_hub_saves_and_delivers(): void {
        global $DB;
        if (!class_exists(\paygw_payhub\hub::class)) {
            $this->markTestSkipped('The payment hub is not installed.');
        }
        \core\plugininfo\paygw::enable_plugin('payhub', 1);
        $accountid = (int) $DB->get_field('payment_gateways', 'accountid', ['gateway' => 'chargily']);
        \core_payment\helper::save_payment_gateway((object) ['accountid' => $accountid, 'gateway' => 'payhub',
            'enabled' => 1, 'config' => json_encode(['use_chargily' => 1])]);
        $this->answers(['id' => 'chk_hub', 'checkout_url' => 'https://pay.chargily.net/test/checkout/chk_hub']);
        $url = \paygw_payhub\hub::start(
            'enrol_fee',
            'fee',
            $this->instanceid,
            (int) $this->student->id,
            'chargily',
            'edahabia',
            'Physics'
        );
        $this->assertSame('https://pay.chargily.net/test/checkout/chk_hub', $url);
        $row = checkout_manager::find('chk_hub');
        $this->assertSame('paygw_payhub', $row->owner);
        $this->answers(['id' => 'chk_hub', 'status' => 'paid', 'amount' => 2501, 'currency' => 'dzd']);
        $after = checkout_manager::sync((int) $row->id);
        $this->assertSame(checkout_manager::PAID, $after->status);
        $this->assertTrue(is_enrolled(\context_course::instance($this->course->id), $this->student));
        $hubrow = $DB->get_record('paygw_payhub', ['reference' => $row->reference]);
        $this->assertSame('paid', $hubrow->status);
        $this->assertEquals($after->paymentid, $hubrow->paymentid);
        $this->assertSame('chargily', $DB->get_field('payments', 'gateway', ['id' => $after->paymentid]));
    }
}
