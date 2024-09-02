<?php

namespace App\Models;

use App\Casts\Percentage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'description'
    ];

    protected $casts = [
        'rate' => Percentage::class
    ];
}
