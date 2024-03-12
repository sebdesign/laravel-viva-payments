# Changelog

All notable changes to `sebdesign/laravel-viva-payments` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.0.9] - 2024-03-12

### Fixes

-   Laravel 11.x Compatibility ([#46](https://github.com/sebdesign/laravel-viva-payments/pull/46))

## [6.0.8] - 2024-02-26

### Fixes

-   Fix composer dependencies

## [6.0.7] - 2024-02-26

### Added

-   Add `ApplicationIdentifierTerminal` to `TransactionPaymentCreated`

## [6.0.6] - 2023-12-04

### Added

-   Adds bankId and switching params to Transaction. ([#42](https://github.com/sebdesign/laravel-viva-payments/pull/42))
-   Add `ExternalTransactionId` to `TransactionPaymentCreated`

## [6.0.5] - 2023-09-27

### Added

-   Fix previous commit

## [6.0.4] - 2023-09-27

### Added

-   Add `ServiceId` to `RecurringTransaction` ([#39](https://github.com/sebdesign/laravel-viva-payments/issues/39))

## [6.0.3] - 2023-07-14

### Added

-   Add `ServiceId` to `TransactionPaymentCreated`

## [6.0.2] - 2023-07-04

### Added

-   Add `MerchantCategoryCode` to `TransactionPaymentCreated`

## [6.0.1] - 2023-04-28

### Fixed

-   Make `CardUniqueReference` nullable on webhook events

## [6.0.0] - 2023-04-01

### Fixed

-   Fix webhook events

## [6.0.0-beta.3] - 2023-03-17

### Added

-   Add missing parameters to `TransactionPaymentCreated`

## [6.0.0-beta.2] - 2023-02-27

### Added

-   Add `BinId` to `TransactionPaymentCreated`

## [6.0.0-beta.1] - 2023-02-02

### Added

-   Laravel 10.x Compatibility ([#30](https://github.com/sebdesign/laravel-viva-payments/issues/30))

## [6.0.0-alpha.6] - 2022-12-30

### Fixed

-   Make `cardUniqueReference` nullable on transaction response ([#29](https://github.com/sebdesign/laravel-viva-payments/issues/29))

## [6.0.0-alpha.5] - 2022-11-30

### Fixed

-   Add `TransactionTypeId` to recurring transaction response

## [6.0.0-alpha.4] - 2022-11-29

### Fixed

-   Make `merchantTrns` nullable on transaction response ([#28](https://github.com/sebdesign/laravel-viva-payments/issues/28))

## [6.0.0-alpha.3] - 2022-11-18

### Added

-   Improve exceptions
-   Create recurring transactions for ISV

## [6.0.0-alpha.2] - 2022-11-10

-   Extract service objects in subdirectory
-   Rename `SmartCheckout` facade to `Viva`
-   Create payment orders for ISV
-   Retrieve transaction by id for ISV

## [6.0.0-alpha.1] - 2022-11-08

### Added

-   Implement Smart Checkout
-   Create card tokens
-   Add support for PHP 8.2

### Changes

-   Dispatch events instead of extending controller for handling webhooks

### Removed

-   Remove Simple Checkout
-   Remove Native Checkout
-   Remove Redirect Checkout
-   Remove deprecated APIs
-   Drop support for PHP 8.0 and below
-   Drop support for Laravel 8 and below

## [5.2.0] - 2022-01-13

-   Add support for Laravel 9

## [5.1.6] - 2021-11-27

-   Add support for PHP 8.0 and 8.1

## [5.1.5] - 2021-10-17

-   Fix relative URI exception from guzzle

## [5.1.4] - 2021-05-28

-   Use POST instead of GET method in capturePreAuthTransaction API [#24](https://github.com/sebdesign/laravel-viva-payments/pull/24)

## [5.1.3] - 2021-02-19

-   Update to TLS v1.2

## [5.1.2] - 2020-12-09

-   Switch to GitHub Actions

## [5.1.1] - 2020-09-29

-   Fix NativeCheckout::chargeTokenUsingCardToken [#18](https://github.com/sebdesign/laravel-viva-payments/issues/18)

## [5.1.0] - 2020-09-07

-   Add support for Laravel 8

## [5.0.0] - 2020-09-01

-   Add OAuth authentication
-   Add Simple Checkout
-   Add Native Checkout v2
-   Remove Native Checkout v1
-   Remove Mobile Checkout

## [4.4.0] - 2020-08-29

-   Allow additional options for the Guzzle client

## [4.3.1] - 2020-07-15

-   Remove extraneous single quote ([#17](https://github.com/sebdesign/laravel-viva-payments/pull/17)) (thanks [@adrianblynch](https://github.com/adrianblynch))

## [4.3.0] - 2020-07-08

-   Allow Guzzle 7

## [4.2.0] - 2020-05-05

-   Allow recurring payments when creating orders
-   Fix documentation links

## [4.1.1] - 2020-03-21

-   Change `orderCode` parameter to `s` after redirect in documentation

## [4.1.0] - 2020-03-03

-   Add support for Laravel 7

## [4.0.0] - 2019-09-02

-   Add support for Laravel 6.0
-   Drop support for Laravel <5.5 and PHP <7.1

## [3.2.0] - 2019-02-15

-   Add support for Laravel 5.8

## [3.1.0] - 2018-07-31

-   Add support for Laravel 5.7

## [3.0.1] - 2018-06-01

-   Use TLSv1 cipher list if cURL doesn't use NSS

## [3.0.0] - 2017-09-14

-   Add support for Laravel 5.5

## [2.0.1] - 2016-11-02

-   Use https everywhere

## [2.0.0] - 2016-04-26

-   Use Guzzle 6

## [1.0.0] - 2016-04-29

-   Initial release
