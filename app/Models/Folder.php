<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'folder_name',
        'vendor_name',
        'offer_date',
        'offer_link',
        'status',
        'file_path',
    ];

    protected $casts = [
        'offer_date' => 'timestamp', // Cast offer_date to a timestamp
    ];

    public $timestamps = true; // Enable Laravel's automatic timestamps
}
