# Changelog

All notable changes to `sebdesign/laravel-viva-payments` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [6.0.0-apha.1] - 2022-11-08

### Added

- Implement Smart Checkout
- Create card tokens
- Add support for PHP 8.2

### Changes
- Dispatch events instead of extending controller for handling webhooks

### Removed

- Remove Simple Checkout
- Remove Native Checkout
- Remove Redirect Checkout
- Remove deprecated APIs
- Drop support for PHP 8.0 and below
- Drop support for Laravel 8 and below

## [5.2.0] - 2022-01-13

- Add support for Laravel 9

## [5.1.6] - 2021-11-27

- Add support for PHP 8.0 and 8.1

## [5.1.5] - 2021-10-17

- Fix relative URI exception from guzzle

## [5.1.4] - 2021-05-28

- Use POST instead of GET method in capturePreAuthTransaction API [#24](https://github.com/sebdesign/laravel-viva-payments/pull/24)

## [5.1.3] - 2021-02-19

- Update to TLS v1.2

## [5.1.2] - 2020-12-09

- Switch to GitHub Actions

## [5.1.1] - 2020-09-29

- Fix NativeCheckout::chargeTokenUsingCardToken [#18](https://github.com/sebdesign/laravel-viva-payments/issues/18)

## [5.1.0] - 2020-09-07

- Add support for Laravel 8

## [5.0.0] - 2020-09-01

- Add OAuth authentication
- Add Simple Checkout
- Add Native Checkout v2
- Remove Native Checkout v1
- Remove Mobile Checkout

## [4.4.0] - 2020-08-29

- Allow additional options for the Guzzle client

## [4.3.1] - 2020-07-15

- Remove extraneous single quote ([#17](https://github.com/sebdesign/laravel-viva-payments/pull/17)) (thanks [@adrianblynch](https://github.com/adrianblynch))

## [4.3.0] - 2020-07-08

- Allow Guzzle 7

## [4.2.0] - 2020-05-05

- Allow recurring payments when creating orders
- Fix documentation links

## [4.1.1] - 2020-03-21

- Change `orderCode` parameter to `s` after redirect in documentation

## [4.1.0] - 2020-03-03

- Add support for Laravel 7

## [4.0.0] - 2019-09-02

- Add support for Laravel 6.0
- Drop support for Laravel <5.5 and PHP <7.1

## [3.2.0] - 2019-02-15

- Add support for Laravel 5.8

## [3.1.0] - 2018-07-31

- Add support for Laravel 5.7

## [3.0.1] - 2018-06-01

- Use TLSv1 cipher list if cURL doesn't use NSS

## [3.0.0] - 2017-09-14

- Add support for Laravel 5.5

## [2.0.1] - 2016-11-02

- Use https everywhere

## [2.0.0] - 2016-04-26

- Use Guzzle 6

## [1.0.0] - 2016-04-29

- Initial release
