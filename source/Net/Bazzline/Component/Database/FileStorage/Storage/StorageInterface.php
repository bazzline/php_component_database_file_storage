<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Net\Bazzline\Component\Database\FileStorage\Storage;

/**
 * Interface StorageInterface
 *
 * @package Net\Bazzline\Component\Database\FileStorage\Storage
 */
interface StorageInterface
{
    /**
     * @param array $data
     * @return string - unique identifier
     */
    public function create(array $data);

    /**
     * @return array
     */
    public function readMany();

    /**
     * @return null|mixed - nothing or data
     */
    public function readOne();

    /**
     * @param array $data
     * @return boolean
     */
    public function update(array $data);

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

    /**
     * @param int $count
     * @param null|int $offset
     * @return $this
     */
    public function limitBy($count, $offset = null);
}
