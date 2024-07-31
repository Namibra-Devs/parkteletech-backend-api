<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderInternal extends Model
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
        'offer_date' => 'timestamp',
    ];

    public $timestamps = true;
}
