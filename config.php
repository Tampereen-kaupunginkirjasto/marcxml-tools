<?php

/**
 * Config file for marc.php
 *
 * tag:         A tag attribute of a datafield
 * code:        A code attribute of a subfieldfield
 * condition:   A valid regular expression
 * remove:      Remove only specific subfieldfield or a whole datafield. Keys: subfield, datafield
 *
 * If condition is array(), then the field is always removed. But if the condition
 * has more than one value, then the field content is matched against the items in it
 * using XPath contains()-function. And if it matches, then the field is removed.
 *
 * The remove key indicates what is removed. If there's `subfield` keyword specified,
 * then only subfieldfield is removed, but otherwise the whole datafield (with specified
 * tag-attribute) is removed.
 *
 * Note, that this version is not the latest and it probably won't work.
 */
$config = array(

    // Aikamääreet
    array(
        'tag'       => '100',
        'code'      => 'd',
        'condition' => array(),
        'remove'    => 'subfield'
    ),
    array(
        'tag'       => '600',
        'code'      => 'd',
        'condition' => array(),
        'remove'    => 'subfield'
    ),
    array(
        'tag'       => '700',
        'code'      => 'd',
        'condition' => array(),
        'remove'    => 'subfield'
    ),
    array(
        'tag'       => '800',
        'code'      => 'd',
        'condition' => array(),
        'remove'    => 'subfield'
    ),
    array(
        'tag'       => '900',
        'code'      => 'd',
        'condition' => array(),
        'remove'    => 'subfield'
    ),


    // Arvioitu ilmestymisaika
    array(
        'tag'       => '263',
        'code'      => 'a',
        'condition' => array(),
        'remove'    => 'subfield'
    ),

    // Hankinnan huomautuksia
    array(
        'tag'       => '591',
        'code'      => 'a',
        'condition' => array(),
        'remove'    => 'subfield'
    ),

    // Linkkikentät
    array(
        'tag'       => '856',
        'code'      => 'u',
        'condition' => array(
            'kirjavalitys.fi',
            'btj.com'
        ),
        'remove'    => 'datafield'
    ),

    // Muuta
    array(
        'tag'       => '971',
        'code'      => 'u',
        'condition' => array(
            'kirjavalitys.fi',
            'btj.com'
        ),
        'remove'    => 'datafield'
    ),
    array(
        'tag'       => '852',
        'code'      => 'u',
        'condition' => array(
            'kirjavalitys.fi',
            'btj.com'
        ),
        'remove'    => 'subfield'
    ),

    // Näistäkin löytyi Kirjavälityksen linkkejä, tosin vain yksi kpl kumpaakin
    array(
        'tag'       => '900',
        'code'      => 'e,f,g,h,p,u',
        'condition' => array(
            'kirjavalitys.fi',
            'btj.com'
        ),
        'remove'    => 'datafield'
    ),
    array(
        'tag'       => '908',
        'code'      => 'u',
        'condition' => array(
            'kirjavalitys.fi',
            'btj.com'
        ),
        'remove'    => 'datafield'
    ),
);

return $config;
