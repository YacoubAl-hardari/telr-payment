<?php

namespace yacoubalhaidari\Telr\DTOs\Order;

use yacoubalhaidari\Telr\DTOs\BaseDTO;
use yacoubalhaidari\Telr\Enums\FramedMode;
use yacoubalhaidari\Telr\Exceptions\TelrValidationException;

/**
 * Maps directly to the order.json "create" request body documented on the
 * "Create session" page.
 */
final class CreateOrderDTO extends BaseDTO
{
    /**
     * @param  WebhookUrlDTO[]  $webhooks  Maximum 2 allowed.
     * @param  array<string,string>  $extra  Maximum 7 key/value pairs.
     */
    public function __construct(
        public readonly OrderDetailsDTO $order,
        public readonly ReturnUrlsDTO $return,
        public readonly ?CustomerDTO $customer = null,
        public readonly FramedMode $framed = FramedMode::STANDARD,
        public readonly ?string $panels = null,
        public readonly array $webhooks = [],
        public readonly array $extra = [],
    ) {
        if (count($this->webhooks) > 2) {
            throw new TelrValidationException('A maximum of 2 webhook objects are allowed per order.');
        }

        if (count($this->extra) > 7) {
            throw new TelrValidationException('A maximum of 7 extra fields are allowed per order.');
        }
    }

    public function toArray(string $store, string $authKey): array
    {
        return self::filter([
            'method' => 'create',
            'store' => $store,
            'authkey' => $authKey,
            'framed' => $this->framed->value,
            'order' => $this->order->toArray(),
            'return' => $this->return->toArray(),
            'customer' => $this->customer?->toArray(),
            'panels' => $this->panels,
            'webhooks' => array_map(fn (WebhookUrlDTO $w) => $w->toArray(), $this->webhooks),
            'extra' => $this->extra,
        ]);
    }
}
