<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_details_id',
        'deductions',
        'earnings',
        'status',
        'total'
    ];

    protected $casts = [
        'deductions' => 'array',
        'earnings' => 'array',
        'status' => \App\Casts\PayslipStatus::class,
        'total' => \App\Casts\Money::class
    ];

    public function staffDetails()
    {
        return $this->belongsTo(StaffDetails::class);
    }
}
