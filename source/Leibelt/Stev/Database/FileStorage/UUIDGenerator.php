<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Leibelt\Stev\Database\FileStorage;

use Rhumsaa\Uuid\Uuid;

class UUIDGenerator implements IdGeneratorInterface
{
    /**
     * @return mixed
     */
    public function generate()
    {
        return Uuid::uuid5(UUID::NAMESPACE_DNS, 'leibelt.de');
    }
}