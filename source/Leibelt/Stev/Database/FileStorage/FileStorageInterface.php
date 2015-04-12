<?php
/**
 * @author stev leibelt <artodeto@bazzline.net>
 * @since 2015-04-12 
 */

namespace Leibelt\Stev\Database\FileStorage;


interface FileStorageInterface
{
    /**
     * @param $data
     * @return mixed - unique identifier
     */
    public function create($data);

    /**
     * @param null|mixed $uniqueIdentifier
     * @param null|mixed $data
     * @return null|mixed - nothing or data
     */
    public function read($uniqueIdentifier = null, $data = null);

    /**
     * @param mixed $uniqueIdentifier
     * @param mixed $data
     * @return boolean
     */
    public function update($uniqueIdentifier, $data);

    /**
     * @param mixed $uniqueIdentifier
     * @return boolean
     */
    public function delete($uniqueIdentifier);

    /**
     * @param array $dataList
     * @return array
     */
    public function createList(array $dataList);

    /**
     * @param null|array $uniqueIdentifiers
     * @param null|mixed $data
     * @return null|mixed - nothing or array of data
     */
    public function readList(array $uniqueIdentifiers = null, $data);

    /**
     * @param array $uniqueIdentifierToDataList
     * @return boolean
     */
    public function updateList(array $uniqueIdentifierToDataList);

    /**
     * @param array $uniqueIdentifiers
     * @return boolean
     */
    public function deleteList(array $uniqueIdentifiers);
}