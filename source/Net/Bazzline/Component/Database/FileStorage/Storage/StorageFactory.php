<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-07 
 */

namespace Net\Bazzline\Component\Database\FileStorage\Storage;

use Net\Bazzline\Component\Database\FileStorage\IdGenerator\UUIDGenerator;
use Net\Bazzline\Component\Csv\Reader\ReaderFactory;
use Net\Bazzline\Component\Database\FileStorage\Writer\LockableWriterFactory;

/**
 * Class StorageFactory
 *
 * @package Net\Bazzline\Component\Database\FileStorage\Storage
 */
class StorageFactory
{
    /**
     * @return Storage
     */
    public function create()
    {
        $generator      = new UUIDGenerator();
        $readerFactory  = new ReaderFactory();
        $reader         = $readerFactory->create();
        $repository     = new Storage();
        $writerFactory  = new LockableWriterFactory();
        $writer         = $writerFactory->create();

        $repository->injectGenerator($generator);
        $repository->injectReader($reader);
        $repository->injectWriter($writer);

        return $repository;
    }
}
