<?php

/**
 * This php file tries to load a serialized Criteria, to unserialize it and make the table to model class relation
 * known through `Propel::buildAllTableMaps`
 */

require dirname(__FILE__) . '/../../../../vendor/autoload.php';
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../../fixtures/bookstore/build/classes'));
Propel::init(__DIR__ . '/../../../fixtures/bookstore/build/conf/bookstore-conf.php');

$phpSerializeString = file_get_contents($argv[1]);

$criteria = unserialize($phpSerializeString);
Propel::buildAllTableMaps();

$test = BasePeer::doSelect($criteria);
if ($test->queryString !== 'SELECT book.id, author.last_name FROM `book` INNER JOIN `author` ON (book.author_id=author.id) WHERE book.id=:p1') {
    exit(1);
}

exit(0); //Anything went fine