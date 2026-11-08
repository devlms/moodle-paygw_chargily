# Chargily Pay for Moodle (EDAHABIA and CIB)

[![CI](https://github.com/devlms/moodle-paygw_chargily/actions/workflows/ci.yml/badge.svg)](https://github.com/devlms/moodle-paygw_chargily/actions/workflows/ci.yml)
[![Maturity](https://img.shields.io/badge/maturity-alpha-orange)](version.php)

A Moodle payment gateway for [Chargily Pay](https://chargily.com/business/pay): learners in Algeria pay course fees with their EDAHABIA (Algérie Poste) or CIB card, in dinars. Works with any Moodle feature that takes payments, such as enrolment on payment.

It works on its own, and also as a provider of the DevLMS payment hub, which offers local and international gateways at one checkout.

| Feature | Status |
| --- | --- |
| Payer picks EDAHABIA or CIB, then pays on Chargily's secure page | Done |
| Test and live mode, set by the secret key (test_sk_ or live_sk_) | Done |
| Every payment confirmed with Chargily before access is given; webhooks checked with the HMAC signature | Done |
| Each payment delivered once, whether the webhook or the return page arrives first | Done |
| English, French and Arabic | Done |
| Privacy API: checkouts exported and deleted, paid or not | Done |
| Refunds | Not available: Chargily has no refund API. Refund from the Chargily dashboard. |

## Set up

1. Install the plugin in `payment/gateway/chargily` and finish the upgrade.
2. Site administration > Plugins > Payment gateways: enable Chargily Pay.
3. Site administration > Payments > Payment accounts: open an account, then Chargily Pay. Paste the secret key from the Developers Corner of your Chargily dashboard, choose the card types to offer, and enable it.
4. Price the course in DZD (for example with Enrolment on payment) and choose that payment account.

Use a test key first: Chargily's test mode takes payments without moving money.

## Requirements

Moodle 4.5 to 5.3. A Chargily Pay account (Algeria).

## How it works

The payer chooses Chargily in Moodle's payment window, picks EDAHABIA or CIB, and goes to Chargily's checkout. When they come back, or when Chargily's webhook arrives, the plugin reads the checkout from Chargily's API; only a checkout Chargily reports as paid, for the exact amount, enrols the learner. Chargily takes whole dinars, so prices with centimes are rounded up.

## Licence

GPL v3 or later. Built by [DevLMS](https://devlms.com), who build custom Moodle plugins and payment integrations. [Get a quote](https://devlms.com/#quote).
