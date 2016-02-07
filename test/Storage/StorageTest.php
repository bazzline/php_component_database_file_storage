<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage\Storage;

use Net\Bazzline\Component\Database\FileStorage\Storage\Storage;
use Test\Net\Bazzline\Component\Database\FileStorage\FileStorageTestCase;

/**
 * Class StorageTest
 *
 * @package Test\Net\Bazzline\Component\Database\FileStorage
 */
class StorageTest extends FileStorageTestCase
{
    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Database\FileStorage\IdGenerator\IdGeneratorInterface */
    private $generator;

    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Csv\Reader\Reader */
    private $reader;

    /** @var Storage */
    private $storage;

    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Database\FileStorage\Writer\LockableWriterInterface */
    private $writer;

    protected function setUp()
    {
        $this->generator    = $this->getMockOfIdGeneratorInterface();
        $this->reader       = $this->getMockOfReader();
        $this->storage   = $this->getNewRepository();
        $this->writer       = $this->getMockOfWriter();

        $this->storage->injectGenerator($this->generator);
        $this->storage->injectReader($this->reader);
        $this->storage->injectWriter($this->writer);
    }

    public function testCreate()
    {
        $this->markTestSkipped();
    }

    public function testReadMany()
    {
        $this->markTestSkipped();
    }

    public function testReadOne()
    {
        $this->markTestSkipped();
    }

    public function testUpdate()
    {
        $this->markTestSkipped();
    }

    public function testDelete()
    {
        $this->markTestSkipped();
    }

    public function testFilterBy()
    {
        $this->markTestSkipped();
    }

    public function testFilterById()
    {
        $this->markTestSkipped();
    }

    public function testLimitBy()
    {
        $this->markTestSkipped();
    }
}
