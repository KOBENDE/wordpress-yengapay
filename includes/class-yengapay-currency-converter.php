<?php

class YengaPay_Currency_Converter {
    // Static conversion rates (to be replaced with an exchange rate API in production)
    private static $conversion_rates = [
        'EUR' => [
            'XOF' => 655.957, // Fixed rate EUR -> XOF
        ],
        'USD' => [
            'XOF' => 600.00, // Approximate rate USD -> XOF (to be updated)
        ]
    ];

    /**
     * Converts an amount from a source currency to XOF
     *
     * @param float $amount Amount to convert
     * @param string $from_currency Source currency (EUR or USD)
     * @return float Amount converted to XOF
     */
    public static function convert_to_xof($amount, $from_currency) {
        $from_currency = strtoupper($from_currency);
        
        // Check if conversion is necessary
        if ($from_currency === 'XOF') {
            return $amount;
        }

        // Check if conversion rate exists
        if (!isset(self::$conversion_rates[$from_currency]['XOF'])) {
            throw new Exception("Conversion rate not available for {$from_currency} to XOF");
        }

        // Perform the conversion
        $converted_amount = $amount * self::$conversion_rates[$from_currency]['XOF'];
        
        // Round to the nearest integer
        return round($converted_amount);
    }

    /**
     * Gets the current conversion rate
     *
     * @param string $from_currency Source currency
     * @return float|null Conversion rate or null if not available
     */
    public static function get_conversion_rate($from_currency) {
        $from_currency = strtoupper($from_currency);
        return isset(self::$conversion_rates[$from_currency]['XOF']) 
            ? self::$conversion_rates[$from_currency]['XOF'] 
            : null;
    }
}