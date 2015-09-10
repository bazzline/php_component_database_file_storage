<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since: 2015-09-10
 */
namespace Net\Bazzline\Component\Database\FileStorage\Writer;

use Net\Bazzline\Component\Csv\Writer\WriterForPhp5Dot3;
use Net\Bazzline\Component\Lock\FileHandlerLock;
use Net\Bazzline\Component\Toolbox\Process\Experiment;
use RuntimeException;

/**
 * Class LockableWriterForPhp5Dot3

 *
*@package Net\Bazzline\Component\Database\FileStorage\Writer
 */
class LockableWriterForPhp5Dot3 extends WriterForPhp5Dot3 implements LockableWriterInterface
{
    /** @var Experiment */
    private $experiment;

    /** @var FileHandlerLock */
    private $lock;

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
     * @throws RuntimeException
     */
    public function acquireLock()
    {
        $this->bindLock($this->lock);
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

    /**
     * @param FileHandlerLock $lock
     */
    private function bindLock(FileHandlerLock $lock)
    {
        $lock->setResource($this->getFileHandler());
    }

    /**
     * @param FileHandlerLock $lock
     * @param Experiment $experiment
     */
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
