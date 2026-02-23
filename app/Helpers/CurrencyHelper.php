<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class CurrencyHelper
{
    /**
     * List of supported currencies with their details
     */
    public static function getCurrencies(): array
    {
        return [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'position' => 'before', 'decimal_places' => 2],
            'EUR' => ['name' => 'Euro', 'symbol' => '€', 'position' => 'before', 'decimal_places' => 2],
            'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'position' => 'before', 'decimal_places' => 2],
            'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥', 'position' => 'before', 'decimal_places' => 0],
            'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'position' => 'before', 'decimal_places' => 2],
            'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'position' => 'before', 'decimal_places' => 2],
            'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$', 'position' => 'before', 'decimal_places' => 2],
            'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'position' => 'before', 'decimal_places' => 2],
            'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF', 'position' => 'before', 'decimal_places' => 2],
            'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'position' => 'before', 'decimal_places' => 2],
            'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦', 'position' => 'before', 'decimal_places' => 2],
            'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R', 'position' => 'before', 'decimal_places' => 2],
            'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$', 'position' => 'before', 'decimal_places' => 2],
            'MXN' => ['name' => 'Mexican Peso', 'symbol' => 'MX$', 'position' => 'before', 'decimal_places' => 2],
            'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$', 'position' => 'before', 'decimal_places' => 2],
            'HKD' => ['name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'position' => 'before', 'decimal_places' => 2],
            'KRW' => ['name' => 'South Korean Won', 'symbol' => '₩', 'position' => 'before', 'decimal_places' => 0],
            'THB' => ['name' => 'Thai Baht', 'symbol' => '฿', 'position' => 'before', 'decimal_places' => 2],
            'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ', 'position' => 'before', 'decimal_places' => 2],
            'SAR' => ['name' => 'Saudi Riyal', 'symbol' => '﷼', 'position' => 'after', 'decimal_places' => 2],
            'NZD' => ['name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'position' => 'before', 'decimal_places' => 2],
            'SEK' => ['name' => 'Swedish Krona', 'symbol' => 'kr', 'position' => 'after', 'decimal_places' => 2],
            'NOK' => ['name' => 'Norwegian Krone', 'symbol' => 'kr', 'position' => 'after', 'decimal_places' => 2],
            'DKK' => ['name' => 'Danish Krone', 'symbol' => 'kr', 'position' => 'after', 'decimal_places' => 2],
            'PLN' => ['name' => 'Polish Zloty', 'symbol' => 'zł', 'position' => 'after', 'decimal_places' => 2],
            'RUB' => ['name' => 'Russian Ruble', 'symbol' => '₽', 'position' => 'after', 'decimal_places' => 2],
            'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺', 'position' => 'before', 'decimal_places' => 2],
            'PHP' => ['name' => 'Philippine Peso', 'symbol' => '₱', 'position' => 'before', 'decimal_places' => 2],
            'IDR' => ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'position' => 'before', 'decimal_places' => 0],
            'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'position' => 'before', 'decimal_places' => 2],
            'VND' => ['name' => 'Vietnamese Dong', 'symbol' => '₫', 'position' => 'after', 'decimal_places' => 0],
            'EGP' => ['name' => 'Egyptian Pound', 'symbol' => 'E£', 'position' => 'before', 'decimal_places' => 2],
            'PKR' => ['name' => 'Pakistani Rupee', 'symbol' => '₨', 'position' => 'before', 'decimal_places' => 2],
            'BDT' => ['name' => 'Bangladeshi Taka', 'symbol' => '৳', 'position' => 'before', 'decimal_places' => 2],
            'GHS' => ['name' => 'Ghanaian Cedi', 'symbol' => '₵', 'position' => 'before', 'decimal_places' => 2],
            'TZS' => ['name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'position' => 'before', 'decimal_places' => 2],
            'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'USh', 'position' => 'before', 'decimal_places' => 0],
        ];
    }

    /**
     * Get user's preferred currency code
     */
    public static function getUserCurrency(): string
    {
        $user = Auth::user();
        return $user && $user->currency_code ? $user->currency_code : 'USD';
    }

    /**
     * Get currency details by code
     */
    public static function getCurrency(string $code): ?array
    {
        $currencies = self::getCurrencies();
        return $currencies[$code] ?? null;
    }

    /**
     * Format amount with currency symbol
     */
    public static function format(float $amount, ?string $currencyCode = null): string
    {
        $currencyCode = $currencyCode ?? self::getUserCurrency();
        $currency = self::getCurrency($currencyCode);
        
        if (!$currency) {
            $currency = self::getCurrency('USD');
        }
        
        $formattedNumber = number_format(
            $amount, 
            $currency['decimal_places'], 
            '.', 
            ','
        );
        
        if ($currency['position'] === 'before') {
            return $currency['symbol'] . $formattedNumber;
        }
        
        return $formattedNumber . ' ' . $currency['symbol'];
    }

    /**
     * Format amount with currency code (e.g., USD 1,234.56)
     */
    public static function formatWithCode(float $amount, ?string $currencyCode = null): string
    {
        $currencyCode = $currencyCode ?? self::getUserCurrency();
        $currency = self::getCurrency($currencyCode);
        
        if (!$currency) {
            $currency = self::getCurrency('USD');
        }
        
        $formattedNumber = number_format(
            $amount, 
            $currency['decimal_places'], 
            '.', 
            ','
        );
        
        return $currencyCode . ' ' . $formattedNumber;
    }

    /**
     * Get currency symbol
     */
    public static function getSymbol(?string $currencyCode = null): string
    {
        $currencyCode = $currencyCode ?? self::getUserCurrency();
        $currency = self::getCurrency($currencyCode);
        
        return $currency ? $currency['symbol'] : '$';
    }

    /**
     * Get currency name
     */
    public static function getName(?string $currencyCode = null): string
    {
        $currencyCode = $currencyCode ?? self::getUserCurrency();
        $currency = self::getCurrency($currencyCode);
        
        return $currency ? $currency['name'] : 'US Dollar';
    }
}
