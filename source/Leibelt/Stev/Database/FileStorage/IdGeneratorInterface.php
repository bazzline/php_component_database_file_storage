<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Leibelt\Stev\Database\FileStorage;

interface IdGeneratorInterface
{
    /**
     * @return mixed
     */
    public function generate();
}