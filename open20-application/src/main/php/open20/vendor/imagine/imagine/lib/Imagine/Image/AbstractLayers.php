<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image;

abstract class AbstractLayers implements LayersInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(ImageInterface $image)
    {
        $this[] = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($offset, ImageInterface $image)
    {
        $this[$offset] = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($offset)
    {
        unset($this[$offset]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($offset)
    {
        return $this[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function has($offset)
    {
        return isset($this[$offset]);
    }
}
