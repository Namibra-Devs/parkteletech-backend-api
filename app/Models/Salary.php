<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 
        'basic_salary', 
        'allowances',
    ];

    protected $casts = [
        'allowances' => 'array',
        'basic_salary' => Money::class,
    ];

    public function staff()
    {
        return $this->belongsTo(StaffDetails::class);
    }
}
