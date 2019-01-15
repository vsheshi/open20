<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\Cache;

/**
 *
 * @internal
 */
interface FileHandlerInterface
{
    /**
     * @return string
     */
    public function getFile();

    /**
     * @return CacheInterface
     */
    public function read();

    /**
     * @param CacheInterface $cache
     */
    public function write(CacheInterface $cache);
}
