<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Filter\Basic;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\BoxInterface;

/**
 * A resize filter
 */
class Resize implements FilterInterface
{
    /**
     * @var BoxInterface
     */
    private $size;
    private $filter;

    /**
     * Constructs Resize filter with given width and height
     *
     * @param BoxInterface $size
     * @param string       $filter
     */
    public function __construct(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->size = $size;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->resize($this->size, $this->filter);
    }
}
