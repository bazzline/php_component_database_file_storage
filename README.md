# Database File Storage for PHP

## [crud](https://en.wikipedia.org/wiki/Create,_read,_update_and_delete) [storage interface](https://github.com/bazzline/php_component_database_file_storage/blob/master/source/Net/Bazzline/Database/FileStorage/StorageInterface.php)

```php
/**
 * @param $data
 * @return mixed - unique identifier
 */
public function create($data);

/**
 * @return array
 */
public function readMany();

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

/**
 * @param int $count
 * @param null|int $offset
 * @return $this
 */
public function limitBy($count, $offset = null);
```

# Install

## By Hand

    mkdir -p vendor/net_bazzline/php_component_database_file_storage
    cd vendor/net_bazzline/php_component_database_file_storage
    git clone https://github.com/bazzline/php_component_database_file_storage .

## With [Packagist](https://packagist.org/packages/net_bazzline/php_component_database_file_storage)

    composer require net_bazzline/php_component_database_file_storage:dev-master

# Benefits

* implemented [locking](https://packagist.org/packages/net_bazzline/component_lock) to prevent multiple reads and writes

# API

[API](http://www.bazzline.net/7ef9fafed9e60d5b861fe82e107b79e3f7adae0e/index.html) is available at [bazzline.net](http://www.bazzline.net).

## Future Improvements

* extend "filterBy"
    * $key and $value are optional to filter by key or by value
* take a look to [bigdump](https://github.com/wires/bigdump)
* take a look to [reactphp/filesystem](https://github.com/reactphp/filesystem)
* take a look to [file wrapper](https://php.net/manual/en/wrappers.file.php)
* implement "modifier" to easy up modifiying data before reading or writing
* if write (create/read) action is triggered, create a \*.lock file to prevent multiple writings
    * or usd [flock](https://php.net/manual/en/function.flock.php)
* implement caching via proxy
    * simple two files storages injected
    * if number of changed itemes reaches limit, cache is written into real storage
* use a uuid generator for unique keys
    * use a [temporary file](http://php.net/manual/en/function.tmpfile.php)
    * beeing even more pro, use [php://temp](http://php.net/manual/en/wrappers.php.php)
        * implement some kind of "intelligent" cache that counts the average size of an entry to determine the maximum number of entries before flushing the cache

# 
# History

* upcomming
    * @todo
    * added *API* section
* [0.6.5](https://github.com/bazzline/php_component_database_file_storage/tree/0.6.5) - released at 18.12.2015
    * updated dependencies
* [0.6.4](https://github.com/bazzline/php_component_database_file_storage/tree/0.6.4) - released at 11.12.2015
    * updated dependencies
* [0.6.3](https://github.com/bazzline/php_component_database_file_storage/tree/0.6.3) - released at 18.11.2015
    * updated dependencies
* [0.6.2](https://github.com/bazzline/php_component_database_file_storage/tree/0.6.2) - released at 13.09.2015
    * stabalized dependencies
* [0.6.1](https://github.com/bazzline/php_component_database_file_storage/tree/0.6.1) - released at 12.09.2015
    * added protected *getIdGenerator*, *getLockableWriterFactory*, *getReaderFactory* and *getStorage* methods to easy up creating your own storage by using the default factory
* [0.6.0](https://github.com/bazzline/php_component_database_file_storage/tree/0.6.0) - released at 12.09.2015
    * fixed issue in *readMany* if limit is used
* [0.5.0](https://github.com/bazzline/php_component_database_file_storage/tree/0.5.0) - released at 12.09.2015
    * added example *has*
    * added method *has($atLeast = 1, $atMost = null)*
    * added optional argument *resetRuntimeProperties* (*filters* and *limit* settings) to *create*, *readMany*, *readOne*, *update* and *delete*
* [0.4.1](https://github.com/bazzline/php_component_database_file_storage/tree/0.4.1) - released at 11.09.2015
    * adapted to new *Experiment* from toolbox
* [0.4.0](https://github.com/bazzline/php_component_database_file_storage/tree/0.4.0) - released at 11.09.2015
    * moved code into directories (*Storage* and *IdGenerator*)
    * renamed *Repository* to *Storage*
* [0.3.4](https://github.com/bazzline/php_component_database_file_storage/tree/0.3.4) - released at 10.09.2015
    * fixed issue in *LockableWriterForPhp5Dot3*
* [0.3.3](https://github.com/bazzline/php_component_database_file_storage/tree/0.3.3) - released at 10.09.2015
    * fixed issue in *LockableWriterForPhp5Dot3*
* [0.3.2](https://github.com/bazzline/php_component_database_file_storage/tree/0.3.2) - released at 10.09.2015
    * fixed issue when dealing with php 5.3
* [0.3.1](https://github.com/bazzline/php_component_database_file_storage/tree/0.3.1) - released at 10.09.2015
    * fixed broken unit test
* [0.3.0](https://github.com/bazzline/php_component_database_file_storage/tree/0.3.0) - released at 10.09.2015
    * created and used own `LockableWriter` to switch from "FileNameLock" to "FileHandlerLock"
