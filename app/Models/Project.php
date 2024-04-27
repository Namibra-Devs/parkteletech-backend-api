<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'project_name',
        'project_location',
        'project_code',
        'offer_date',
        'end_date',
        'status',
    ];

    // Optional: Specify default Laravel timestamps behavior
    public $timestamps = true;

    // Optional: Define casts to ensure correct data types
    // protected $casts = [
    //     'offer_date' => 'timestamp', // Casting offer_date as a timestamp
    //     'end_date' => 'timestamp', // Casting end_date as a timestamp
    // ];
}
