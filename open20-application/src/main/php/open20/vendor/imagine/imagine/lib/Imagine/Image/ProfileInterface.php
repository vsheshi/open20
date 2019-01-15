<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image;

interface ProfileInterface
{
    /**
     * Returns the name of the profile
     *
     * @return String
     */
    public function name();

    /**
     * Returns the profile data
     *
     * @return String
     */
    public function data();
}
