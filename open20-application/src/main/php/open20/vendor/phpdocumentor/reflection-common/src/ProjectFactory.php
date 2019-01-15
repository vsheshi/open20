<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 */
namespace phpDocumentor\Reflection;

/**
 * Interface for project factories. A project factory shall convert a set of files
 * into an object implementing the Project interface.
 */
interface ProjectFactory
{
    /**
     * Creates a project from the set of files.
     *
     * @param string $name
     * @param File[] $files
     * @return Project
     */
    public function create($name, array $files);
}
