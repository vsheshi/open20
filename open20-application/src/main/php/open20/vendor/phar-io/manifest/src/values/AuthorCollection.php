<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class AuthorCollection implements \Countable, \IteratorAggregate {
    /**
     * @var Author[]
     */
    private $authors = [];

    public function add(Author $author) {
        $this->authors[] = $author;
    }

    /**
     * @return Author[]
     */
    public function getAuthors() {
        return $this->authors;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->authors);
    }

    /**
     * @return AuthorCollectionIterator
     */
    public function getIterator() {
        return new AuthorCollectionIterator($this);
    }
}
