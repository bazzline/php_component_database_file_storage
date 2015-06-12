<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage;

use Net\Bazzline\Component\Database\FileStorage\Repository;

class RepositoryTest extends FileStorageTestCase
{
    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Database\FileStorage\IdGeneratorInterface */
    private $generator;

    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Lock\FileLock*/
    private $lock;

    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Csv\Reader\Reader*/
    private $reader;

    /** @var Repository */
    private $repository;

    /** @var \Mockery\MockInterface|\Net\Bazzline\Component\Csv\Writer\Writer*/
    private $writer;

    protected function setUp()
    {
        $this->generator    = $this->getMockOfIdGeneratorInterface();
        $this->lock         = $this->getMockOfFileLock();
        $this->reader       = $this->getMockOfReader();
        $this->repository   = $this->getNewRepository();
        $this->writer       = $this->getMockOfWriter();

        $this->repository->injectGenerator($this->generator);
        $this->repository->injectLock($this->lock);
        $this->repository->injectReader($this->reader);
        $this->repository->injectWriter($this->writer);
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