<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-07 
 */

require_once __DIR__ . '/../vendor/autoload.php';

$factory    = new \Net\Bazzline\Database\FileStorage\StorageFactory();
$storage    = $factory->create();
$path       = __DIR__ . '/storage';

$storage->injectPath($path);

$collection = $storage->read();

//echo var_export($collection, true) . PHP_EOL;
/**
foreach ($collection as $id => $data) {
    echo $id . ': ' . var_export($data, true) . PHP_EOL;
}
*/
