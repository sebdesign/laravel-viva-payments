# Viva Payments for Laravel

[![VivaPayments logo](https://www.vivawallet.com/assets/vw-logo.svg "Viva Wallet logo")](https://www.vivawallet.com/)

This package provides an interface for the Viva Wallet Payment API. It handles the **Smart Checkout** integration, the **ISV Payment API**, and **Webhooks**.

Check out the official Viva Wallet Developer Portal for detailed instructions on the APIs and more: https://developer.vivawallet.com

**Note:** This project is not a official package, and I'm not affiliated with Viva Payments in any way.

## Setup

#### Installation

Install the package through Composer.

This package requires PHP 8.1 and Laravel 9.0 or higher, and uses Guzzle 7.0 to make API calls. Use the appropriate version according to your dependencies.

| Viva Payments for Laravel | Guzzle     | Laravel |
| ------------------------- | ---------- | ------- |
| ^6.0                      | ^7.0       | ^9.0    |
| ^5.2                      | ^7.0       | ^9.0    |
| ^5.1                      | ^7.0       | ^8.0    |
| ^5.0                      | ^6.0\|^7.0 | ^7.0    |
| ^4.3                      | ^6.0\|^7.0 | ^7.0    |
| ~4.1                      | ~6.0       | ~7.0    |
| ~4.0                      | ~6.0       | ~6.0    |
| ~3.0                      | ~6.0       | ~5.5    |
| ~2.0                      | ~6.0       | ~5.0    |
| ~1.0                      | ~5.0       | ~5.0    |

```
composer require threls/laravel-viva-payments
```

#### Service Provider

The package will automatically register its service provider.

#### Configuration

Add the following array in your `config/services.php`.

```php
'viva' => [
    'api_key' => env('VIVA_API_KEY'),
    'merchant_id' => env('VIVA_MERCHANT_ID'),
    'environment' => env('VIVA_ENVIRONMENT', 'production'),
    'client_id' => env('VIVA_CLIENT_ID'),
    'client_secret' => env('VIVA_CLIENT_SECRET'),
    'isv_partner_id' => env('VIVA_ISV_PARTNER_ID'),
    'isv_partner_api_key' => env('VIVA_ISV_PARTNER_API_KEY'),
],
```

The `api_key` and `merchant_id` can be found in the _Settings > API Access_ section of your profile.

The `client_id` and `client_secret` are needed for the _Smart Checkout_. You can generate the _Smart Checkout Credentials_ in the _Settings > API Access_ section of your profile.

The `isv_partner_id` and `isv_partner_api_key` are required for using the **ISV Payment API** with Basic authentication.

> Read more about API authentication on the Developer Portal: https://developer.vivawallet.com/getting-started/find-your-account-credentials/client-smart-checkout-credentials/

The `environment` can be either `production` or `demo`.

## Smart Checkout

> Read more about the Smart Checkout process on the Developer portal: https://developer.vivawallet.com/smart-checkout/

The `\Sebdesign\VivaPayments\Facades\Viva` facade provides all the methods needed to interact with the Smart Checkout integration.

The following guide will walk you through the necessary steps:

#### Create the payment order

The amount requested in cents is required. All the other parameters are optional. Check out the [request body schema](https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Payments/paths/~1checkout~1v2~1orders/post).

```php
use Sebdesign\VivaPayments\Facades\Viva;

$orderCode = Viva::orders()->create(
    order: new CreatePaymentOrder(amount: 1000),
);
```

#### Redirect to Smart Checkout

```php
use Sebdesign\VivaPayments\Facades\Viva;

$redirectUrl = Viva::orders()->redirectUrl(
    ref: $orderCode,
    color: '0000ff',
    paymentMethod: 23,
);

return redirect()->away(path: $redirectUrl);
```

#### Verify payment

```php
use Sebdesign\VivaPayments\Facades\Viva;

$response = Viva::transactions()->retrieve(transactionId: request('t'));
```

### Full example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Sebdesign\VivaPayments\Enums\TransactionStatus;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\VivaException;

class CheckoutController extends Controller
{
    /**
     * Create a payment order and redirect to the checkout page.
     */
    public function checkout(): RedirectResponse
    {
        try {
            $orderCode = Viva::orders()->create(new CreatePaymentOrder(
                amount: 1000,
                customerTrns: 'Short description of purchased items/services to display to your customer',
                customer: new Customer(
                    email: 'johdoe@vivawallet.com',
                    fullName: 'John Doe',
                    countryCode: 'GB',
                    requestLang: 'en-GB',
                ),
            ));
        } catch (VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        $redirectUrl = Viva::orders()->redirectUrl(
            ref: $orderCode,
            color: '0000ff',
            paymentMethod: 23,
        );

        return redirect()->away($redirectUrl);
    }

    /**
     * Redirect from the checkout page and get the order details from the API.
     */
    public function confirm(Request $request): RedirectResponse
    {
        try {
            $transaction = Viva::transactions()->retrieve($request->input('t'));
        } catch (VivaException $e) {
            report($e);

            return back()->withErrors($e->getMessage());
        }

        $status = match ($transaction->statusId) {
            case TransactionStatus::PaymentPending: 'The order is pending.',
            case TransactionStatus::PaymentSuccessful: 'The order is paid.',
            case TransactionStatus::Error: 'The order was not paid.',
        }

        return view('order/success', compact('status'));
    }
}
```

## Handling Webhooks

Viva Payments supports Webhooks, and this package offers a controller which verifies and handles incoming notification events.

> Read more about the Webhooks on the Developer Portal: https://developer.vivawallet.com/webhooks-for-payments/

### Define the route

In your `routes/web.php` define the following route for each webhook you have in your profile, replacing the URI(s) and your controller(s) accordingly.

```php
Route::get('viva/webhooks', [WebhookController::class, 'verify']);
Route::post('viva/webhooks', [WebhookController::class, 'handle']);
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
     * @var array<int, string>
     */
    protected $except = ['viva/webhooks'];
}
```

### Handle webhook events

To handle any request from Viva Wallet, you may listen to the `WebhookEvent`.
According to the `EventTypeId`, you can handle any event.

```php
use Sebdesign\VivaPayments\Enums\WebhookEventType;
use Sebdesign\VivaPayments\Events\WebhookEvent;

class EventServiceProvider
{
    protected $listen = [
        WebhookEvent::class => [WebhookEventListener::class],
    ];
}

class WebhookEventListener
{
    public function handle(WebhookEvent $event): void
    {
        match ($event->EventTypeId) {
            WebhookEventType::TransactionPaymentCreated => ...,
            WebhookEventType::TransactionFailed => ...,
            WebhookEventType::TransactionReversalCreated => ...,
            default => ...,
        };
    }
}
```

The `EventData` property contains an object with the actual notification.
The class of the object depends on the notification type:

| Event                       | Class                       |
| --------------------------- | --------------------------- |
| Transaction Payment Created | `TransactionPaymentCreated` |
| Transaction Failed          | `TransactionFailed`         |
| All other events            | `stdClass`                  |

In addition, the `TransactionPaymentCreated` and `TransactionFailed` events are dispatched. You can listen to these specific events instead of listening to the `WebhookEvent`.

```php
use Sebdesign\VivaPayments\Enums\WebhookEventType;
use Sebdesign\VivaPayments\Events\TransactionFailed;
use Sebdesign\VivaPayments\Events\TransactionPaymentCreated;

class EventServiceProvider
{
    protected $listen = [
        TransactionPaymentCreated::class => [
            ConfirmOrder::class,
        ],
        TransactionFailed::class => [
            CancelOrder::class,
        ],
    ];
}

class ConfirmOrder
{
    public function handle(TransactionPaymentCreated $event): void
    {
        //
    }
}

class CancelOrder
{
    public function handle(TransactionFailed $event): void
    {
        //
    }
}
```

## Payment API reference

All methods accept a `$guzzleOptions` array argument as their last parameter. This argument is entirely optional, and it allows you to specify additional request options to the `Guzzle` client.

### Orders

#### Create a payment order

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/get

```php
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;

$orderCode = Viva::orders()->create(
    order: new CreatePaymentOrder(
        amount: 1000,
        customerTrns: 'Short description of purchased items/services to display to your customer',
        customer: new Customer(
            email: 'johdoe@vivawallet.com',
            fullName: 'John Doe',
            phone: '+30999999999',
            countryCode: 'GB',
            requestLang: 'en-GB',
        ),
        paymentTimeout: 300,
        preauth: false,
        allowRecurring: false,
        maxInstallments: 12,
        paymentNotification: true,
        tipAmount: 100,
        disableExactAmount: false,
        disableCash: true,
        disableWallet: true,
        sourceCode: '1234',
        merchantTrns: 'Short description of items/services purchased by customer',
        tags: [
            'tags for grouping and filtering the transactions',
            'this tag can be searched on VivaWallet sales dashboard',
            'Sample tag 1',
            'Sample tag 2',
            'Another string',
        ],
        cardTokens: ['ct_5d0a4e3a7e04469f82da228ca98fd661'],
    ),
    guzzleOptions: [],
);
```

##### Get the redirect URL

> See: https://developer.vivawallet.com/smart-checkout/smart-checkout-integration/#step-2-redirect-the-customer-to-smart-checkout-to-pay-the-payment-order

```php
use Sebdesign\VivaPayments\Facades\Viva;

$url = Viva::orders()->redirectUrl(
    ref: $orderCode,
    color: '0000ff',
    paymentMethod: 23,
);
```

### Transactions

#### Retrieve a transaction

> See: https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions/paths/~1checkout~1v2~1transactions~1{transactionId}/get

```php
use Sebdesign\VivaPayments\Facades\Viva;

$transaction = Viva::transactions()->retrieve(
    transactionId: 'c90d4902-6245-449f-b2b0-51d99cd09cfe',
    guzzleOptions: [],
);
```

#### Create a recurring transaction

> See: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/post

```php
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;

$response = Viva::transactions()->createRecurring(
    transactionId: '252b950e-27f2-4300-ada1-4dedd7c17904',
    transaction: new CreateRecurringTransaction(
        amount: 100,
        installments: 1,
        customerTrns: 'A description of products / services that is displayed to the customer',
        merchantTrns: 'Your merchant reference',
        sourceCode: '6054',
        tipAmount: 0,
    ),
    guzzleOptions: [],
);
```

### OAuth

#### Request access token

> See: https://developer.vivawallet.com/authentication/#step-2-request-access-token

You don't need to call this method, because the client requests the access token automatically when needed.
However, you can specify the client credentials at runtime if you want.

```php
use Sebdesign\VivaPayments\Facades\Viva;

Viva::withOAuthCredentials(
    clientId: 'client_id',
    clientSecret: 'client_secret',
);
```

If you need to request access tokens manually, you can use the `requestToken` method.
This method returns the token as an `AccessToken` object.

```php
use Sebdesign\VivaPayments\Facades\Viva;

// Using `client_id` and `client_secret` from `config/services.php`:
$token = Viva::oauth()->requestToken();

// Using custom client credentials
$token = Viva::oauth()->requestToken(
    clientId: 'client_id',
    clientSecret: 'client_secret',
    guzzleOptions: [],
);
```

### Use an existing access token

If you are storing the token somewhere, e.g. in your database or in the cache, you can set the access token string on the client to be used as a Bearer token.

```php
use Sebdesign\VivaPayments\Facades\Viva;

Viva::withToken(token: 'eyJhbGciOiJSUzI1...');
```

### Cards

#### Create card token

> See: https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions/paths/~1acquiring~1v1~1cards~1tokens/post

```php
use Sebdesign\VivaPayments\Facades\Viva;

$cardToken = Viva::cards()->createToken(
    transactionId: '6cffe5bf-909c-4d69-b6dc-2bef1a6202f7',
    guzzleOptions: [],
);
```

### Webhooks

##### Get an authorization code

> See: https://developer.vivawallet.com/webhooks-for-payments/#generate-a-webhook-verification-key

```php
use Sebdesign\VivaPayments\Facades\Viva;

$key = Viva::webhooks()->getVerificationKey(
    guzzleOptions: [],
);
```

## ISV Payment API Reference

The ISV Payment API methods are available through the `Viva::isv()` service.

### Orders

#### Create a payment order

> See: https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Payments/paths/~1checkout~1v2~1isv~1orders/post

```php
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;

$orderCode = Viva::isv()->orders()->create(
    order: new CreatePaymentOrder(
        amount: 1000,
        customerTrns: 'Short description of purchased items/services to display to your customer',
        customer: new Customer(
            email: 'johdoe@vivawallet.com',
            fullName: 'John Doe',
            phone: '+30999999999',
            countryCode: 'GB',
            requestLang: 'en-GB',
        ),
        paymentTimeout: 300,
        preauth: false,
        allowRecurring: false,
        maxInstallments: 12,
        paymentNotification: true,
        tipAmount: 100,
        disableExactAmount: false,
        disableCash: true,
        disableWallet: true,
        sourceCode: '1234',
        merchantTrns: 'Short description of items/services purchased by customer',
        tags: [
            'tags for grouping and filtering the transactions',
            'this tag can be searched on VivaWallet sales dashboard',
            'Sample tag 1',
            'Sample tag 2',
            'Another string',
        ],
        isvAmount: 10,
        resellerSourceCode: '2345',
    ),
    guzzleOptions: [],
);
```

### Transactions

#### Retrieve a transaction

> See: https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Retrieve-Transactions/paths/~1checkout~1v2~1isv~1transactions~1{transactionId}?merchantId={merchantId}/get

```php
use Sebdesign\VivaPayments\Facades\Viva;

$transaction = Viva::isv()->transactions()->retrieve(
    transactionId: 'c90d4902-6245-449f-b2b0-51d99cd09cfe',
    guzzleOptions: [],
);
```

#### Create a recurring transaction

> See: https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Recurring-Payments/paths/~1api~1transactions~1{id}/post

```php
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;

Viva::withBasicAuthCredentials(
    config('services.viva.isv_partner_id').':'.config('services.viva.merchant_id'),
    config('services.viva.isv_partner_api_key'),
);

$transaction = Viva::isv()->transactions()->createRecurring(
    transactionId: 'c90d4902-6245-449f-b2b0-51d99cd09cfe',
    transaction: new CreateRecurringTransaction(
        amount: 100,
        isvAmount: 1,
        customerTrns: 'A description of products / services that is displayed to the customer',
        merchantTrns: 'Your merchant reference',
        sourceCode: '4929333',
        resellerSourceCode: '1565',
    ),
    guzzleOptions: [],
);
```

## Exceptions

When the VivaPayments API returns an error, a `Sebdesign\VivaPayments\VivaException` is thrown.

For any other HTTP error a `GuzzleHttp\Exception\ClientException` is thrown.

## Tests

Unit tests are triggered by running `phpunit --group unit`.

To run functional tests you have to include a `.env` file in the root folder, containing the credentials (`VIVA_API_KEY`, `VIVA_MERCHANT_ID`, `VIVA_CLIENT_ID`, `VIVA_CLIENT_SECRET`), in order to hit the VivaPayments demo API. Then run `phpunit --group functional` to trigger the tests.
