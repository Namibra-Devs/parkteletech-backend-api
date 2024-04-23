<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'project_type',
        'item_deliver',
        'delivery_date',
        'status',
    ];
}
