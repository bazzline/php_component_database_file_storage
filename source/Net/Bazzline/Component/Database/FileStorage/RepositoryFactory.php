<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-07 
 */

namespace Net\Bazzline\Component\Database\FileStorage;

use Net\Bazzline\Component\Csv\Reader\ReaderFactory;
use Net\Bazzline\Component\Database\FileStorage\Writer\LockableWriterFactory;

class RepositoryFactory
{
    /**
     * @return Repository
     */
    public function create()
    {
        $generator      = new UUIDGenerator();
        $readerFactory  = new ReaderFactory();
        $reader         = $readerFactory->create();
        $repository     = new Repository();
        $writerFactory  = new LockableWriterFactory();
        $writer         = $writerFactory->create();

        $repository->injectGenerator($generator);
        $repository->injectReader($reader);
        $repository->injectWriter($writer);

        return $repository;
    }
}
