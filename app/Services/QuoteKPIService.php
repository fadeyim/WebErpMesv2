<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class QuoteKPIService
{
    /**
     * Retrieves the rate of grouped quotes by status.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getQuotesDataRate($year, $companyId = null)
    {
        $query = DB::table('quotes')
                    ->select('statu', DB::raw('count(*) as QuoteCountRate'))
                    ->whereYear('created_at', $year)
                    ->groupBy('statu');

            // If a company ID is provided, add the filter
            if ($companyId) {
                $query->where('companies_id', $companyId);
            }

            return $query->get();
    }


    /**
     * Retrieves the monthly summary of quotes for the current year, filtered by company.
     *
     * @param int $year
     * @param int|null $companyId
     * @return \Illuminate\Support\Collection
     */
    public function getQuoteMonthlyRecap($year, $companyId = null)
    {
        $cacheKey = 'quote_monthly_recap_' . $year . '_company_' . ($companyId ?? 'all');
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($year, $companyId) {
            // Commence la requête avec une jointure et un filtrage éventuel par compagnie
            $query = DB::table('quote_lines')
                ->selectRaw('
                    MONTH(created_at) AS month,
                    SUM((selling_price * qty)-(selling_price * qty)*(discount/100)) AS quoteSum
                ')
                ->whereYear('quote_lines.created_at', $year)
                ->groupByRaw('MONTH(quote_lines.created_at)');

            // If a company ID is provided, add the filter
            if ($companyId) {
                $query->where('quotes.companies_id', $companyId);
            }

            // Execute and return results
            return $query->get();
        });

    }

        /**
     * Retrieves the monthly summary of quote for the last year.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getQuoteMonthlyRecapPreviousYear($year)
    {
        $lastyear = $year-1;
        $cacheKey = 'quote_monthly_recap_lastyear_' . $lastyear;
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($lastyear) {
            return DB::table('quote_lines')
                        ->selectRaw('
                            MONTH(created_at) AS month,
                            SUM((selling_price * qty)-(selling_price * qty)*(discount/100)) AS quoteSum
                        ')
                        ->whereYear('quote_lines.created_at', $lastyear)
                        ->groupByRaw('MONTH(quote_lines.created_at)')
                        ->get();
        });
    }

}
