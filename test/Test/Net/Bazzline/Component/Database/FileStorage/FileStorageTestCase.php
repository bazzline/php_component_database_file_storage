<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage;

use Mockery;
use Net\Bazzline\Component\Database\FileStorage\Repository;
use Net\Bazzline\Component\Database\FileStorage\UUIDGenerator;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

abstract class FileStorageTestCase extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return \org\bovigo\vfs\vfsStreamDirectory
     */
    protected function getFileSystem()
    {
        return vfsStream::setup('test');
    }

    /**
     * @param string $name
     * @return \org\bovigo\vfs\vfsStreamFile
     */
    protected function getNewFile($name)
    {
        return vfsStream::newFile($name);
    }

    /**
     * @param string $name
     * @return \org\bovigo\vfs\vfsStreamDirectory
     */
    protected function getNewDirectory($name)
    {
        return vfsStream::newDirectory($name);
    }

    /**
     * @return Repository
     */
    protected function getNewRepository()
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
