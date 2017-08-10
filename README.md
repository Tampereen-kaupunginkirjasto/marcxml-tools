# MarcXML-tools

Tools, that were used to work with the data dump of [PIKI](https://piki.verkkokirjasto.fi/web/arena)-libraries.

## Requirements

- Git (http://git-scm.org)
- Composer (http://getcomposer.org)
- Editor, eg. Notepad++
- PHP (XMLWriter, XMLReader, DOM)

## Usage instructions

1. Clone the `marcxml-tools`-repository
2. Run `composer update` or `composer install`
3. Extract the data dump
4. Edit the `runner.php`-script to suit your needs
5. Execute `php runner.php`

### An example

```PHP
$analytic = new Analytic;
$analytic->registerAnalyzers(array(
    new \PIKI\MARCXML\Analyzer\K653Analyzer
));
```

### Available analyzers

| Class name   | Related MarcXML-tags
| ------------ | ----
| K650Analyzer | 650$a ja 650$x + vocabluary 650$2
| K653Analyzer | 653$a
| FITamPublishYearAnalyzer | 852$a contains (partial) word *tam*
