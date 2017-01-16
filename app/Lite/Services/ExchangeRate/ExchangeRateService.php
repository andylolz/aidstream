<?php namespace App\Lite\Services\ExchangeRate;


use App\Http\Controllers\Complete\Traits\HistoryExchangeRates;
use App\Lite\Services\Traits\ExchangeRateCalculator;
use App\Models\HistoricalExchangeRate as RateModel;
use App\Models\Settings;

/**
 * Class ExchangeRateService
 * @package App\Lite\Services\ExchangeRate
 */
class ExchangeRateService
{
    use HistoryExchangeRates, ExchangeRateCalculator;
    /**
     * @var RateModel
     */
    private $rateModel;
    /**
     * @var Settings
     */
    private $settings;

    /**
     * ExchangeRateService constructor.
     * @param RateModel $rateModel
     * @param Settings  $settings
     */
    public function __construct(RateModel $rateModel, Settings $settings)
    {
        $this->rateModel = $rateModel;
        $this->settings  = $settings;
    }

    /**
     * Converts the amount present in budget of all activities into USD.
     * Fetches the exchange rate if not available.
     * Calculates the total budget and max budget in an activity into USD.
     *
     * @param $activities
     * @return array
     */
    public function budgetDetails($activities)
    {
        $totalAmount     = 0;
        $maxAmount       = 0;
        $exchangedAmount = 0;

        foreach ($activities as $activity) {
            $budget           = $activity->budget;
            $default_currency = $this->getDefaultCurrency($activity);

            if ($budget) {
                foreach ($budget as $key => $value) {
                    $date     = getVal($value, ['value', 0, 'value_date']);
                    $amount   = getVal($value, ['value', 0, 'amount']);
                    $currency = getVal($value, ['value', 0, 'currency']);

                    if ($amount != "") {
                        $currency = ($currency == "") ? $default_currency : $currency;
                        $this->exchangeRate($date);
                        $exchangedAmount = $this->calculate($date, $currency, $amount);
                        $totalAmount     = $totalAmount + $exchangedAmount;
                    }
                }
            }

            if ($maxAmount < $exchangedAmount) {
                $maxAmount = $exchangedAmount;
            }
        }

        $totalAmountInString = $this->formatAmountIntoWord($totalAmount);
        $maxAmountInString   = $this->formatAmountIntoWord($maxAmount);

        return ['total' => $totalAmountInString, 'maxBudget' => $maxAmountInString];
    }

    /**
     * If the field has no currency, then the default currency of activity is used.
     * If the activity has no default currency, then the default currency of settings is used.
     *
     * @param $activity
     * @return string
     */
    public function getDefaultCurrency($activity)
    {
        $defaultCurrency = "USD";

        if ($activityDefaultCurrency = $activity->default_field_values) {
            $defaultCurrency = getVal($activityDefaultCurrency, [0, 'default_currency']);
        }

        if ($defaultCurrency == "") {
            $settings = $this->settings->where('organization_id', session('org_id'))->first();
            (($settingsDefaultCurrency = $settings->default_currency) == "") ?: $defaultCurrency = $settingsDefaultCurrency;
        }

        return $defaultCurrency;
    }

    /**
     * Checks if the exchange rate is already available in database.
     * If exchange rate is not available then the exchange rate is fetched using api.
     * If the date is in future, the exchange rate of present date is fetched.
     *
     * @param $date
     * @return $this
     */
    protected function exchangeRate($date)
    {
        $allDates      = $this->rateModel->select('date')->get()->toArray();
        $newDate       = array_values(array_diff([$date], array_flatten($allDates)));
        $exchangeRates = [];

        if (!empty($newDate)) {
            if (($date = getVal($newDate, [0], date('Y-m-d'))) < date('Y-m-d')) {
                $exchangeRates[] = $this->clean(json_decode($this->curl($date), true), $date);
                $this->storeExchangeRates($exchangeRates);
            } else {
                $this->storePresentDateRate($allDates);
            }
        }

        return $this;
    }

    /**
     * Stores the exchange rate of Present date.
     *
     * @param $allDates
     * @return bool
     */
    protected function storePresentDateRate($allDates)
    {
        if (!in_array(date('Y-m-d'), array_flatten($allDates))) {
            dd($allDates);
            $exchangeRate = $this->clean(json_decode($this->curl(date('Y-m-d')), true), date('Y-m-d'));

            $this->storeExchangeRates($exchangeRate);;
        }

        return false;
    }

    /** Returns the amount with numberFormat.
     *
     * @param $amount
     * @return string
     */
    protected function formatAmountIntoWord($amount)
    {
        $arrayAmount  = (array_map('intval', str_split(round($amount))));
        $numberFormat = $this->numberFormat(count($arrayAmount));

        return $this->placeValueAmount($arrayAmount, $numberFormat);
    }

    /**
     * Returns the first 3 or 2 or 1 digit amount and its numberFormat.
     *
     * @param $arrayAmount
     * @param $numberFormat
     * @return string
     */
    protected function placeValueAmount($arrayAmount, $numberFormat)
    {
        $remainder = count($arrayAmount) % 3;
        $amount    = "";

        ($remainder == 0) ? $i = 3 : $i = $remainder;

        while ($i > 0) {
            $amount = $amount . $arrayAmount[$i - 1];
            $i --;
        }

        return sprintf("$%s %s", strrev($amount), $numberFormat);
    }

    /**
     * Returns numberFormat based on the count of digit of the amount.
     *
     * @param $count
     * @return string
     */
    protected function numberFormat($count)
    {
        if ($count > 3 && $count <= 6) {
            return "K";
        }

        if ($count > 6 && $count <= 9) {
            return "M";
        }

        if ($count > 9 && $count <= 12) {
            return "B";
        }
    }
}
