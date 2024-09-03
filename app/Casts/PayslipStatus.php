<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PayslipStatus implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return match ($value) {
            0 => 'Draft',
            1 => 'Generated',
            2 => 'Paid',
            default => null,
        };
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (gettype($value) === 'string' && in_array($value, ['Draft', 'Generated', 'Paid']))
            return match ($value) {
                'Draft' => 0,
                'Generated' => 1,
                'Paid' => 2,
                default => null,
            };
        return $value;
    }
}
