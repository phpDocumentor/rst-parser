<?php

use phpDocumentor\Guides\RestructuredText\Builder;

include (__DIR__ . '/../vendor/autoload.php');

$source = __DIR__ . '/../docs/en';
$target = __DIR__ . '/../build/output';

if (!@mkdir($target, 0777, true) && !is_dir($target)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $target));
}

$builder = new Builder();
$builder->build($source, $target);
