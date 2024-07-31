<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilesInternal extends Model
{
    use HasFactory;

    protected $fillable = ['staff_detail_id', 'name', 'path'];
}
