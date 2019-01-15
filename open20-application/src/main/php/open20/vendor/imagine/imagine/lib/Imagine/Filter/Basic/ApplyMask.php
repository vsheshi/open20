<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;

/**
 * An apply mask filter
 */
class ApplyMask implements FilterInterface
{
    /**
     * @var ImageInterface
     */
    private $mask;

    /**
     * @param ImageInterface $mask
     */
    public function __construct(ImageInterface $mask)
    {
        $this->mask = $mask;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->applyMask($this->mask);
    }
}
