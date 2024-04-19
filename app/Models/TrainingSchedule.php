<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_topic',
        'desc',
        'time',
        'date',
        'department',
        'individuals'
    ];

}
