<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id', 'applicant_name', 'status', 'date_applied'
    ];
}
