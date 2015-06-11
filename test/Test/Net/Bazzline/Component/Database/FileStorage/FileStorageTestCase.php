<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage;

use Net\Bazzline\Component\Database\FileStorage\UUIDGenerator;
use PHPUnit_Framework_TestCase;

abstract class FileStorageTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return UUIDGenerator
     */
    protected function getNewUUIDGenerator()
    {
        return new UUIDGenerator();
    }
}