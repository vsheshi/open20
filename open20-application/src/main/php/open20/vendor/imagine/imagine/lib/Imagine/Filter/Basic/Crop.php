<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Filter\Basic;

use Imagine\Image\ImageInterface;
use Imagine\Image\BoxInterface;
use Imagine\Image\PointInterface;
use Imagine\Filter\FilterInterface;

/**
 * A crop filter
 */
class Crop implements FilterInterface
{
    /**
     * @var PointInterface
     */
    private $start;

    /**
     * @var BoxInterface
     */
    private $size;

    /**
     * Constructs a Crop filter with given x, y, coordinates and crop width and
     * height values
     *
     * @param PointInterface $start
     * @param BoxInterface   $size
     */
    public function __construct(PointInterface $start, BoxInterface $size)
    {
        $this->start = $start;
        $this->size  = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->crop($this->start, $this->size);
    }
}
