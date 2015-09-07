<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Component\Database\FileStorage;

use Ramsey\Uuid\Uuid;

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