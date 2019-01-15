<?php

namespace Faker\Provider\fr_FR;

class Payment extends \Faker\Provider\Payment
{
    /**
     * Value Added Tax (VAT)
     *
     * @example 'FR12123456789', ('spaced') 'FR 12 123 456 789'
     *
     *
     * @param bool $spacedNationalPrefix
     *
     * @return string VAT Number
     */
    public function vat($spacedNationalPrefix = true)
    {
        $prefix = ($spacedNationalPrefix) ? "FR " : "FR";

        return sprintf("%s%s%s%s", $prefix, self::randomNumber(2, true), $this->siren($spacedNationalPrefix));
    }

    /**
     * International Bank Account Number (IBAN)
     * @param  string  $prefix      for generating bank account number of a specific bank
     * @param  string  $countryCode ISO 3166-1 alpha-2 country code
     * @param  integer $length      total length without country code and 2 check digits
     * @return string
     */
    public static function bankAccountNumber($prefix = '', $countryCode = 'FR', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }
}
