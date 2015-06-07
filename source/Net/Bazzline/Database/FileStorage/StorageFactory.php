<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-07 
 */

namespace Net\Bazzline\Database\FileStorage;

use Net\Bazzline\Component\Csv\Reader\ReaderFactory;
use Net\Bazzline\Component\Csv\Writer\WriterFactory;

class StorageFactory
{
    /**
     * @return Storage
     */
    public function create()
    {
        $generator      = new UUIDGenerator();
        //@todo implement switch for php 5.3
        $readerFactory  = new ReaderFactory();
        $reader         = $readerFactory->create();
        $storage        = new Storage();
        $writerFactory  = new WriterFactory();
        $writer         = $writerFactory->create();

        $storage->injectGenerator($generator);
        $storage->injectReader($reader);
        $storage->injectWriter($writer);

        return $storage;
    }
}