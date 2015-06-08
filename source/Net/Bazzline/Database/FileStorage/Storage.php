<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Database\FileStorage;

use Net\Bazzline\Component\Csv\Reader\Reader;
use Net\Bazzline\Component\Csv\Reader\ReaderFactory;
use Net\Bazzline\Component\Csv\Writer\Writer;

class Storage implements FileStorageInterface
{
    /** @var array */
    private $filters;

    /** @var null|mixed */
    private $filterById;

    /** @var IdGeneratorInterface */
    private $generator;

    /** @var boolean */
    private $hasFilterById;

    /** @var boolean */
    private $hasFilters;

    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $offset;

    /** @var string */
    private $path;

    /** @var Reader */
    private $reader;

    /** @var Writer */
    private $writer;

    public function __construct()
    {
        $this->resetFilters();
    }

    /**
     * @param IdGeneratorInterface $generator
     * @return $this
     */
    public function injectGenerator(IdGeneratorInterface $generator)
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     * @throws InvalidArgumentException
     */
    public function injectPath($path)
    {
        $this->createPathIfNotAvailable($path);
        $this->validatePath($path);
        $this->path = $path . DIRECTORY_SEPARATOR . 'database.csv';

        if (!is_file($this->path)) {
            touch($this->path);
        }

        if (!is_null($this->reader)) {
            $this->reader->setPath($this->path);
        }
        if (!is_null($this->writer)) {
            $this->writer->setPath($this->path);
        }

        return $this;
    }

    /**
     * @param Reader $reader
     * @return $this
     */
    public function injectReader(Reader $reader)
    {
        $this->reader = $reader;

        if (!is_null($this->path)) {
            $this->reader->setPath($this->path);
        }

        return $this;
    }

    /**
     * @param Writer $writer
     * @return $this
     */
    public function injectWriter(Writer $writer)
    {
        $this->writer = $writer;

        if (!is_null($this->path)) {
            $this->writer->setPath($this->path);
        }

        return $this;
    }

    /**
     * @param array $data
     * @return string - unique identifier
     */
    public function create(array $data)
    {
        $id     = $this->generator->generate();
        $line   = array($id, json_encode($data));
        $writer = $this->writer;

        $writer($line);
        $this->resetFilters();

        return $id;
    }



    /**
     * @return array
     * @todo
     */
    public function read()
    {
        $collection = array();
        $reader     = $this->reader;
        $reader->rewind();

        if ($this->hasOffset()) {
            $reader = $this->seekReaderToOffset($reader, $this->offset);
        }

        $iterator   = ($this->hasLimit()) ? $this->limit : -1;

        while ($line = $reader()) {
            if ($this->isValidLine($line)) {
                if ($this->hasFilterById) {
                    if ($line[0] === $this->filterById) {
                        $collection[$line[0]] = (array) json_decode($line[1]);
                        break;
                    }
                } else if ($this->hasFilters) {
                    $data = (array) json_decode($line[1]);

                    foreach ($this->filters as $key => $filter) {
                        if ((isset($data[$key]))
                            && ($data[$key] === $filter)) {
                            $collection[$line[0]] = $data;
                        }
                    }
                } else {
                    $collection[$line[0]] = (array) json_decode($line[1]);
                }
                --$iterator;
                if ($iterator === 0) {
                    break;
                }
            }
        }

        $this->resetFilters();

        return $collection;
    }



    /**
     * @return null|mixed - nothing or data
     */
    public function readOne()
    {
        $this->limitBy(1);
        $collection = $this->read();
        $this->resetFilters();

        return $collection;
    }

    /**
     * @param array $data
     * @return boolean
     */
    public function update(array $data)
    {
        $reader = $this->reader;
        $reader->rewind();
        // TODO: Implement update() method.
        //@reuse filter logic read read
        //@todo read and write into different files
        //@todo replace old file with new file
        $this->resetFilters();
    }

    /**
     * @return boolean
     */
    public function delete()
    {
        // TODO: Implement delete() method.
        //@reuse filter logic read read
        //@todo read and write into different files
        //@todo replace old file with new file
        $this->resetFilters();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return $this
     * @todo implement a way that it is also valid to filter by $key
     *  (without value) and vice versa
     */
    public function filterBy($key, $value)
    {
        $this->filters[$key]    = $value;
        $this->hasFilters       = true;

        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function filterById($id)
    {
        $this->filterById       = $id;
        $this->hasFilterById    = true;

        return $this;
    }

    /**
     * @param int $count
     * @param null|int $offset
     * @return $this
     */
    public function limitBy($count, $offset = null)
    {
        $this->limit = (int) $count;
        if (!is_null($offset)) {
            $this->offset = (int) $offset;
        }

        return $this;
    }

    /**
     * @param string $path
     * @throws InvalidArgumentException
     */
    private function createPathIfNotAvailable($path)
    {
        if (!is_dir($path)) {
            //@todo replace by command if needed
            //$couldNotBeCreated = (!mkdir($path, 0755, true));
            exec('/usr/bin/env mkdir -p ' . $path);
            $couldNotBeCreated = false; //@todo implement

            if ($couldNotBeCreated) {
                $message = 'could not create directory "' . $path . '""';

                throw new InvalidArgumentException($message);
            }
        }
    }

    /**
     * @return bool
     */
    private function hasLimit()
    {
        return (is_int($this->limit));
    }

    /**
     * @return bool
     */
    private function hasOffset()
    {
        return (is_int($this->offset));
    }

    /**
     * @param mixed|array $line
     * @return bool
     */
    private function isValidLine($line)
    {
        return ((is_array($line) && count($line) === 2));
    }

    private function resetFilters()
    {
        $this->filters          = array();
        $this->filterById       = null;
        $this->hasFilterById    = false;
        $this->hasFilters       = false;
        $this->limit            = null;
        $this->offset           = null;
    }

    /**
     * @param string $path
     * @throws InvalidArgumentException
     */
    private function validatePath($path)
    {
        if (!is_dir($path)) {
            $message = 'path "' . $path . '" must be a directory';

            throw new InvalidArgumentException($message);
        }

        if (!is_writable($path)) {
            $message = 'directory "' . $path . '" is not writable';

            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param Reader $reader
     * @param int $offset
     * @return Reader
     */
    private function seekReaderToOffset(Reader $reader, $offset)
    {
        $reader(($offset - 1));

        return $reader;
    }
}