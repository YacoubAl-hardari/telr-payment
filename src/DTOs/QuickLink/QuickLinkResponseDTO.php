<?php

namespace yacoubalhaidari\Telr\DTOs\QuickLink;

final class QuickLinkResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly int $code,
        public readonly string $status,
        public readonly ?string $url = null,
        public readonly array $raw = [],
    ) {
    }

    public static function fromApiResponse(array $response): self
    {
        $payload = $response['QuickLinkResponse'] ?? [];
        $code = (int) ($payload['Code'] ?? 0);

        return new self(
            success: $code === 200,
            code: $code,
            status: (string) ($payload['Status'] ?? ''),
            url: $payload['URL'] ?? null,
            raw: $response,
        );
    }
}
