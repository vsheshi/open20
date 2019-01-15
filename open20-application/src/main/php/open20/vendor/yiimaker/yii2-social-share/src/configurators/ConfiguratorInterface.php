<?php
/**
 */

namespace ymaker\social\share\configurators;

/**
 * Interface for configurators of social network drivers.
 *
 * @since 1.0
 */
interface ConfiguratorInterface
{
    /**
     * This method should returns a array with config
     * for social network drivers.
     *
     * @return array
     */
    public function getSocialNetworks();

    /**
     * This method should returns a array with HTML
     * options for share links.
     *
     * @return array
     */
    public function getOptions();
}
