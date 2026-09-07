<?php

namespace yacoubalhaidari\Telr\Contracts;

use yacoubalhaidari\Telr\DTOs\QuickLink\CreateQuickLinkDTO;
use yacoubalhaidari\Telr\DTOs\QuickLink\QuickLinkResponseDTO;

interface QuickLinkInterface
{
    public function create(CreateQuickLinkDTO $link): QuickLinkResponseDTO;
}
