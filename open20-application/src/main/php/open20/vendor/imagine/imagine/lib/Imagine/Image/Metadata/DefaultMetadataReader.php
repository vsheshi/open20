<?php

/*
 *
 * (l) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image\Metadata;

/**
 * Default metadata reader
 */
class DefaultMetadataReader extends AbstractMetadataReader
{
    /**
     * {@inheritdoc}
     */
    protected function extractFromFile($file)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    protected function extractFromData($data)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    protected function extractFromStream($resource)
    {
        return array();
    }
}
