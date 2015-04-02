# Database File Storage for PHP

## Basic Interface (Draft)

```php
storage->create($data) : $uniqueIdentifier;
storage->read($uniqueIdentifier = null, $data) : $data|null;
storage->update($uniqueIdentifier, $data) : true|false;
storage->delete($uniqueIdentifier) : true|false;
```
