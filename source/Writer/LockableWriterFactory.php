<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since: 2015-09-10
 */
namespace Net\Bazzline\Component\Database\FileStorage\Writer;

use Net\Bazzline\Component\Csv\Writer\WriterFactory;
use Net\Bazzline\Component\Lock\FileHandlerLock;
use Net\Bazzline\Component\Toolbox\Process\Experiment;

/**
 * Class LockableWriterFactory
 *
 * @package Net\Bazzline\Component\Database\FileStorage\Writer
 */
class LockableWriterFactory extends WriterFactory
{
    /**
     * @return LockableWriterInterface
     */
    public function create()
    {
        $experiment = new Experiment();
        $lock       = new FileHandlerLock();
        $writer     = parent::create();

        /** @var LockableWriterInterface $writer */
        $writer->injectExperiment($experiment);
        $writer->injectLock($lock);

        return $writer;
    }

    /**
     * @return LockableWriterInterface
     */
    protected function getWriter()
    {
        if ($this->phpVersionLessThen5Dot4()) {
            $writer = new LockableWriterForPhp5Dot3();
        } else {
            $writer = new LockableWriter();
        }

        return $writer;
    }
}
