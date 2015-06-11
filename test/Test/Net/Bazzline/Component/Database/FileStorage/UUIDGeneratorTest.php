<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage;

use Net\Bazzline\Component\Database\FileStorage\UUIDGenerator;

class UUIDGeneratorTest extends FileStorageTestCase
{
    public function testGenerate()
    {
        $generator  = $this->getNewUUIDGenerator();
        $uuid       = $generator->generate();

        $this->assertTrue(is_string($uuid));
    }
}