<?php

namespace App\Traits;

use ValueError;

/**
 * Persist backed enums as their raw string values while exposing
 * enum instances to the application.
 *
 * Property defaults are applied when the object is instantiated,
 * so an overriding $enums map is already populated before the
 * parent Model constructor runs.
 */
trait HasEnumValues
{
    public function setAttribute($key, $value)
    {
        if ($value !== null && isset($this->enums[$key]) && ! $value instanceof \BackedEnum) {
            $value = $this->asEnum($this->enums[$key], $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value !== null && isset($this->enums[$key]) && ! $value instanceof \BackedEnum) {
            return $this->asEnum($this->enums[$key], $value);
        }

        return $value;
    }

    protected function asEnum(string $enumClass, mixed $value): \BackedEnum
    {
        try {
            return $enumClass::from($value);
        } catch (ValueError $e) {
            throw new \UnexpectedValueException(
                'Nilai tidak valid untuk tipe data: '.$value, previous: $e
            );
        }
    }

    /**
     * @return array<int, string>
     */
    public static function enumValues(string $attribute): array
    {
        $instance = new static;

        return array_column($instance->enums[$attribute]::cases(), 'value');
    }
}
