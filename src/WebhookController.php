<?php

namespace Sebdesign\VivaPayments;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class WebhookController extends Controller
{
    protected $webhook;

    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Handle an incoming request.
     *
     * Handle a GET verification request or a POST notification.
     *
     * @param  Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle(Request $request)
    {
        if ($request->method() == 'GET') {
            return $this->verify();
        }

        return $this->handleTransaction($request);
    }

    /**
     * Handle a POST notification.
     *
     * @param  Illuminate\Http\Request $request
     * @return mixed
     */
    protected function handleTransaction(Request $request)
    {
        switch ($request->get('EventTypeId')) {
            case Webhook::CREATE_TRANSACTION:
                return $this->handleCreateTransaction($request);
            case Webhook::REFUND_TRANSACTION:
                return $this->handleRefundTransaction($request);
            default:
                return $this->handleEventNotification($request);
        }
    }

    /**
     * Handle a Create Transaction event notification.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    abstract protected function handleCreateTransaction(Request $request);

    /**
     * Handle a Refund Transaction event notification.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    abstract protected function handleRefundTransaction(Request $request);

    /**
     * Handle any other type of event notification.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    abstract protected function handleEventNotification(Request $request);

    /**
     * Verify a webhook.
     *
     * @return array
     */
    protected function verify() : array
    {
        return (array) $this->webhook->getAuthorizationCode();
    }
}
