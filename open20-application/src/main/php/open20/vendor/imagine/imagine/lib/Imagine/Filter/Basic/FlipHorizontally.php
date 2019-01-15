<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Filter\Basic;

use Imagine\Image\ImageInterface;
use Imagine\Filter\FilterInterface;

/**
 * A "flip horizontally" filter
 */
class FlipHorizontally implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->flipHorizontally();
    }
}
