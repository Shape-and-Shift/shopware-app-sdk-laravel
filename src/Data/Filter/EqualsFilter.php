<?php

declare(strict_types=1);

namespace Sas\ShopwareAppLaravelSdk\Data\Filter;

class EqualsFilter extends Filter
{
    private string $field;

    private mixed $value;

    public function __construct(string $field, mixed $value)
    {
        $this->field = $field;
        $this->value = $value;
    }

    public function parse(): array
    {
        return [
            'type' => self::TYPE_EQUALS,
            'field' => $this->field,
            'value' => $this->value,
        ];
    }
}
