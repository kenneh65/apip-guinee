<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

$loader->add('Html2Pdf_', __DIR__.'/../vendor/html2pdf/lib'); //ligne � ajouter
$loader->add('env',__DIR__.'/config/.env');
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
