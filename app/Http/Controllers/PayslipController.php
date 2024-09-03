<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePayslipRequest;
use App\Services\Interfaces\PayslipContract;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function __construct(
        private PayslipContract $payslipService
    ) {}

    public function create(CreatePayslipRequest $request)
    {
        $createdPayslip = $this->payslipService->create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Payslip created successfully!',
            'data' => $createdPayslip
        ], 201);
    }
}
