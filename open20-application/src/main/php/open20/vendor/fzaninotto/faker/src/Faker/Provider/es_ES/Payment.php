<?php

namespace Faker\Provider\es_ES;

class Payment extends \Faker\Provider\Payment
{
    private static $vatMap = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'N', 'P', 'Q', 'R', 'S', 'U', 'V', 'W');

    /**
     * International Bank Account Number (IBAN)
     * @param  string  $prefix      for generating bank account number of a specific bank
     * @param  string  $countryCode ISO 3166-1 alpha-2 country code
     * @param  integer $length      total length without country code and 2 check digits
     * @return string
     */
    public static function bankAccountNumber($prefix = '', $countryCode = 'ES', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }

    /**
     * Value Added Tax (VAT)
     *
     * @example 'B93694545'
     *
     *
     * @return string VAT Number
     */
    public static function vat()
    {
        $letter = static::randomElement(self::$vatMap);
        $number = static::numerify('########');

        return $letter . $number;
    }
}
