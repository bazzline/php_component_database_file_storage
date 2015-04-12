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

        $this->createPathIfNotAvailable($this->path);
        $this->validatePath($this->path);
    }

    /**
     * @param $data
     * @return mixed - unique identifier
     */
    public function create($data)
    {
        $id     = $this->generator->generate();
        $line   = array($id, json_encode($data));

        $this->writer->writeRow($line);

        return $id;
    }

    /**
     * @param null|mixed $uniqueIdentifier
     * @param null|mixed $data
     * @return null|mixed - nothing or data
     * @todo implement support/usage of $data
     */
    public function read($uniqueIdentifier = null, $data = null)
    {
        $data = null;

        while ($line = $this->reader->getRow()) {
            if ($line[0] === $uniqueIdentifier) {
                $data = json_decode($line[1]);
                break;
            }
        }

        return $data;
    }

    /**
     * @param mixed $uniqueIdentifier
     * @param mixed $data
     * @return boolean
     * @todo
     */
    public function update($uniqueIdentifier, $data)
    {
        $couldBeUpdated = false;

        //read and write into separate file
        $fileData = $this->reader->getAll();

        //@todo implement "empty file"
        //$this->writer->
        foreach ($fileData as $line) {

        }
        while ($line = $this->reader->getRow()) {
            if ($line[0] === $uniqueIdentifier) {
                $couldBeUpdated = json_decode($line[1]);
                break;
            }
        }

        return $couldBeUpdated;
    }

    /**
     * @param mixed $uniqueIdentifier
     * @return boolean
     */
    public function delete($uniqueIdentifier)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param array $dataList
     * @return array
     */
    public function createList(array $dataList)
    {
        // TODO: Implement createList() method.
    }

    /**
     * @param null|array $uniqueIdentifiers
     * @param null|mixed $data
     * @return null|mixed - nothing or array of data
     */
    public function readList(array $uniqueIdentifiers = null, $data)
    {
        // TODO: Implement readList() method.
    }

    /**
     * @param array $uniqueIdentifierToDataList
     * @return boolean
     */
    public function updateList(array $uniqueIdentifierToDataList)
    {
        // TODO: Implement updateList() method.
    }

    /**
     * @param array $uniqueIdentifiers
     * @return boolean
     */
    public function deleteList(array $uniqueIdentifiers)
    {
        // TODO: Implement deleteList() method.
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