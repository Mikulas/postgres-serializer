# Features

- [`PostgresArray`](#postgresarray)
- [`CompositeType`](#compositetype)

[Api documentation](https://codedoc.pub/Mikulas/postgres-serializer/master/index.html)

## `PostgresArray`

Adds support for [PostgreSQL array types](http://www.postgresql.org/docs/9.4/static/arrays.html)

Depending on your desired behaviour, `null` can either be cast to empty array, or be left as `null`.

- [`PostgresArray`](https://codedoc.pub/Mikulas/postgres-serializer/master/class-Mikulas.PostgresSerializer.PostgresArray.html)

### `PostgresArray` Usage
```php
use Mikulas\PostgresSerializer\PostgresArray;

PostgresArray::serialize(['foo', NULL, 'bar']);
PostgresArray::parse('{"foo",NULL,"bar"}');
```

## `CompositeType`

Parses PostgreSQL [composite types](http://www.postgresql.org/docs/current/static/rowtypes.html) into php hash.

- [`CompositeType`](https://codedoc.pub/Mikulas/postgres-serializer/master/class-Mikulas.PostgresSerializer.CompositeType.html)

### `CompositeType` Usage

```php
use Mikulas\PostgresSerializer\CompositeType;

CompositeType::serialize([['lat', 'lng'], 'radius']);
CompositeType::parse('((lat,lng),radius)');
```
