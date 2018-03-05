lib-database-init-bundle ![](https://travis-ci.org/paysera/lib-database-init-bundle.svg?branch=master)
========================

Initializes your database to needed state.
Supports plain SQL queries and Doctrine Fixtures

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
In your `config_dev.yml`:
```yaml
paysera_database_init:
    directories:
        sql: '%kernel.root_dir%/Sql'
        fixtures: '%kernel.root_dir%/Fixtures'
```
- `paysera_database_init.directories.sql` (optional) - 
Will look for `*.sql` files, split each by lines, and execute each line.
Multi-line SQL statements currently not supported.


- `paysera_database_init.directories.fixtures` (optional) - 
Will load all fixtures in given directory to database.
Be aware that migrations should be executed before applying fixtures.

#### Run
`bin/console paysera:db-init:init`


#### Extension
Implement `\Paysera\Bundle\DatabaseInitBundle\Service\Initializer\DatabaseInitializerInterface`
and tag your service with `paysera_database_init.initializer`, provide `priority` tag attribute.