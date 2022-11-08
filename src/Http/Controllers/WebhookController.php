<?php

namespace Sebdesign\VivaPayments\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sebdesign\VivaPayments\Events\TransactionFailed;
use Sebdesign\VivaPayments\Events\TransactionPaymentCreated;
use Sebdesign\VivaPayments\Events\WebhookEvent;
use Sebdesign\VivaPayments\Webhook;

class WebhookController extends Controller
{
    /**
     * Verify a webhook.
     *
     * @see https://developer.vivawallet.com/webhooks-for-payments/#generate-a-webhook-verification-key
     */
    public function verify(Webhook $webhook): JsonResponse
    {
        return response()->json($webhook->getVerificationKey());
    }

    /**
     * Handle requests from Viva Wallet.
     *
     * @see https://developer.vivawallet.com/webhooks-for-payments/#handle-requests-from-viva-wallet
     */
    public function handle(Request $request): JsonResponse
    {
        /** @phpstan-ignore-next-line */
        $event = WebhookEvent::create($request->json()->all());

        event($event);

        match ($event->EventData::class) {
            TransactionPaymentCreated::class => event($event->EventData),
            TransactionFailed::class => event($event->EventData),
            default => null,
        };

        return response()->json();
    }
}
