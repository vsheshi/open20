<?php

namespace Faker\Provider\th_TH;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @var array Thai phone number formats
     */
    protected static $formats = array(
        '0 #### ####',
        '+66 #### ####',
        '0########',
    );

    /**
     * @var array Thai mobile phone number formats
     */
    protected static $mobileFormats = array(
      '08# ### ####',
      '08 #### ####',
      '09# ### ####',
      '09 #### ####',
    );

    /**
     * Returns a Thai mobile phone number
     * @return string
     */
    public static function mobileNumber()
    {
        return static::numerify(static::randomElement(static::$mobileFormats));
    }
}
