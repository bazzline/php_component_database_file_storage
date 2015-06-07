<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Database\FileStorage;

use Rhumsaa\Uuid\Uuid;

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