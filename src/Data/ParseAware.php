<?php

declare(strict_types=1);

namespace Sas\ShopwareAppLaravelSdk\Data;

interface ParseAware
{
    public function parse(): array;
}
