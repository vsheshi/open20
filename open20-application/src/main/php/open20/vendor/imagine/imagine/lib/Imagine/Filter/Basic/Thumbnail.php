<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Filter\Basic;

use Imagine\Image\ImageInterface;
use Imagine\Image\BoxInterface;
use Imagine\Filter\FilterInterface;

/**
 * A thumbnail filter
 */
class Thumbnail implements FilterInterface
{
    /**
     * @var BoxInterface
     */
    private $size;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $filter;

    /**
     * Constructs the Thumbnail filter with given width, height and mode
     *
     * @param BoxInterface $size
     * @param string       $mode
     * @param string       $filter
     */
    public function __construct(BoxInterface $size, $mode = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->size = $size;
        $this->mode = $mode;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        return $image->thumbnail($this->size, $this->mode, $this->filter);
    }
}
