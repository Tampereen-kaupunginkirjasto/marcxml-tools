# marcxml-tools

Työkaluja, joita käytettiin PIKI-kirjastojen tietokantadumpin käsittelyssä.

## Käyttöohjeet

1. Kloonaa Githubista marcxml-tools-repository
2. Lataa ja pura MARCXML-muotoinen tietokantadumppi data/dump-hakemistoon
3. Muokkaa runner.php-tiedostoa ja lisää registerAnalyzers()-kutsuun haluamasi analysoijaluokan instanssi (kts. esimerkki alla)
4. Suorita seuraava komento: php runner.php tai php runner.php | tee -a logs/my-log.log

### Esimerkki analysoijan käytöstä

    $analytic = new Analytic;
    $analytic->registerAnalyzers(array(
        new \PIKI\MARCXML\Analyzer\K653Analyzer
    ));

### Saatavilla olevat analysoijat

- \PIKI\MARCXML\Analyzer\K650Analyzer - 650$a ja 650$x + sanasto 650$2
- \PIKI\MARCXML\Analyzer\K653Analyzer - 653$a
- \PIKI\MARCXML\Analyzer\FITamPublishYearAnalyzer - 852$a sisältää sanan `tam`

