# Database File Storage for PHP

## [crud](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) [storage interface](https://github.com/stevleibelt/php_component_database_file_storage/blob/master/source/Leibelt/Stev/Database/FileStorage/FileStorageInterface.php)

```php
//separates single crud from list crud operations

//single object crud operations
public function create($data) : $uniqueIdentifier;
public function read($uniqueIdentifier = null, $data = null) : null|$data;
public function update($uniqueIdentifier, $data) : true|false;
public function delete($uniqueIdentifier) : true|false;

//collection crud operations
public function createList(array $dataList) : array;
public function readList(array $uniqueIdentifier = null, $data = null) : null|array;
public function updateList(array $uniqueIdentifierToDataList) : true|false;
public function deleteList(array $uniqueIdentifiers) : true|false;
```

## Future Improvements

* take a look to [bigdump](https://github.com/wires/bigdump)
* take a look to [reactphp/filesystem](https://github.com/reactphp/filesystem)
* take a look to [file wrapper](https://php.net/manual/en/wrappers.file.php)
* if write (create/read) action is triggered, create a *.lock file to prevent multiple writings
    * or usd [flock](https://php.net/manual/en/function.flock.php)
* implement caching via proxy
    * simple two files storages injected
    * if number of changed itemes reaches limit, cache is written into real storage
* use a uuid generator for unique keys
    * use a [temporary file](http://php.net/manual/en/function.tmpfile.php)
    * beeing even more pro, use [php://temp](http://php.net/manual/en/wrappers.php.php)
        * implement some kind of "intelligent" cache that counts the average size of an entry to determine the maximum number of entries before flushing the cache
