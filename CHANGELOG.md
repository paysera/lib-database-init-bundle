# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

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
