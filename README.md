# Database File Storage for PHP

## Basic Interface (Draft)

```php
//single object crud
$storage->create($data) : $uniqueIdentifier;
$storage->read($uniqueIdentifier = null, $data) : $data|null;
$storage->update($uniqueIdentifier, $data) : true|false;
$storage->delete($uniqueIdentifier) : true|false;

//collection crud
$storage->createList($data) : array($uniqueIdentifier);
$storage->readList(array $uniqueIdentifier = null, $data) : $data|array;
$storage->updateList(array $uniqueIdentifier, $data) : true|false;
$storage->deleteList(array $uniqueIdentifier) : true|false;
```

## Thoughts

* if write (create/read) action is triggered, create a *.lock file to prevent multiple writings
* implement caching via proxy
    * simple two files storages injected
    * if number of changed itemes reaches limit, cache is written into real storage
* use a uuid generator for unique keys
    * use a [temporary file](http://php.net/manual/en/function.tmpfile.php)
    * beeing even more pro, use [php://temp](http://php.net/manual/en/wrappers.php.php)
        * implement some kind of "intelligent" cache that counts the average size of an entry to determine the maximum number of entries before flushing the cache
