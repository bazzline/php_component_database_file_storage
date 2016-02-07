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
     * @param bool $resetRuntimeProperties
     * @return string - unique identifier
     */
    public function create(array $data, $resetRuntimeProperties = true);

    /**
     * @param bool $resetRuntimeProperties
     * @return array
     */
    public function readMany($resetRuntimeProperties = true);

    /**
     * @param bool $resetRuntimeProperties
     * @return null|mixed - nothing or data
     */
    public function readOne($resetRuntimeProperties = true);

    /**
     * @param array $data
     * @param bool $resetRuntimeProperties
     * @return boolean
     */
    public function update(array $data, $resetRuntimeProperties = true);

    /**
     * @param bool $resetRuntimeProperties
     * @return boolean
     */
    public function delete($resetRuntimeProperties = true);

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

    /**
     * @param int $atLeast
     * @param null|int $atMost
     * @return bool
     */
    public function has($atLeast = 1, $atMost = null);

    public function resetRuntimeProperties();
}
