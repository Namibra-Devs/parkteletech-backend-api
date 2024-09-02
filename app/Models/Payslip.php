<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'deductions',
        'earnings',
        'status',
    ];

    protected $casts = [
        'deductions' => 'array',
        'earnings' => 'array',
        'status' => \App\Casts\PayslipStatus::class
    ];

    public function employee()
    {
        return $this->belongsTo(StaffDetails::class);
    }
}
