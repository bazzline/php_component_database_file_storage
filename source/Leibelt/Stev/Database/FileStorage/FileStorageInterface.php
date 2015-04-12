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
     * @param mixed $uniqueIdentifier
     * @return null|mixed - nothing or data
     */
    public function read($uniqueIdentifier);

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
     * @param mixed $data
     * @return null|mixed - nothing or data
     */
    public function search($data);

    /**
     * @param array $dataList
     * @return array
     */
    public function createList(array $dataList);

    /**
     * @param array $uniqueIdentifiers
     * @return null|mixed - nothing or array of data
     */
    public function readList(array $uniqueIdentifiers);

    /**
     * @param array $uniqueIdentifierToDataList
     * @return boolean
     */
    public function updateList(array $uniqueIdentifierToDataList);

    /**
     * @param array $uniqueIdentifierToDataList
     * @return boolean
     */
    public function deleteList(array $uniqueIdentifierToDataList);

    /**
     * @param mixed $data
     * @return null|array - nothing or collection of data
     */
    public function searchList($data);
}