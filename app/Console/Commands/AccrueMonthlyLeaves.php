<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;

class AccrueMonthlyLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:accrue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrue monthly leaves for all active employees based on leave types configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $currentMonth = now()->month;

        // Determine which accrual types are active this month
        $activeAccrualTypes = ['Monthly'];

        // Quarterly runs in Jan (1), Apr (4), Jul (7), Oct (10)
        if (in_array($currentMonth, [1, 4, 7, 10])) {
            $activeAccrualTypes[] = 'Quarterly';
        }

        // Yearly runs in Jan (1)
        if ($currentMonth === 1) {
            $activeAccrualTypes[] = 'Yearly';
        }

        $leaveTypes = LeaveType::where('status', 'Active')
            ->whereIn('accrual_type', $activeAccrualTypes)
            ->get();

        if ($leaveTypes->isEmpty()) {
            $this->info('No active leave types found for accrual this month.');
            return;
        }

        $count = 0;
        foreach ($users as $user) {
            foreach ($leaveTypes as $type) {
                // Find or create user's leave balance record
                $balanceRecord = LeaveBalance::firstOrCreate(
                    [
                        'leave_type_id' => $type->id,
                        'user_id'       => $user->id,
                    ],
                    [
                        'balance'       => 0.00,
                        'created_by'    => $user->id,
                        'updated_by'    => $user->id,
                    ]
                );

                $previousBalance = (float) $balanceRecord->balance;
                $accrualAmount = (float) $type->accrual;
                $newBalance = $previousBalance + $accrualAmount;

                // Enforce max balance limit if set
                if ($type->max_balance > 0 && $newBalance > $type->max_balance) {
                    $newBalance = $type->max_balance;
                    $accrualAmount = $newBalance - $previousBalance;
                }

                if ($accrualAmount > 0) {
                    $balanceRecord->balance = $newBalance;
                    $balanceRecord->updated_by = $user->id;
                    $balanceRecord->save();

                    // Log to leave_transactions
                    \App\Models\LeaveTransaction::create([
                        'user_id'          => $user->id,
                        'leave_type_id'    => $type->id,
                        'transaction_type' => 'credit',
                        'amount'           => $accrualAmount,
                        'previous_balance' => $previousBalance,
                        'current_balance'  => $newBalance,
                        'remarks'          => $type->accrual_type . ' Accrual',
                        'created_by'       => 1, // System admin
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Accrued leaves successfully for {$count} records (Intervals processed: " . implode(', ', $activeAccrualTypes) . ")!");
    }
}