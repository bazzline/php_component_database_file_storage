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
