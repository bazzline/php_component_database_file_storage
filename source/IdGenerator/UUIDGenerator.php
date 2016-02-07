<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Component\Database\FileStorage\IdGenerator;

use Ramsey\Uuid\Uuid;

/**
 * Class UUIDGenerator
 *
 * @package Net\Bazzline\Component\Database\FileStorage\IdGenerator
 */
class UUIDGenerator implements IdGeneratorInterface
{
    /**
     * @return mixed
     */
    public function generate()
    {
        return Uuid::uuid4()->toString();
    }
}
