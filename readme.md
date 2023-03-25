# Viva Payments for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sebdesign/laravel-viva-payments.svg?style=flat-square)](https://packagist.org/packages/sebdesign/laravel-viva-payments)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/workflow/status/sebdesign/laravel-viva-payments/Tests/master?style=flat-square)](https://github.com/sebdesign/laravel-viva-payments/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/sebdesign/laravel-viva-payments.svg?style=flat-square)](https://scrutinizer-ci.com/g/sebdesign/laravel-viva-payments)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/sebdesign/laravel-viva-payments.svg?style=flat-square)](https://scrutinizer-ci.com/g/sebdesign/laravel-viva-payments)

[![VivaPayments logo](https://camo.githubusercontent.com/7f0b41d204f5c27c416a83fa0bc8d1d1e45cf495/68747470733a2f2f7777772e766976617061796d656e74732e636f6d2f436f6e74656e742f696d672f6c6f676f2e737667 "VivaPayments logo")](https://www.vivapayments.com/)

This package provides an interface for the Viva Wallet API. It handles the **Redirect Checkout**, **Native Checkout v2**, and **Simple Checkout** payment methods, as well as **Webhooks**.

Check out the official Viva Wallet Developer Portal for detailed instructions on the APIs and more: https://developer.vivawallet.com

**Note:** This project is not a certified package, and I'm not affiliated with Viva Payments in any way.

## Table of Contents

-   [Setup](#setup)
    -   [Installation](#installation)
    -   [Service Provider](#service-provider)
    -   [Configuration](#configuration)
-   [Simple Checkout](#simple-checkout)
    -   [Display the button](#display-the-button)
    -   [Make the charge](#make-the-charge)
-   [Native Checkout v2](#native-checkout-v2)
    -   [Build a custom payment form](#build-a-custom-payment-form)
    -   [Make the actual charge](#make-the-actual-charge)
-   [Redirect Checkout](#redirect-checkout)
    -   [Create a payment order](#create-a-payment-order)
    -   [Redirect to the Viva checkout page](#redirect-to-the-viva-checkout-page)
    -   [Confirm the transaction](#confirm-the-transaction)
    -   [Full example](#full-example)
-   [Handling Webhooks](#handling-webhooks)
    -   [Extend the controller](#extend-the-controller)
    -   [Define the route](#define-the-route)
    -   [Exclude from CSRF protection](exclude-from-csrf-protection)
-   [API Methods](#api-methods)
    -   [Orders](#orders)
        -   [Create a payment order](#create-a-payment-order)
        -   [Get an order](#get-an-order)
        -   [Update an order](#update-an-order)
        -   [Cancel an order](#cancel-an-order)
    -   [Transactions](#transactions)
        -   [Create a new transaction](#create-a-new-transaction)
        -   [Create a recurring transaction](#create-a-recurring-transaction)
        -   [Get transactions](#get-transactions)
        -   [Cancel a card payment / Make a refund](#cancel-a-card-payment-make-a-refund)
    -   [OAuth](#oauth)
        -   [Request access token](#request-access-token)
        -   [Use an existing access token](#use-an-existing-access-token)
    -   [Native Checkout](#native-checkout)
        -   [Generate a charge token using card details](#generate-a-charge-token-using-card-details)
        -   [Generate a card token using a charge token](#generate-a-card-token-using-a-charge-token)
        -   [Generate one-time charge token using card token](#generate-one-time-charge-token-using-card-token)
        -   [Create transaction](#create-transaction)
        -   [Capture a pre-auth](#capture-a-pre-auth)
        -   [Check for installments](#check-for-installments)
    -   [Payment Sources](#payment-sources)
        -   [Add a payment source](#add-a-payment-source)
    -   [Webhooks](#webhooks)
        -   [Get an authorization code](#get-an-authorization-code)
-   [Exceptions](#exceptions)
-   [Tests](#tests)

## Setup

#### Installation

Install the package through Composer.

This package requires Laravel 5.0 or higher, and uses Guzzle to make API calls. Use the appropriate version according to your dependencies.

| Viva Payments for Laravel | Guzzle     | Laravel |
| ------------------------- | ---------- | ------- |
| ~1.0                      | ~5.0       | ~5.0    |
| ~2.0                      | ~6.0       | ~5.0    |
| ~3.0                      | ~6.0       | ~5.5    |
| ~4.0                      | ~6.0       | ~6.0    |
| ~4.1                      | ~6.0       | ~7.0    |
| ^4.3                      | ^6.0\|^7.0 | ^7.0    |
| ^5.0                      | ^6.0\|^7.0 | ^7.0    |
| ^5.1                      | ^7.0       | ^8.0    |
| ^5.2                      | ^7.0       | ^9.0    |
| ^5.3                      | ^7.0       | ^10.0   |

```
composer require sebdesign/laravel-viva-payments
```

#### Service Provider

This package supports auto-discovery for Laravel 5.5.

If you are using an older version, add the following service provider in your `config/app.php`.

```php
'providers' => [
    Sebdesign\VivaPayments\VivaPaymentsServiceProvider::class,
],
```

#### Configuration

Add the following array in your `config/services.php`.

```php
'viva' => [
    'api_key' => env('VIVA_API_KEY'),
    'merchant_id' => env('VIVA_MERCHANT_ID'),
    'public_key' => env('VIVA_PUBLIC_KEY'),
    'environment' => env('VIVA_ENVIRONMENT', 'production'),
    'client_id' => env('VIVA_CLIENT_ID'),
    'client_secret' => env('VIVA_CLIENT_SECRET'),
],
```

The `api_key`, `merchant_id`, and `public_key` can be found in the _Settings > API Access_ section of your profile.

The `public_key` is only needed for the _Simple Checkout_.

The `client_id` and `client_secret` are needed for the _Native Checkout_. You can generate the _Native Checkout v2 credentials_ in the _Settings > API Access_ section of your profile.

> Read more about API authentication on the Developer Portal: https://developer.vivawallet.com/authentication

The `environment` can be either `production` or `demo`.

> To simulate a successful payment on the demo environment, use the card number 4111 1111 1111 1111 with any valid date and 111 for the CVV2.

## Simple Checkout

> Read more about the Simple Checkout process on the Developer portal: https://developer.vivawallet.com/online-checkouts/simple-checkout

Follow these steps to complete setup of Simple Checkout.

### Display the button

First, create a route that displays the payment button.

In your `routes/web.php`:

```php
Route::get('checkout', 'CheckoutController@create');
```

In your `app/Http/Controllers/CheckoutController.php`:

```php
<?php

namespace App\Http\Controllers;

class CheckoutContrroller extends Controller
{
    /**
     * Display the payment button.
     *
     * @param  \Sebdesign\VivaPayments\Client $client
     * @return \Illuminate\Http\Response
     */
    public function create(Client $client)
    {
        return view('checkout', [
            'publicKey' => config('services.viva.public_key'),
            'baseUrl' => $client->getUrl(),
        ]);
    }
}
```

In your `resources/views/checkout.blade.php`:

```php
<html>
    <head>
        <title>Simple Checkout</title>
    </head>
    <body>
      <form id="myform" action="{{ url('checkout') }}" method="post">
        <button type="button"
          data-vp-publickey="{{ $publicKey }}"
          data-vp-baseurl="{{ $baseUrl }}"
          data-vp-lang="en"
          data-vp-amount="1000"
          data-vp-description="My product">
        </button>
      </form>

      <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
      <script src="https://demo.vivapayments.com/web/checkout/js"></script>
    </body>
</html>
```

### Make the charge

Then create a route to submit the `vivaWalletToken` from your form's `action` to the `CheckoutController`.

In your `routes/web.php`:

```php
Route::post('checkout', 'CheckoutController@store');
```

In your `app/Http/Controllers/CheckoutController.php`:

```php
<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Sebdesign\VivaPayments\Transaction;
use Sebdesign\VivaPayments\VivaException;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     *
     * @param  \Illuminate\Http\Request            $request
     * @param  \Sebdesign\VivaPayments\Transaction $transactions
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Transaction $transactions)
    {
        try {
            $transaction = $transactions->create([
                'PaymentToken' => $request->input('vivaWalletToken');
            ]);
        } catch (RequestException | VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        return redirect('order/success');
    }
```

## Native Checkout v2

Follow the steps described in the Developer Portal: https://developer.vivawallet.com/online-checkouts/native-checkout-v2

### Build a custom payment form

First, create a route to the controller that returns the view with the payment form.

In your `routes/web.php`:

```php
Route::get('checkout', 'CheckoutController@create');
```

In your `app/Http/Controllers/CheckoutController.php`:

```php
<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\OAuth;
use Sebdesign\VivaPayments\VivaException;

class CheckoutController extends Controller
{
    /**
     * Display the payment form.
     *
     * @param  \Sebdesign\VivaPayments\Client $client
     * @param  \Sebdesign\VivaPayments\OAuth  $oauth
     * @return \Illuminate\Http\Response
     */
    public function create(Client $client, OAuth $oauth)
    {
        try {
            $token = $oauth->requestToken();
        } catch (RequestException | VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        return view('checkout', [
            'baseUrl' => $client->getApiUrl(),
            'accessToken' => $token->access_token,
        ]);
    }
}
```

In your `resources/views/checkout.blade.php`:

```php
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Native 3DS test</title>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://demo.vivapayments.com/web/checkout/v2/js"></script>
</head>

<body>
    <form action="{{ url('checkout') }}" method="POST" id="payment-form">
        <div class="form-row">
            <label>
                <span>Cardholder Name</span>
                <input type="text" data-vp="cardholder" size="20" name="txtCardHolder" autocomplete="off"/>
            </label>
        </div>

        <div class="form-row">
            <label>
                <span>Card Number</span>
                <input type="text" data-vp="cardnumber" size="20" name="txtCardNumber" autocomplete="off"/>
            </label>
        </div>

        <div class="form-row">
            <label>
                <span>CVV</span>
                <input type="text" data-vp="cvv" name="txtCVV" size="4" autocomplete="off"/>
            </label>
        </div>

        <div class="form-row">
            <label>
                <span>Expiration (MM/YYYY)</span>
                <input type="text" data-vp="month" size="2" name="txtMonth" autocomplete="off"/>
            </label>
            <span> / </span>
            <input type="text" data-vp="year" size="4" name="txtYear" autocomplete="off"/>
        </div>

        <div class="form-row">
            <label>
                <span>Installments</span>
                <select id="js-installments" name="installments" style="display:none"></select>
            </label>
        </div>

        <input type="hidden" id="charge-token" name="chargeToken"/>
        <input type="button" id="submit" value="Submit Payment" />
    </form>

    <div id="threed-pane" style="height: 450px; width:500px"></div>

    <script>
        $(document).ready(function () {
            VivaPayments.cards.setup({
                baseURL: '{{ $baseUrl }}',
                authToken: '{{ $accessToken }}',
                cardHolderAuthOptions: {
                    cardHolderAuthPlaceholderId: 'threed-pane',
                    cardHolderAuthInitiated: function () {
                        $('#threed-pane').show();
                    },
                    cardHolderAuthFinished: function () {
                        $('#threed-pane').hide();
                    }
                },
                installmentsHandler: function (response) {
                    if (!response.Error) {
                        if (response.MaxInstallments == 0)
                            return;
                        $('#js-installments').show();
                        for (i = 1; i <= response.MaxInstallments; i++) {
                            $('#js-installments').append($("<option>").val(i).text(i));
                        }
                    }
                    else {
                        alert(response.Error);
                    }
                }
            });

            $('#submit').on('click', function (evt) {
                evt.preventDefault();
                VivaPayments.cards.requestToken({
                    amount: 3600
                }).done(function (data) {
                    console.log(data);
                    $('#charge-token').val(data.chargeToken);
                    $('#payment-form').submit();
                });
            });

        });
    </script>
</body>
</html>
```

### Make the actual charge

Then create a route to submit your form with the charge token to the `CheckoutController`.

In your `routes/web.php`:

```php
Route::post('checkout', 'CheckoutController@store');
```

In your `app/Http/Controllers/CheckoutController.php`:

```php
<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Sebdesign\VivaPayments\NativeCheckout;
use Sebdesign\VivaPayments\OAuth;
use Sebdesign\VivaPayments\VivaException;

class CheckoutController extends Controller
{
    /**
     * Create a new transaction with the charge token from the form.
     *
     * @param  \Illuminate\Http\Request               $request
     * @param  \Sebdesign\VivaPayments\OAuth          $oauth
     * @param  \Sebdesign\VivaPayments\NativeCheckout $nativeCheckout
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, OAuth $oauth, NativeCheckout $nativeCheckout)
    {
        try {
            $oauth->requestToken();

            $transactionId = $nativeCheckout->createTransaction([
                'amount' => 1000,
                'tipAmount' => 0,
                'preauth' => false,
                'chargeToken' => $request->input('chargeToken'),
                'installments' => $request->input('installments'),
                'merchantTrns' => 'Merchant transaction reference',
                'customerTrns' => 'Description that the customer sees',
                'currencyCode' => 826,
                'customer' => [
                    'email' => 'native@vivawallet.com',
                    'phone' => '442037347770',
                    'fullname' => 'John Smith',
                    'requestLang' => 'en',
                    'countryCode' => 'GB',
            ]);
        } catch (RequestException | VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        return redirect('order/success');
    }
}
```

## Redirect Checkout

Redirect checkout is a simple 3 step process, where you create the Payment Order, redirect the customer to Viva Payments secure environment and then confirm the transaction.

> Read more about the redirect checkout process on the Developer Portal: https://developer.vivawallet.com/online-checkouts/redirect-checkout

The following guide will walk you through the necessary steps:

#### Create the payment order

The first argument is the amount requested in cents. All the parameters in the second argument are optional. Check out the [request body schema](https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders/post).

```php
$order = app(Sebdesign\VivaPayments\Order::class);

$orderCode = $order->create(100, [
    'fullName'      => 'Customer Name',
    'email'         => 'customer@domain.com',
    'sourceCode'    => 'Default',
    'merchantTrns'  => 'Order reference',
    'customerTrns'  => 'Description that the customer sees',
]);
```

#### Redirect to the Viva checkout page

```php
$checkoutUrl = $order->getCheckoutUrl($orderCode);

return redirect($checkoutUrl);
```

#### Confirm the transaction

```php
$order = app(Sebdesign\VivaPayments\Order::class);

$response = $order->get(request('s'));
```

### Full example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\VivaException;

class CheckoutController extends Controller
{
    /**
     * Create a payment order and redirect to the checkout page.
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \Sebdesign\VivaPayments\Order     $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request, Order $order)
    {
        try {
            $orderCode = $order->create(100, [
                'fullName'      => 'Customer Name',
                'email'         => 'customer@domain.com',
                'sourceCode'    => 'Default',
                'merchantTrns'  => 'Order reference',
                'customerTrns'  => 'Description that the customer sees',
            ]);
        } catch (VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        $checkoutUrl = $order->getCheckoutUrl($orderCode);

        return redirect()->away($checkoutUrl);
    }

    /**
     * Redirect from the checkout page and get the order details from the API.
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \Sebdesign\VivaPayments\Order     $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Request $request, Order $order)
    {
        try {
            $response = $order->get($request->get('s'));
        } catch (VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        switch ($response->StateId) {
            case Order::PENDING:
                $state = 'The order is pending.';
                break;
            case Order::EXPIRED:
                $state = 'The order has expired.';
                break;
            case Order::CANCELED:
                $state = 'The order has been canceled.';
                break;
            case Order::PAID:
                $state = 'The order is paid.';
                break;
        }

        return view('order/success', compact('state'));
    }
}
```

## Handling Webhooks

Viva Payments supports Webhooks, and this package offers a controller which can be extended to handle incoming notification events.

> Read more about the Webhooks on the Developer Portal: https://developer.vivawallet.com/api-reference-guide/payment-api/webhooks

### Extend the controller

You can make one controller to handle all the events, or make a controller for each event. Either way, your controllers must extend the `Sebdesign\VivaPayments\WebhookController`. The webhook verification is handled automatically.

For the moment, Viva Payment offers the _Create Transaction_ and _Cancel/Refund Transaction_ events. To handle those events, you controller must extend the `handleCreateTransaction` and a `handleRefundTransaction` methods respectively. For any other event that might be available, extend the `handleEventNotification` method.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sebdesign\VivaPayments\WebhookController as BaseController;

class WebhookController extends BaseController
{
    /**
     * Handle a Create Transaction event notification.
     *
     * @param  \Illuminate\Http\Request $request
     */
    protected function handleCreateTransaction(Request $request)
    {
        $event = $request->EventData;
    }

    /**
     * Handle a Refund Transaction event notification.
     *
     * @param  \Illuminate\Http\Request $request
     */
    protected function handleRefundTransaction(Request $request)
    {
        $event = $request->EventData;
    }

    /**
     * Handle any other type of event notification.
     *
     * @param  \Illuminate\Http\Request $request
     */
    protected function handleEventNotification(Request $request)
    {
        $event = $request->EventData;
    }
}
```

### Define the route

In your `routes/web.php` define the following route for each webhook you have in your profile, replacing the URI(s) and your controller(s) accordingly.

```php
Route::match(['post', 'get'], 'viva/webhooks', 'WebhookController@handle');
```

### Exclude from CSRF protection

Don't forget to add your webhook URI(s) to the `$except` array on your `VerifyCsrfToken` middleware.

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'viva/webhooks',
    ];
}
```

## API Methods

All methods accept a `$guzzleOptions` array argument as their last parameter. This argument is entirely optional, and it allows you to specify additional request options to the `Guzzle` client.

### Orders

##### Create a payment order

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/get

```php
$order = app(Sebdesign\VivaPayments\Order::class);

$orderCode = $order->create(100, $parameters = [], $guzzleOptions = []);
```

##### Get an order

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/get

```php
$response = $order->get(175936509216, $guzzleOptions = []);
```

##### Update an order

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/patch

```php
$order->update(175936509216, ['Amount' => 50], $guzzleOptions = []);
```

##### Cancel an order

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/delete

```php
$response = $order->cancel(175936509216, $guzzleOptions = []);
```

### Transactions

##### Create a new transaction

> See: https://developer.vivawallet.com/online-checkouts/simple-checkout/#step-2-make-the-charge

```php
$transaction = app(Sebdesign\VivaPayments\Transaction::class);

$response = $transaction->create([
    'PaymentToken' => $request->input('vivaWalletToken'),
], $guzzleOptions = []);
```

##### Create a recurring transaction

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/post

```php
$transaction = app(Sebdesign\VivaPayments\Transaction::class);

$response = $transaction->create(
    '252b950e-27f2-4300-ada1-4dedd7c17904',
    1500,
    $parameters = [],
    $guzzleOptions = []
);
```

##### Get transactions

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/get

```php
// By transaction ID
$transactions = $transaction->get('252b950e-27f2-4300-ada1-4dedd7c17904', $guzzleOptions = []);

// By order code
$transactions = $transaction->getByOrder(175936509216, $guzzleOptions = []);

// By order date

// The date can be a string in Y-m-d format,
// or a DateTimeInterface instance like DateTime or Carbon.

$transactions = $transaction->getByDate('2016-03-11', $guzzleOptions = []);

// By clearance date

// The date can be a string in Y-m-d format,
// or a DateTimeInterface instance like DateTime or Carbon.

$transactions = $transaction->getByClearanceDate('2016-03-11', $guzzleOptions = []);

// By source code and date

// The date can be a string in Y-m-d format,
// or a DateTimeInterface instance like DateTime or Carbon.

$transactions = $transaction->getBySourceCode('Default', '2016-03-11', $guzzleOptions = []);
```

##### Cancel a card payment / Make a refund

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/delete

```php
$response = $transaction->cancel(
    '252b950e-27f2-4300-ada1-4dedd7c17904',
    100,
    'username',
    $guzzleOptions = []
);
```

### OAuth

#### Request access token

> See: https://developer.vivawallet.com/authentication/#step-2-request-access-token

These methods return the access token as an **object**.

```php
$oauth = app(Sebdesign\VivaPayments\OAuth::class);

// Using `client_id` and `client_secret` from `config/services.php`:
// The token will be automatically stored on the client and used as a Bearer token.
$token = $oauth->requestToken();

// Using custom credentials
// The token will be automatically stored on the client and used as a Bearer token.
$token = $oauth->requestToken('client_id', 'client_secret', $guzzleOptions = []);

// You can also generate an access token without storing it on the client:
$token = $oauth->token('client_id', 'client_secret', $guzzleOptions = []);
```

### Use an existing access token

```php
$oauth = app(Sebdesign\VivaPayments\OAuth::class);

// If you are storing the token somewhere, e.g. in your database or in the cache, you can set the access token string on the client to be used as a Bearer token.
$oauth->withToken('eyJhbGciOiJSUzI1...');
```

### Native Checkout

> Don't forget to generate or set an access token before using the following methods.

#### Generate a charge token using card details

> See: https://developer.vivawallet.com/api-reference-guide/card-tokenization-api/#step-1-generate-one-time-charge-token-using-card-details

```php
$native = app(Sebdesign\VivaPayments\NativeCheckout::class);

$chargeToken = $native->chargeToken(
    1000,
    'Customer Name',
    '4111 1111 1111 1111',
    111,
    3,
    2016,
    'https://www.example.com',
    $guzzleOptions = []
);
```

#### Generate a card token using a charge token

> See: https://developer.vivawallet.com/api-reference-guide/card-tokenization-api/#step-2-generate-card-token-using-the-charge-token-optional

```php
$nativeCheckout = app(Sebdesign\VivaPayments\NativeCheckout::class);

$cardToken = $nativeCheckout->cardToken(
    'ctok__pEfMPKCt-FHnN0vS3T23Gz1aDk',
    $guzzleOptions = []
);
```

#### Generate one-time charge token using card token

> See: https://developer.vivawallet.com/api-reference-guide/card-tokenization-api/#step-3-generate-one-time-charge-token-using-card-token-optional

```php
$nativeCheckout = app(Sebdesign\VivaPayments\NativeCheckout::class);

$chargeToken = $nativeCheckout->chargeTokenUsingCardToken(
    '2188A74B6BB8DE0D5671886B5385125121CAE870',
    $guzzleOptions = []
);
```

#### Create transaction

> See: https://developer.vivawallet.com/api-reference-guide/native-checkout-v2-api/#create-transaction

```php
$nativeCheckout = app(Sebdesign\VivaPayments\NativeCheckout::class);

$transactionId = $nativeCheckout->createTransaction(
    [
        'amount' => 1000,
        'preauth' => false,
        'sourceCode' => '4693',
        'chargeToken' => '<charge token>',
        'installments' => 1,
        'merchantTrns' => 'Merchant transaction reference',
        'customerTrns' => 'Description that the customer sees',
        'currencyCode' => 826,
        'customer' => [
            'email' => 'native@vivawallet.com',
            'phone' => '442037347770',
            'fullname' => 'John Smith',
            'requestLang' => 'en',
            'countryCode' => 'GB',
        ],
    ],
    $guzzleOptions = []
);
```

#### Capture a pre-auth

> See: https://developer.vivawallet.com/api-reference-guide/native-checkout-v2-api/#capture-a-pre-auth

```php
$nativeCheckout = app(Sebdesign\VivaPayments\NativeCheckout::class);

$transactionId = $nativeCheckout->capturePreAuthTransaction(
    'b1a3067c-321b-4ec6-bc9d-1778aef2a19d',
    300,
    $guzzleOptions = []
);
```

#### Check for installments

> See: https://developer.vivawallet.com/api-reference-guide/native-checkout-v2-api/#check-for-installments

```php
$nativeCheckout = app(Sebdesign\VivaPayments\NativeCheckout::class);

$maxInstallments = $nativeCheckout->installments('4111 1111 1111 1111', $guzzleOptions = []);
```

### Payment Sources

##### Add a payment source

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Sources/paths/~1api~1sources/post

```php
$source = app(Sebdesign\VivaPayments\Source::class);

$source->create(
    'Site 1',
    'site1',
    'https://www.domain.com',
    'order/failure',
    'order/success',
    $guzzleOptions = []
);
```

### Webhooks

##### Get an authorization code

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/webhooks/#webhook-url-verification

```php
$webhook = app(Sebdesign\VivaPayments\Webhook::class);

$key = $webhook->getAuthorizationCode($guzzleOptions = []);
```

## Exceptions

When the VivaPayments API returns an error, a `Sebdesign\VivaPayments\VivaException` is thrown.

For any other HTTP error a `GuzzleHttp\Exception\ClientException` is thrown.

## Tests

Unit tests are triggered by running `phpunit --group unit`.

To run functional tests you have to include a `.env` file in the root folder, containing the credentials (`VIVA_API_KEY`, `VIVA_MERCHANT_ID`, `VIVA_PUBLIC_KEY`), in order to hit the VivaPayments demo API. Then run `phpunit --group functional` to trigger the tests.
