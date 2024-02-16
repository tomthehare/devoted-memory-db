# devoted-memory-db

## Overview
This repository contains code that runs an in-memory database.
Given that it's an in-memory database, the state will only last as long as the process is running.

## Quickstart

This solution assumes you have docker desktop installed.
If you do not, you can do so by visiting the [docker site](https://www.docker.com/products/docker-desktop/). 
I thought using docker would be easier than trying to make you install PHP on whatever environment you are testing with!

Run the following command from within the root folder of this repo to start a new database:

```bash
docker compose run --rm memory-db
```

## Supported Commands

### `SET [name] [value]`
The `SET` command sets a key `[name]` in the database to a provided `[value]`. 
If the `[name]` is not present, a new key will be set. 
If the `[name]` was already present, the `[value]` will overwrite whatever the previous was.

### `GET [name]`
The `GET` command retrieves the value assigned to `[name]` from the database.  
If there is no value assigned to `[name]`, it will return `NULL`.

### `DELETE [name]`
The `DELETE` command unsets a `[name]` in the database. 
If there is no `[name]` present in the database, it is a no-op.

### `COUNT [value]`
The `COUNT` command returns the number of names that have a given `[value]` assigned to them. 
If there are no names with `[value]` assigned, it will return 0.

### `END`
The `END` command terminates the database session.

### `BEGIN` 
The `BEGIN` command instructs the database to start a new transaction scope. 
Nested transactions are supported. 

All commands following the `BEGIN` command will be grouped together to be executed when the transaction is committed.

### `ROLLBACK`
The `ROLLBACK` command instructs the database to drop the existing transaction scope and all commands contained within. 
If there is no open transaction scope, it will print `TRANSACTION NOT FOUND`
If there is no open transaction scope, the command is a no-op.

### `COMMIT` 
The `COMMIT` command instructs the database to apply all commands that occured between it and the `BEGIN` command, in order. 
If there is no open transaction scope, the command is a no-op.

### `EXIT`
The `EXIT` command will terminate the database session gracefully.

## Running Tests

To run tests, run the following commands:

```bash
docker compose run --rm memory-db-dev
composer install
php vendor/bin/phpunit tests/Unit --testdox --colors=always
```
The way the docker volumes are set up, if you don't have the `vendor` dir already, you'll lose the one that was created during build. 
This is why we run `composer install` - just to make sure its there.