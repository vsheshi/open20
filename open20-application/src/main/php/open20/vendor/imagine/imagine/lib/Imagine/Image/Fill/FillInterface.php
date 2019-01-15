<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image\Fill;

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\PointInterface;

/**
 * Interface for the fill
 */
interface FillInterface
{
    /**
     * Gets color of the fill for the given position
     *
     * @param PointInterface $position
     *
     * @return ColorInterface
     */
    public function getColor(PointInterface $position);
}
