lib-database-init-bundle ![](https://travis-ci.org/paysera/lib-database-init-bundle.svg?branch=master)
========================

Initializes your database to needed state.
Exports your database. Can be configured to export different parts of the database (stricture, specific grouped tables).
Supports plain SQL queries and Doctrine Fixtures.

#### Installation
Install: `composer requre --dev paysera/lib-database-init-bundle`
Register:
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            // ...
            $bundles[] = new \Paysera\Bundle\DatabaseInitBundle\PayseraDatabaseInitBundle();
        }
        return $bundles;
    }
```

#### Configuration
In your `config_dev.yml` - change table names with corresponding tables in the project:
```yaml
paysera_database_init:
    directories:
        sql: 
            initial: &initial '%kernel.project_dir%/sql/initial'
            additional: &additional '%kernel.project_dir%/sql/additional'
        fixtures: 
            main: '%kernel.project_dir%/fixtures'
        structure: *initial
    exports:
        configuration:
            name: configuration
            tables:
                - config_table_1
                - config_table_2
            directory: *initial
        data:
            name: data
            invert_tables_from: configuration
            directory: *initial
        users:
            name: users
            tables:
                - users_table
            directory: *additional
        cards:
            name: cards
            tables:
                - card_table
            directory: *additional
```
- `paysera_database_init.directories.sql` (optional) - 
Will look for `*.sql` files in given directories, split each by lines, and execute each line.
Multi-line SQL statements should be separated by `;\n` characters.


- `paysera_database_init.exports` (optional) - 
Define different DatabaseExport configurations
    - `name` - will be used for the exported filename
    - `priority` - sets priority over the rest of the exports; it is also used as a prefix of the exported filename
    - `tables` - array of tables to be exported
    - `directory` - exported file will be placed in this directory

- `paysera_database_init.directories.fixtures` (optional) - 
Will load all fixtures in given directories to database.
Be aware that migrations should be executed before applying fixtures.

- `paysera_database_init.directories.structure` (required) - 
Structure will be exported in stated directory

- `invert_tables_from` (optional) -
Will ignore specified export configuration's tables. 
When used without specified `tables` it will export all tables in the db except `{invert_tables_from}.tables`

#### Run
`bin/console paysera:db-init:init {initializer} {set}`
* `initializer` - optional name of single initializer to run.
* List of provided initializers:
  - `sql`
  - `fixtures`
* `set` - optional name of given configuration, 
i.e. `initial` or `additional` in configuration example above.

`bin/console paysera:db-init:export {export_key}`
* `export_key` - optional name of single exporter to run.


#### Extension
Implement `\Paysera\Bundle\DatabaseInitBundle\Service\Initializer\DatabaseInitializerInterface`
and tag your service with `paysera_database_init.initializer`, provide `priority` tag attribute.

#### Run PHPUnit tests
`docker-compose up`

After that, run the tests from within the container: 

`docker exec -it lib-db-init vendor/bin/phpunit`
