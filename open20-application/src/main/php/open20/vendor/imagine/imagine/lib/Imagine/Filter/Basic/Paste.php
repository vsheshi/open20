<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Filter\Basic;

use Imagine\Image\ImageInterface;
use Imagine\Image\PointInterface;
use Imagine\Filter\FilterInterface;

/**
 * A paste filter
 */
class Paste implements FilterInterface
{
    /**
     * @var ImageInterface
     */
    private $image;

    /**
     * @var PointInterface
     */
    private $start;

    /**
     * Constructs a Paste filter with given ImageInterface to paste and x, y
     * coordinates of target position
     *
     * @param ImageInterface $image
     * @param PointInterface $start
     */
    public function __construct(ImageInterface $image, PointInterface $start)
    {
        $this->image = $image;
        $this->start = $start;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->paste($this->image, $this->start);
    }
}
