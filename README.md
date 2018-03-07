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
        sql: 
            initial: '%kernel.root_dir%/sql/initial'
            additional: '%kernel.root_dir%/sql/additional'
        fixtures: 
            main: '%kernel.root_dir%/fixtures'
```
- `paysera_database_init.directories.sql` (optional) - 
Will look for `*.sql` files in given directories, split each by lines, and execute each line.
Multi-line SQL statements should be separated by `;\n` characters.


- `paysera_database_init.directories.fixtures` (optional) - 
Will load all fixtures in given directories to database.
Be aware that migrations should be executed before applying fixtures.

#### Run
`bin/console paysera:db-init:init {initializer} {set}`
* `initializer` - optional name of single initializer to run.
* List of provided initializers:
  - `sql`
  - `fixtures`
* `set` - optional name of given configuration, 
i.e. `initial` or `additional` in configuration example above.


#### Extension
Implement `\Paysera\Bundle\DatabaseInitBundle\Service\Initializer\DatabaseInitializerInterface`
and tag your service with `paysera_database_init.initializer`, provide `priority` tag attribute.
