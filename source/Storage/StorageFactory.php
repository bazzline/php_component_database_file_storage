<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-07 
 */

namespace Net\Bazzline\Component\Database\FileStorage\Storage;

use Net\Bazzline\Component\Database\FileStorage\IdGenerator\IdGeneratorInterface;
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
        $generator      = $this->getIdGenerator();
        $readerFactory  = $this->getReaderFactory();
        $reader         = $readerFactory->create();
        $storage        = $this->getStorage();
        $writerFactory  = $this->getLockableWriterFactory();
        $writer         = $writerFactory->create();

        $storage->injectGenerator($generator);
        $storage->injectReader($reader);
        $storage->injectWriter($writer);

        return $storage;
    }

    /**
     * @return IdGeneratorInterface
     */
    protected function getIdGenerator()
    {
        return new UUIDGenerator();
    }

    /**
     * @return LockableWriterFactory
     */
    protected function getLockableWriterFactory()
    {
        return new LockableWriterFactory();
    }

    /**
     * @return ReaderFactory
     */
    protected function getReaderFactory()
    {
        return new ReaderFactory();
    }

    /**
     * @return Storage
     */
    protected function getStorage()
    {
        return new Storage();
    }
}
