<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoReport extends Model
{
    protected $fillable = [
        'project_name',
        'completion_date',
        'description',
        'status',
        'file_paths', // JSON array of file paths
    ];

    protected $casts = [
        'completion_date' => 'timestamp', // Cast completion date to timestamp
        'file_paths' => 'json', // Cast file_paths as JSON
    ];

    public $timestamps = true; // Enable Laravel's automatic timestamps
}
