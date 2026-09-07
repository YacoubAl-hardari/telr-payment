<?php

namespace yacoubalhaidari\Telr\Services;

use yacoubalhaidari\Telr\Contracts\QuickLinkInterface;
use yacoubalhaidari\Telr\DTOs\QuickLink\CreateQuickLinkDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\QuickLinkDetailsDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\QuickLinkResponseDTO;
use yacoubalhaidari\Telr\Enums\Currency;

/**
 * Wraps POST /gateway/api_quicklink.json for creating QuickLinks,
 * including the "Card Change API" convenience flow documented on the
 * "Card Change in Repeat Billing Agreement" page.
 */
class QuickLinkService implements QuickLinkInterface
{
    protected const ENDPOINT = '/gateway/api_quicklink.json';

    public function __construct(
        protected HttpClient $client,
        protected string $store,
        protected string $authKey,
    ) {
    }

    public function create(CreateQuickLinkDTO $link): QuickLinkResponseDTO
    {
        $payload = $link->toArray($this->store, $this->authKey);
        $response = $this->client->postJson(self::ENDPOINT, $payload);

        return QuickLinkResponseDTO::fromApiResponse($response);
    }

    /**
     * Convenience wrapper for the "Card Change API" flow: sends a
     * minimal QuickLink with ChangeCard=true against an existing
     * agreement, so paying it updates the stored card instead of
     * taking a new payment.
     */
    public function requestCardChange(
        string $agreementId,
        string $email,
        Currency $currency,
        string $amount = '1',
        string $description = 'Card update',
        ?string $cartId = null,
    ): QuickLinkResponseDTO {
        $details = new QuickLinkDetailsDTO(
            desc: $description,
            cart: $cartId ?? uniqid('cardchange_', true),
            currency: $currency,
            amount: $amount,
            email: $email,
            agreementId: (int) $agreementId,
            changeCard: true,
        );

        return $this->create(new CreateQuickLinkDTO(details: $details));
    }
}
