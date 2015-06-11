<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-07 
 */

namespace Net\Bazzline\Component\Database\FileStorage;

use Net\Bazzline\Component\Csv\Reader\ReaderFactory;
use Net\Bazzline\Component\Csv\Writer\WriterFactory;
use Net\Bazzline\Component\Lock\FileLock;

class RepositoryFactory
{
    /**
     * @return Repository
     */
    public function create()
    {
        $generator      = new UUIDGenerator();
        $lock           = new FileLock();
        $readerFactory  = new ReaderFactory();
        $reader         = $readerFactory->create();
        $repository     = new Repository();
        $writerFactory  = new WriterFactory();
        $writer         = $writerFactory->create();

        $repository->injectGenerator($generator);
        $repository->injectLock($lock);
        $repository->injectReader($reader);
        $repository->injectWriter($writer);

        return $repository;
    }
}