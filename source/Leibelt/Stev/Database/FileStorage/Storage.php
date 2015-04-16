<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Leibelt\Stev\Database\FileStorage;

use EasyCSV\Reader;
use EasyCSV\Writer;

class Storage implements FileStorageInterface
{
    /** @var array */
    private $filters;

    /** @var null|mixed */
    private $filterById;

    /** @var IdGeneratorInterface */
    private $generator;

    /** @var string */
    private $path;

    /** @var Reader */
    private $reader;

    /** @var Writer */
    private $writer;

    /**
     * @param string $path
     * @param IdGeneratorInterface
     * @throws InvalidArgumentException
     */
    public function __construct($path, IdGeneratorInterface $generator, Reader $reader, Writer $writer)
    {
        $this->generator    = $generator;
        $this->path         = (string) $path;
        $this->reader       = $reader;
        $this->writer       = $writer;

        $this->resetFilters();
        $this->createPathIfNotAvailable($this->path);
        $this->validatePath($this->path);
    }

    /**
     * @param mixed $data
     * @return mixed - unique identifier
     */
    public function create($data)
    {
        $id     = $this->generator->generate();
        $line   = array($id, json_encode($data));

        $this->writer->writeRow($line);
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

        if (!$this->hasFilterById()
            && !$this->hasFilters()) {
            while ($line = $this->reader->getRow()) {
                $collection[$line[0]] = json_decode($line[1]);
            }
        } else {
            while ($line = $this->reader->getRow()) {
                if ($this->hasFilterById()) {
                    if ($line[0] === $this->filterById) {
                        $data = json_decode($line[1]);
                        break;
                    }
                }
                if ($this->hasFilters()) {
                    $entry = json_decode($line[1]);

                    foreach ($this->filters as $key => $filter) {
                        //@todo
                    }
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
        //@reuse filter logic read read
        //@todo read and write into different files
        //@todo replace old file with new file
        $this->resetFilters();
    }

    /**
     * @param mixed $data
     * @return boolean
     */
    public function update($data)
    {
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
     */
    public function filterBy($key, $value)
    {
        $this->filters[$key] = $value;

        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function filterById($id)
    {
        $this->filterById = $id;

        return $this;
    }

    /**
     * @param string $path
     * @throws InvalidArgumentException
     */
    private function createPathIfNotAvailable($path)
    {
        if (!is_dir($path)) {
            $couldNotBeCreated = (!mkdir($path, 0755, true));

            if ($couldNotBeCreated) {
                $message = 'could not create directory "' . $path . '""';

                throw new InvalidArgumentException($message);
            }
        }
    }

    /**
     * @return bool
     */
    private function hasFilters()
    {
        return (!empty($this->filters));
    }

    /**
     * @return bool
     */
    private function hasFilterById()
    {
        return (!is_null($this->filterById));
    }

    private function resetFilters()
    {
        $this->filters      = array();
        $this->filterById   = null;
    }

    /**
     * @param string $path
     * @throws InvalidArgumentException
     */
    private function validatePath($path)
    {
        if (!is_writable($path)) {
            $message = 'directory "' . $path . '" is not writable';

            throw new InvalidArgumentException($message);
        }
    }
}