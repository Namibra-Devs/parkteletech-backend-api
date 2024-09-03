<?php

namespace App\Services;

use App\Models\Payslip;
use App\Models\StaffDetails;
use App\Services\Interfaces\PayslipContract;

class PayslipService implements PayslipContract
{
    private function calculateAmounts(array $items, string $key = 'amount'): int|float
    {
        $total = 0;
        foreach ($items as $item) {
            $total += (float) $item[$key];
        }
        return $total;
    }


    /**
     * @inheritDoc
     */
    public function create(array $paySlipData)
    {
        if (empty($paySlipData['earnings']) || empty($paySlipData['deductions']))
            abort('400', 'Earnings and deductions are required');
        $totalEarnings = $this->calculateAmounts($paySlipData['earnings']);
        $totalDeductions = $this->calculateAmounts($paySlipData['deductions']);
        $total = $totalEarnings - $totalDeductions;
        $createdPayslip = Payslip::create([
            'staff_details_id' => $paySlipData['employee_id'],
            'earnings' => $paySlipData['earnings'],
            'deductions' => $paySlipData['deductions'],
            'total' => $total,
            'status' => 0
        ]);
        // dd( $createdPayslip->staffDetail()->get());
        // $staff = StaffDetails::find($paySlipData['employee_id']);
        // $createdPayslip->staffDetails()->associate($staff);
        // $createdPayslip->save();
        return $createdPayslip->load('staffDetails');
    }
}
