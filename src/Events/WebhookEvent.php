<?php

namespace Sebdesign\VivaPayments\Events;

use Sebdesign\VivaPayments\Enums\WebhookEventType;

/**
 * @template TEventData of object
 */
class WebhookEvent
{
    public function __construct(
        public readonly string $Url,
        /** @var TEventData */
        public readonly object $EventData,
        public readonly string $Created,
        public readonly string $CorrelationId,
        public readonly WebhookEventType $EventTypeId,
        public readonly ?string $Delay,
        public readonly string $MessageId,
        public readonly string $RecipientId,
        public readonly int $MessageTypeId,
    ) {
    }

    /**
     * @phpstan-param  WebhookEventArray  $attributes
     * @phpstan-return self<TEventData>
     */
    public static function create(array $attributes): self
    {
        $eventType = WebhookEventType::from($attributes['EventTypeId']);

        $eventData = match ($eventType) {
            /** @phpstan-ignore-next-line */
            WebhookEventType::TransactionPaymentCreated => TransactionPaymentCreated::create($attributes['EventData']),
            /** @phpstan-ignore-next-line */
            WebhookEventType::TransactionFailed => TransactionFailed::create($attributes['EventData']),
            default => (object) $attributes['EventData'],
        };

        /** @phpstan-ignore-next-line */
        return new self(...[
            ...$attributes,
            'EventTypeId' => $eventType,
            'EventData' => $eventData,
        ]);
    }
}
