<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-06-11
 */

namespace Test\Net\Bazzline\Component\Database\FileStorage\IdGenerator;

use Test\Net\Bazzline\Component\Database\FileStorage\FileStorageTestCase;

/**
 * Class UUIDGeneratorTest
 *
 * @package Test\Net\Bazzline\Component\Database\FileStorage\IdGenerator
 */
class UUIDGeneratorTest extends FileStorageTestCase
{
    public function testGenerate()
    {
        $generator  = $this->getNewUUIDGenerator();
        $uuid       = $generator->generate();

        $this->assertTrue(is_string($uuid));
        $this->assertEquals(36, strlen($uuid));
    }
}
