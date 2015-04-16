<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Leibelt\Stev\Database\FileStorage;

interface FileStorageInterface
{
    /**
     * @param mixed $data
     * @return mixed - unique identifier
     */
    public function create($data);

    /**
     * @return null|mixed - nothing or data
     */
    public function read();

    /**
     * @param mixed $data
     * @return boolean
     */
    public function update($data);

    /**
     * @return boolean
     */
    public function delete();

    /**
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function filterBy($key, $value);

    /**
     * @param mixed $id
     * @return $this
     */
    public function filterById($id);
}