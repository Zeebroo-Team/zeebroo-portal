<?php

namespace Modules\Transaction\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Transaction\Services\LoanRecurringDeductionService;

class ProcessLoanRecurringDeductionsCommand extends Command
{
    protected $signature = 'loans:process-recurring-deductions
                            {--date= : Evaluate as-of this date (Y-m-d); default today}';

    protected $description = 'Log scheduled loan installments (deduction from assigned account) into the transactions ledger';

    public function handle(LoanRecurringDeductionService $service): int
    {
        $asOf = Carbon::today();
        if ($this->option('date')) {
            try {
                $asOf = Carbon::parse((string) $this->option('date'))->startOfDay();
            } catch (\Throwable) {
                $this->error('Invalid --date. Use Y-m-d.');

                return self::FAILURE;
            }
        }

        $result = $service->run($asOf);

        $this->info(sprintf(
            'Loan deductions as of %s: %d new ledger row(s), %d loan(s) had missing installments logged.',
            $asOf->toDateString(),
            $result['entries_created'],
            $result['processed_loans']
        ));

        return self::SUCCESS;
    }
}
