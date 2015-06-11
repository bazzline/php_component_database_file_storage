<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage;

use Mockery;
use Net\Bazzline\Component\Database\FileStorage\Repository;
use Net\Bazzline\Component\Database\FileStorage\UUIDGenerator;
use PHPUnit_Framework_TestCase;

abstract class FileStorageTestCase extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }



    /**
     * @return Repository
     */
    protected function getNewRepositry()
    {
        return new Repository();
    }

    /**
     * @return UUIDGenerator
     */
    protected function getNewUUIDGenerator()
    {
        return new UUIDGenerator();
    }
}
