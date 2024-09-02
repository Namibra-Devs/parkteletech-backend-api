<?php

namespace App\Http\Controllers;

use App\Service\Interfaces\PayslipContract;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function __construct(
        private PayslipContract $payslipService
    ) {}

    public function create() {}
}
