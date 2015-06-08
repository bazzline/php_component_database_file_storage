# Database File Storage for PHP

## [crud](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) [storage interface](https://github.com/stevleibelt/php_component_database_file_storage/blob/master/source/Leibelt/Stev/Database/FileStorage/FileStorageInterface.php)

```php
/**
 * @param $data
 * @return mixed - unique identifier
 */
public function create($data);

/**
 * @return array
 */
public function read();

/**
 * @return null|mixed - nothing or data
 */
public function readOne();

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
```

## Future Improvements

* implement count
* extend "filterBy"
    * $key and $value are optional to filter by key or by value
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
