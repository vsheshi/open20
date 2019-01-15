#!/usr/bin/env php
<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

$functions         = require __DIR__ . '/arginfo.php';
$resourceFunctions = [];

foreach ($functions as $function => $arguments) {
    foreach ($arguments as $argument) {
        if ($argument == 'resource') {
            $resourceFunctions[] = $function;
        }
    }
}

$resourceFunctions = array_unique($resourceFunctions);
sort($resourceFunctions);

$buffer = <<<EOT
<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\ResourceOperations;

class ResourceOperations
{
    /**
     * @return string[]
     */
    public static function getFunctions()
    {
        return [

EOT;

foreach ($resourceFunctions as $function) {
    $buffer .= sprintf("            '%s',\n", $function);
}

$buffer .= <<< EOT
        ];
    }
}

EOT;

file_put_contents(__DIR__ . '/../src/ResourceOperations.php', $buffer);

