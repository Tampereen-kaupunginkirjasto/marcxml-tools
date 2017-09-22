# MarcXML-tools

Tools, that were originally used to work with the data dump of [PIKI](https://piki.verkkokirjasto.fi/web/arena)-libraries.

Currently, it's being developed further and it can (hopefully) be used with other MARXML-formatted data, too.

The idea is that you can write event listeners, which has access to each record in the dataset, one at the time, and extract whatever information you need.

There's currently one event listener available, which collects keywords from datafield 650 (and subfield-codes 2, a, x) and logs the details usnig Monolog.

Using Monolog is nice thing since you can wire up multiple log handlers, for  example RedisHandler or MongoDBHandler and process the results elsewhere.

The keyword listener that comes with the app serves as an example, what you could do.

## Requirements

- Git (http://git-scm.org) (optional, you can download the ZIP from Github)
- Composer (http://getcomposer.org)
- Code editor
- PHP (tested at least with 7.1.9 on Ubuntu; see composer.json for other dependencies)
- Some coding skills to change what's needed

## Usage instructions

1. Clone the `marcxml-tools`-repository
2. Run `composer install`
3. Download and extract the data dump
4. Edit the `config.json` to suit your needs (optional)
5. Execute `php mx.php path/to/data`
