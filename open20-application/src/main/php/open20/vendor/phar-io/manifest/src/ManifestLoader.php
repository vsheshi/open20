<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class ManifestLoader {
    /**
     * @param string $filename
     *
     * @return Manifest
     *
     * @throws ManifestLoaderException
     */
    public static function fromFile($filename) {
        try {
            return (new ManifestDocumentMapper())->map(
                ManifestDocument::fromFile($filename)
            );
        } catch (Exception $e) {
            throw new ManifestLoaderException(
                sprintf('Loading %s failed.', $filename),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $filename
     *
     * @return Manifest
     *
     * @throws ManifestLoaderException
     */
    public static function fromPhar($filename) {
        return self::fromFile('phar://' . $filename . '/manifest.xml');
    }

    /**
     * @param string $manifest
     *
     * @return Manifest
     *
     * @throws ManifestLoaderException
     */
    public static function fromString($manifest) {
        try {
            return (new ManifestDocumentMapper())->map(
                ManifestDocument::fromString($manifest)
            );
        } catch (Exception $e) {
            throw new ManifestLoaderException(
                'Processing string failed',
                $e->getCode(),
                $e
            );
        }
    }
}
