# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.1.0
 - Added
    - Support for Symfony 4
 - Removed
    - Support for Symfony 2

## 2.0.0
 - Added
    - Sql export features - exports can be configured by groups of tables and/or by specifying an `invert` export group 
    (tables in specified group will be excluded from the export)
    - A new config property is required now: `paysera_database_init.directories.structure` - 
    it specifies were would be placed database structure sqls
```yaml
paysera_database_init:
     directories:
         sql:
             initial: &initial '%kernel.root_dir%/sql/initial'
             other: &other '%kernel.root_dir%/sql/other'
         fixtures:
             main: '%kernel.root_dir%/Fixtures'
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
```
 - Changed
    - Initializer priorities are now handled for lowest to highest number. Due to this, default priorities are changed as well.
    - DatabaseInitializerInterface:initialize now requires initializer name to be passed. 
## 1.0.0
- Added
  - Support of 2nd argument: `bin/console paysera:db-init:init {initializer} {set}`
- Changed
  - Bundle configuration now accepts key-value sets for directories. Key should be used as `set` name above:
```yaml
paysera_database_init:
    directories:
        sql:
            initial: '%kernel.root_dir%/sql/initial'
            other: '%kernel.root_dir%/sql/other'
        fixtures:
            main: '%kernel.root_dir%/Fixtures'
```

## 0.2.0
- Added
  - Support of specific single initializer to run: `bin/console paysera:db-init:init sql` - will execute only `SqlInitializer`.
  If no argument is specified, all initializers will be executed.

## 0.1.0
- Added
  - `InitializationReport` and `InitializationMessage` - command now prints useful info. 

## 0.0.1
- Initial release
