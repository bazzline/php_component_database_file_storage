<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Component\Database\FileStorage\Storage;

use Net\Bazzline\Component\Database\FileStorage\IdGenerator\IdGeneratorInterface;
use Net\Bazzline\Component\Database\FileStorage\InvalidArgumentException;
use Net\Bazzline\Component\Database\FileStorage\RuntimeException;
use Net\Bazzline\Component\Csv\Reader\Reader;
use Net\Bazzline\Component\Database\FileStorage\Writer\LockableWriterInterface;

/**
 * Class Storage
 *
 * @package Net\Bazzline\Component\Database\FileStorage\Storage
 */
class Storage implements StorageInterface
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

    /** @var LockableWriterInterface */
    private $writer;

    public function __construct()
    {
        $this->resetRuntimeProperties();
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
     * @throws InvalidArgumentException|RuntimeException
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
     * @param LockableWriterInterface $writer
     * @return $this
     */
    public function injectWriter(LockableWriterInterface $writer)
    {
        $this->writer = $writer;

        if (!is_null($this->path)) {
            $this->writer->setPath($this->path);
        }

        return $this;
    }

    /**
     * @param array $data
     * @param bool $resetRuntimeProperties
     * @return string - unique identifier
     */
    public function create(array $data, $resetRuntimeProperties = true)
    {
        $id     = $this->generator->generate();
        $line   = $this->createLine($id, $data);
        $writer = $this->writer;

        $this->acquireLock();
        $writer($line);
        $this->releaseLockIfNeeded();

        $this->resetRuntimePropertiesIfNeeded($resetRuntimeProperties);

        return $id;
    }



    /**
     * @param bool $resetRuntimeProperties
     * @return array
     * @todo
     */
    public function readMany($resetRuntimeProperties = true)
    {
        $collection = [];
        $reader     = $this->reader;
        $reader->rewind();

        if ($this->hasOffset()) {
            $reader = $this->seekReaderToOffset($reader, $this->offset);
        }

        $iterator   = ($this->hasLimit()) ? $this->limit : -1;

        while ($line = $reader()) {
            $isCountableLine = false;

            if ($this->isValidLine($line)) {
                if ($this->hasFilterById) {
                    if ($this->lineHasId($line, $this->filterById)) {
                        $collection = $this->addLineToCollection($line, $collection);
                        break;
                    }
                } else if ($this->hasFilters) {
                    if ($this->lineHasFilters($line, $this->filters)) {
                        $collection         = $this->addLineToCollection($line, $collection);
                        $isCountableLine    = true;
                    }
                } else {
                    $collection         = $this->addLineToCollection($line, $collection);
                    $isCountableLine    = true;
                }
            }

            if ($isCountableLine) {
                --$iterator;
                if ($iterator === 0) {
                    break;
                }
            }
        }

        $this->resetRuntimePropertiesIfNeeded($resetRuntimeProperties);

        return $collection;
    }



    /**
     * @param bool $resetRuntimeProperties
     * @return null|mixed - nothing or data
     */
    public function readOne($resetRuntimeProperties = true)
    {
        $this->limitBy(1);
        $collection = $this->readMany();
        $this->resetRuntimePropertiesIfNeeded($resetRuntimeProperties);

        return $collection;
    }

    /**
     * @param bool $resetRuntimeProperties
     * @param array $data
     * @return boolean
     */
    public function update(array $data, $resetRuntimeProperties = true)
    {
        return $this->updateOrDelete($data, $resetRuntimeProperties);
    }

    /**
     * @param bool $resetRuntimeProperties
     * @return boolean
     */
    public function delete($resetRuntimeProperties = true)
    {
        return $this->updateOrDelete(null, $resetRuntimeProperties);
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
     * @param int $atLeast
     * @param null|int $atMost
     * @return bool
     */
    public function has($atLeast = 1, $atMost = null)
    {
        if (is_null($atMost)) {
            $this->limitBy($atLeast);
        }

        $numberOfEntries = count($this->readMany(false));

        if (is_null($atMost)) {
            $has = ($numberOfEntries >= $atLeast);
        } else {
            $has = (($numberOfEntries >= $atLeast)
                && ($numberOfEntries <= $atMost));
        }

        return $has;
    }

    public function resetRuntimeProperties()
    {
        $this->filters          = [];
        $this->filterById       = null;
        $this->hasFilterById    = false;
        $this->hasFilters       = false;
        $this->limit            = null;
        $this->offset           = null;
    }

    /**
     * @param array $data - null triggers a deletion of fitting lines
     * @param bool $resetRuntimeProperties
     * @return bool
     */
    private function updateOrDelete(array $data = null, $resetRuntimeProperties = true)
    {
        $collection     = [];
        $delete         = (is_null($data));
        $wasSuccessful  = true;
        $path           = $this->path . ($delete ? '.delete' : '.update');
        $reader         = $this->reader;
        $writer         = $this->writer;
        $reader->rewind();

        $writer->copy($path, true);
        $this->acquireLock();
        $writer->truncate();

        if ($this->hasOffset()) {
            $reader = $this->seekReaderToOffset($reader, $this->offset);
        }

        $iterator = ($this->hasLimit()) ? $this->limit : -1;

        while ($line = $reader()) {
            if ($this->isValidLine($line)) {
                if ($this->hasFilterById) {
                    $addLine = true;
                    if ($this->lineHasId($line, $this->filterById)) {
                        if ($delete) {
                            $addLine = false;
                        } else {
                            $line = $this->setDataInLine($line, $data);
                        }
                    }
                } else if ($this->hasFilters) {
                    $addLine = true;
                    if ($this->lineHasFilters($line, $this->filters)) {
                        if ($delete) {
                            $addLine = false;
                        } else {
                            $line = $this->setDataInLine($line, $data);
                        }
                    }
                } else {
                    $addLine = (!$delete);
                }

                if ($addLine) {
                    $collection = $this->addLineToCollection($line, $collection);
                }
                --$iterator;
                if ($iterator === 0) {
                    break;
                }
            }
        }

        foreach ($collection as $id => $data) {
            $line = $this->createLine($id, $data);

            if ($writer($line) == false) {
                $wasSuccessful = false;
                break;
            } else {
                $wasSuccessful = true;
            }
        }

        $this->releaseLockIfNeeded();
        if ($wasSuccessful) {
            $writer->copy($this->path, true);
            unlink($path);
        } else {
            $writer->setPath($this->path);
        }

        $this->resetRuntimePropertiesIfNeeded($resetRuntimeProperties);

        return $wasSuccessful;
    }

    /**
     * @param string $path
     * @throws InvalidArgumentException
     */
    private function createPathIfNotAvailable($path)
    {
        if (!is_dir($path)) {
            //@todo replace by command if needed
            //http://php.net/manual/en/function.clearstatcache.php
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

    /**
     * @param bool|true $isNeeded
     */
    private function resetRuntimePropertiesIfNeeded($isNeeded = true)
    {
        if ($isNeeded) {
            $this->resetRuntimeProperties();
        }
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
    private function lineHasId(array $line, $id)
    {
        return ($line[self::KEY_ID] === $id);
    }

    /**
     * @param array $line
     * @param array $filters
     * @return bool
     */
    private function lineHasFilters(array &$line, array &$filters)
    {
        $lineHasFilters = false;
        $data = $this->getDataFromLine($line);

        foreach ($filters as $key => $value) {
            if ((isset($data[$key]))
                && ($data[$key] === $value)) {
                $lineHasFilters = true;
            } else {
                $lineHasFilters = false;
                break;
            }
        }

        return $lineHasFilters;
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
        $line = [
            self::KEY_ID => $id
        ];
        $line = $this->setDataInLine($line, $data);

        return $line;
    }

    /**
     * @throws RuntimeException
     */
    private function acquireLock()
    {
        $this->writer->acquireLock();
    }

    /**
     * @throws RuntimeException
     */
    private function releaseLockIfNeeded()
    {
        if ($this->writer->isLocked()) {
            $this->writer->releaseLock();
        }
    }
}
