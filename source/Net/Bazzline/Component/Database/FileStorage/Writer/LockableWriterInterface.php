<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since: 2015-09-10
 */
namespace Net\Bazzline\Component\Database\FileStorage\Writer;

use Net\Bazzline\Component\Csv\Writer\WriterInterface;
use Net\Bazzline\Component\Lock\FileHandlerLock;
use Net\Bazzline\Component\Toolbox\Process\Experiment;
use RuntimeException;

/**
 * Interface LockableWriterInterface
 *
 * @package Net\Bazzline\Component\Database\FileStorage\Writer
 */
interface LockableWriterInterface extends WriterInterface
{
    /**
     * @param Experiment $experiment
     */
    public function injectExperiment(Experiment $experiment);

    /**
     * @param FileHandlerLock $lock
     */
    public function injectLock(FileHandlerLock $lock);

    /**
     * @throws RuntimeException
     */
    public function acquireLock();

    /**
     * @return bool
     */
    public function isLocked();

    /**
     * @throws RuntimeException
     */
    public function releaseLock();
}
