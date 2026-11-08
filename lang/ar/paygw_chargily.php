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
 * Strings for paygw_chargily (Arabic).
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['back'] = 'رجوع';
$string['canceled'] = 'أُلغيت عملية الدفع. لم يُقتطع أي مبلغ.';
$string['checkagain'] = 'تحقّق مرة أخرى';
$string['error:api'] = 'Chargily: {$a}';
$string['error:busy'] = 'يجري التحقق من هذه الدفعة الآن. حاول مرة أخرى بعد لحظات.';
$string['error:currency'] = 'لا يقبل Chargily Pay إلا الدينار الجزائري (DZD).';
$string['error:generic'] = '{$a}';
$string['error:nomethod'] = 'اختر وسيلة دفع واحدة على الأقل: الذهبية (EDAHABIA) أو CIB.';
$string['error:owner'] = 'تمّت عملية الدفع، لكن {$a} لم يتمكّن من تسجيلها. اتصل بمدير الموقع.';
$string['error:secretkey'] = 'أدخل مفتاح Chargily السري. تبدأ مفاتيح الاختبار بـ test_sk_ ومفاتيح الإنتاج بـ live_sk_.';
$string['failed'] = 'لم تنجح عملية الدفع. لم يُقتطع أي مبلغ، ويمكنك المحاولة مرة أخرى.';
$string['gatewaydescription'] = 'بطاقات الذهبية و CIB بالدينار الجزائري عبر Chargily Pay.';
$string['gatewayname'] = 'بطاقة الذهبية أو CIB (Chargily Pay)';
$string['method:cib'] = 'CIB';
$string['method:cib_help'] = 'بطاقة بنكية (CIB)';
$string['method:edahabia'] = 'الذهبية';
$string['method:edahabia_help'] = 'بطاقة بريد الجزائر';
$string['methods'] = 'وسائل الدفع';
$string['paid'] = 'تمّ استلام الدفعة. شكرًا لك.';
$string['paywith'] = 'الدفع بالبطاقة';
$string['pluginname'] = 'Chargily Pay';
$string['pluginname_desc'] = 'بطاقات الذهبية و CIB بالدينار الجزائري عبر Chargily Pay.';
$string['privacy:metadata:chargily'] = 'لإتمام الدفع، تُرسَل إلى Chargily Pay قيمةُ المبلغ ووسيلةُ الدفع ووصفُ ما يُدفع مقابله ومرجعٌ للعملية. يُدخل الدافع بيانات بطاقته في صفحة Chargily وليس في Moodle.';
$string['privacy:metadata:chargily:amount'] = 'المبلغ المطلوب دفعه.';
$string['privacy:metadata:chargily:description'] = 'ما يُدفع مقابله.';
$string['privacy:metadata:chargily:metadata'] = 'مرجع لهذه الدفعة في Moodle.';
$string['privacy:metadata:paygw_chargily'] = 'عمليات الدفع عبر Chargily التي بدأت في هذا الموقع.';
$string['privacy:metadata:paygw_chargily:amount'] = 'المبلغ المقتطع بالدينار.';
$string['privacy:metadata:paygw_chargily:checkoutid'] = 'معرّف عملية الدفع لدى Chargily.';
$string['privacy:metadata:paygw_chargily:method'] = 'نوع البطاقة المستخدمة: الذهبية أو CIB.';
$string['privacy:metadata:paygw_chargily:status'] = 'حالة الدفع: قيد الانتظار أو مدفوعة أو فاشلة أو ملغاة.';
$string['privacy:metadata:paygw_chargily:timecreated'] = 'تاريخ بدء عملية الدفع.';
$string['privacy:metadata:paygw_chargily:userid'] = 'الشخص الذي يدفع.';
$string['redirectnote'] = 'تُدخل بيانات بطاقتك في صفحة Chargily الآمنة، ثم تعود إلى هنا.';
$string['secretkey'] = 'المفتاح السري';
$string['secretkey_help'] = 'من ركن المطوّرين في لوحة تحكم Chargily Pay. مفتاح الاختبار (test_sk_...) يستخدم وضع الاختبار حيث لا تتحرك أي أموال، ومفتاح الإنتاج (live_sk_...) يستقبل مدفوعات حقيقية.';
$string['waiting'] = 'لم يؤكّد Chargily هذه الدفعة بعد. يستغرق ذلك عادة بضع ثوانٍ.';
$string['webhookurl'] = 'رابط الويب هوك';
$string['webhookurl_help'] = 'يُرسَل هذا العنوان إلى Chargily مع كل عملية دفع، ولا حاجة لأي إعداد في لوحة تحكم Chargily.';
$string['event:paymentfailed'] = 'فشل دفع Chargily أو أُلغي';
$string['event:paymentreceived'] = 'استُلمت دفعة Chargily';
