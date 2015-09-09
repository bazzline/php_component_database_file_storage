<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-09-09
 */
namespace Net\Bazzline\Component\Database\FileStorage;

use Net\Bazzline\Component\Csv\InvalidArgumentException;
use Net\Bazzline\Component\Csv\Writer\Writer;
use Net\Bazzline\Component\Csv\Writer\WriterInterface;
use Net\Bazzline\Component\Lock\FileHandlerLock;
use Net\Bazzline\Component\Toolbox\Process\Experiment;
use RuntimeException;

/**
 * Class Writer
 * @package Net\Bazzline\Component\Database\FileStorage\Storage
 */
class LockableWriter implements WriterInterface
{
    /** @var Experiment */
    private $experiment;

    /** @var FileHandlerLock */
    private $lock;

    /** @var Writer */
    private $writer;

    /**
     * @param Experiment $experiment
     */
    public function injectExperiment(Experiment $experiment)
    {
        $this->experiment = $experiment;

        if ($this->lock instanceof FileHandlerLock) {
            $this->setupExperiment($this->lock, $this->experiment);
        }
    }

    /**
     * @param FileHandlerLock $lock
     */
    public function injectLock(FileHandlerLock $lock)
    {
        $this->lock = $lock;

        if ($this->experiment instanceof Experiment) {
            $this->setupExperiment($this->lock, $this->experiment);
        }
    }

    /**
     * @param Writer $writer
     */
    public function injectWriter(Writer $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @throws RuntimeException
     */
    public function acquireLock()
    {
        $this->experiment->andFinallyStartTheExperiment();
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->lock->isLocked();
    }

    /**
     * @throws RuntimeException
     */
    public function releaseLock()
    {
        $this->lock->release();
    }

    //begin of WriterInterface
    /**
     * @return bool
     */
    public function hasHeadline()
    {
        return $this->writer->hasHeadline();
    }

    /**
     * @param string $delimiter
     * @throws InvalidArgumentException
     */
    public function setDelimiter($delimiter)
    {
        $this->writer->setDelimiter($delimiter);
    }

    /**
     * @param string $enclosure
     * @throws InvalidArgumentException
     */
    public function setEnclosure($enclosure)
    {
        $this->writer->setEnclosure($enclosure);
    }

    /**
     * @param string $escapeCharacter
     * @throws InvalidArgumentException
     */
    public function setEscapeCharacter($escapeCharacter)
    {
        $this->writer->setEscapeCharacter($escapeCharacter);
    }

    /**
     * @param string $path
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setPath($path)
    {
        return $this->writer->setPath($path);
    }

    /**
     * @param mixed|array $data
     * @return false|int
     */
    public function __invoke($data)
    {
        $writer = $this->writer;

        return $writer($data);
    }

    /**
     * @param string $path
     * @param bool $setPathAsCurrentPath
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function copy($path, $setPathAsCurrentPath = false)
    {
        return $this->writer->copy($path, $setPathAsCurrentPath);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function move($path)
    {
        return $this->writer->move($path);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return $this->writer->delete();
    }

    public function truncate()
    {
        $this->writer->truncate();
    }

    /**
     * truncates file and writes content
     *
     * @param array $collection
     * @return false|int
     */
    public function writeAll(array $collection)
    {
        return $this->writer->writeAll($collection);
    }

    /**
     * @param array $headlines
     * @return false|int
     */
    public function writeHeadlines(array $headlines)
    {
        return $this->writer->writeHeadlines($headlines);
    }

    /**
     * @param array $collection
     * @return false|int
     */
    public function writeMany(array $collection)
    {
        return $this->writer->writeMany($collection);
    }

    /**
     * @param string|array $data
     * @return false|int
     */
    public function writeOne($data)
    {
        return $this->writer->writeOne($data);
    }
    //end of WriterInterface

    private function setupExperiment(FileHandlerLock $lock, Experiment $experiment)
    {
        $experiment
            ->attempt(3)
            ->toExecute(function () use ($lock) {
                try {
                    $lock->acquire();
                    $wasSuccessful = true;
                } catch (RuntimeException $exception) {
                    $wasSuccessful = false;
                }

                return $wasSuccessful;
            })
            ->andWaitFor(300)
            ->orExecute(function () use ($lock) {
                $lock->acquire();
            });
    }
}