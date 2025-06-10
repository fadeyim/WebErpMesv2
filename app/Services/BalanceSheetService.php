<?php
namespace App\Services;

use App\Models\Accounting\AccountingEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BalanceSheetService
{
    /**
     * Retrieve account balances grouped by account number.
     */
    public function getAccountBalances(): Collection
    {
        return AccountingEntry::select(
            'account_number',
            'account_label',
            DB::raw('SUM(debit_amount) as total_debit'),
            DB::raw('SUM(credit_amount) as total_credit')
        )
        ->groupBy('account_number', 'account_label')
        ->orderBy('account_number')
        ->get()
        ->map(function ($entry) {
            $entry->balance = $entry->total_debit - $entry->total_credit;
            return $entry;
        });
    }

    /**
     * Split balances between assets and liabilities/equity groups.
     */
    public function getBalanceSheet(): array
    {
        $balances = $this->getAccountBalances();

        $assets = $balances->filter(function ($entry) {
            $firstDigit = substr($entry->account_number, 0, 1);
            return in_array($firstDigit, ['1', '2', '3', '4', '5']);
        });

        $liabilities = $balances->filter(function ($entry) {
            $firstDigit = substr($entry->account_number, 0, 1);
            return !in_array($firstDigit, ['1', '2', '3', '4', '5']);
        });

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'assetsTotal' => $assets->sum('balance'),
            'liabilitiesTotal' => $liabilities->sum('balance'),
            'balances' => $balances,
        ];
    }
}
