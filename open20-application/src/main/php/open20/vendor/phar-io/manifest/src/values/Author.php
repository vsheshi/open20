<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class Author {
    /**
     * @var string
     */
    private $name;

    /**
     * @var Email
     */
    private $email;

    /**
     * @param string $name
     * @param Email  $email
     */
    public function __construct($name, Email $email) {
        $this->name  = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return string
     */
    public function __toString() {
        return sprintf(
            '%s <%s>',
            $this->name,
            $this->email
        );
    }
}
