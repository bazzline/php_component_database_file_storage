<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Database\FileStorage;

use Net\Bazzline\Component\Csv\Reader\Reader;
use Net\Bazzline\Component\Csv\Writer\Writer;

class Storage implements FileStorageInterface
{
    const KEY_ID    = 0;
    const KEY_DATA  = 1;

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
        $line   = $this->createLine($id, $data);
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
                    if ($this->lineContainsId($line, $this->filterById)) {
                        $collection = $this->addLineToCollection($line, $collection);
                        break;
                    }
                } else if ($this->hasFilters) {
                    $data = $this->getDataFromLine($line);

                    foreach ($this->filters as $key => $value) {
                        if ((isset($data[$key]))
                            && ($data[$key] === $value)) {
                            $line = $this->setDataInLine($line, $data);
                            $collection = $this->addLineToCollection($line, $collection);
                        }
                    }
                } else {
                    $collection = $this->addLineToCollection($line, $collection);
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
        $writer = $this->writer;
        $path   = $this->path . '.update';
        $reader->rewind();

        $writer->copy($path, true);
        $writer->truncate();

        // TODO: Implement update() method.
        //@reuse filter logic read read
        //@todo read and write into different files
        //@todo replace old file with new file
        while ($line = $reader()) {
            if ($this->isValidLine($line)) {
                if ($this->hasFilterById) {
                    if ($this->lineContainsId($line, $this->filterById)) {
                        $collection[$line[self::KEY_ID]] = $data;
                    } else {
                        $collection[$line[self::KEY_ID]] = (array) json_decode($line[self::KEY_DATA]);
                    }
                } else if ($this->hasFilters) {
                    $existingData = (array) json_decode($line[1]);
                    $foo = true;

                    foreach ($this->filters as $key => $filter) {
                        if ((isset($existingData[$key]))
                            && ($existingData[$key] === $filter)) {
                            $foo = true;
                            $collection[$line[self::KEY_ID]] = $data;
                        } else {
                            $foo = false;
                            break;
                        }
                    }

                    if ($foo) {
                        $collection[$line[self::KEY_ID]] = $data;
                    } else {
                        $collection[$line[self::KEY_ID]] = (array) json_decode($line[self::KEY_DATA]);
                    }
                } else {
                    $collection[$line[self::KEY_ID]] = $data;
                }
            }
        }
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

    /**
     * @param array $line
     * @param string $id
     * @return bool
     */
    private function lineContainsId(array $line, $id)
    {
        return ($line[self::KEY_ID] === $id);
    }

    /**
     * @param array $line
     * @param array $collection
     * @return array
     */
    private function addLineToCollection(array &$line, array &$collection)
    {
        $collection[$line[self::KEY_ID]] = $this->getDataFromLine($line);

        return $collection;
    }

    /**
     * @param array $line
     * @return array
     */
    private function getDataFromLine(array &$line)
    {
        return (array) json_decode($line[self::KEY_DATA]);
    }

    /**
     * @param array $line
     * @param array $data
     * @return array
     */
    private function setDataInLine(array &$line, array &$data)
    {
        $line[self::KEY_DATA] = json_encode($data);

        return $line;
    }

    /**
     * @param string $id
     * @param array $data
     * @return array
     */
    private function createLine($id, array $data)
    {
        $line = array(self::KEY_ID => $id);
        $line = $this->setDataInLine($line, $data);

        return $line;
    }
}