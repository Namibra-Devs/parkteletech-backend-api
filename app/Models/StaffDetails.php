<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'email',
        'dob',
        'phone',
        'id_type',
        'id_no',
        'employment_status',
        'address',
        'documents',
    ];
}
