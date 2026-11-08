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
 * Strings for paygw_chargily (French).
 *
 * @package    paygw_chargily
 * @copyright  2026 DevLMS <contact@devlms.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['back'] = 'Retour';
$string['canceled'] = 'Le paiement a été annulé. Rien n\'a été débité.';
$string['checkagain'] = 'Vérifier à nouveau';
$string['error:api'] = 'Chargily : {$a}';
$string['error:busy'] = 'Ce paiement est en cours de vérification. Réessayez dans un instant.';
$string['error:currency'] = 'Chargily Pay n\'accepte que le dinar algérien (DZD).';
$string['error:generic'] = '{$a}';
$string['error:nomethod'] = 'Choisissez au moins un moyen de paiement : EDAHABIA ou CIB.';
$string['error:owner'] = 'Le paiement est passé, mais {$a} n\'a pas pu l\'enregistrer. Contactez l\'administrateur du site.';
$string['error:secretkey'] = 'Saisissez une clé secrète Chargily. Les clés de test commencent par test_sk_, les clés de production par live_sk_.';
$string['failed'] = 'Le paiement n\'a pas abouti. Rien n\'a été débité ; vous pouvez réessayer.';
$string['gatewaydescription'] = 'Cartes EDAHABIA et CIB en dinars algériens, via Chargily Pay.';
$string['gatewayname'] = 'Carte EDAHABIA ou CIB (Chargily Pay)';
$string['method:cib'] = 'CIB';
$string['method:cib_help'] = 'Carte bancaire (CIB)';
$string['method:edahabia'] = 'EDAHABIA';
$string['method:edahabia_help'] = 'Carte d\'Algérie Poste';
$string['methods'] = 'Moyens de paiement';
$string['paid'] = 'Paiement reçu. Merci.';
$string['paywith'] = 'Payer par carte';
$string['pluginname'] = 'Chargily Pay';
$string['pluginname_desc'] = 'Cartes EDAHABIA et CIB en dinars algériens, via Chargily Pay.';
$string['privacy:metadata:chargily'] = 'Pour encaisser un paiement, le montant, le moyen de paiement, une description de l\'achat et une référence sont envoyés à Chargily Pay. Le payeur saisit sa carte sur la page de Chargily, pas dans Moodle.';
$string['privacy:metadata:chargily:amount'] = 'Le montant à payer.';
$string['privacy:metadata:chargily:description'] = 'Ce qui est payé.';
$string['privacy:metadata:chargily:metadata'] = 'Une référence vers ce paiement dans Moodle.';
$string['privacy:metadata:paygw_chargily'] = 'Les paiements Chargily lancés sur ce site.';
$string['privacy:metadata:paygw_chargily:amount'] = 'Le montant débité, en dinars.';
$string['privacy:metadata:paygw_chargily:checkoutid'] = 'L\'identifiant du paiement chez Chargily.';
$string['privacy:metadata:paygw_chargily:method'] = 'Le type de carte utilisé : EDAHABIA ou CIB.';
$string['privacy:metadata:paygw_chargily:status'] = 'Si le paiement est en attente, payé, échoué ou annulé.';
$string['privacy:metadata:paygw_chargily:timecreated'] = 'La date de début du paiement.';
$string['privacy:metadata:paygw_chargily:userid'] = 'La personne qui paie.';
$string['redirectnote'] = 'Vous saisissez votre carte sur la page sécurisée de Chargily, puis vous revenez ici.';
$string['secretkey'] = 'Clé secrète';
$string['secretkey_help'] = 'Dans l\'espace développeurs de votre tableau de bord Chargily Pay. Une clé de test (test_sk_...) utilise le mode test de Chargily, sans mouvement d\'argent ; une clé de production (live_sk_...) encaisse de vrais paiements.';
$string['waiting'] = 'Chargily n\'a pas encore confirmé ce paiement. Cela prend généralement quelques secondes.';
$string['webhookurl'] = 'URL du webhook';
$string['webhookurl_help'] = 'Cette adresse est transmise à Chargily avec chaque paiement ; rien à configurer dans le tableau de bord Chargily.';
