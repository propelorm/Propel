<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreTestBase.php';

/**
 * Tests adapter independent functionality
 *
 * @see        BookstoreDataPopulator
 * @author     Francois EZaninotto
 * @package    runtime.adapter
 */
class DBAdapterTest extends BookstoreTestBase
{

    public function testTurnSelectColumnsToAliases()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c1 = new Criteria();
        $c1->addSelectColumn(BookPeer::ID);
        $db->turnSelectColumnsToAliases($c1);

        $c2 = new Criteria();
        $c2->addAsColumn('book_id', BookPeer::ID);
        $this->assertTrue($c1->equals($c2));
    }

    public function testTurnSelectColumnsToAliasesPreservesAliases()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c1 = new Criteria();
        $c1->addSelectColumn(BookPeer::ID);
        $c1->addAsColumn('foo', BookPeer::TITLE);
        $db->turnSelectColumnsToAliases($c1);

        $c2 = new Criteria();
        $c2->addAsColumn('book_id', BookPeer::ID);
        $c2->addAsColumn('foo', BookPeer::TITLE);
        $this->assertTrue($c1->equals($c2));
    }

    public function testTurnSelectColumnsToAliasesExisting()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c1 = new Criteria();
        $c1->addSelectColumn(BookPeer::ID);
        $c1->addAsColumn('book_id', BookPeer::ID);
        $db->turnSelectColumnsToAliases($c1);

        $c2 = new Criteria();
        $c2->addAsColumn('book_id_1', BookPeer::ID);
        $c2->addAsColumn('book_id', BookPeer::ID);
        $this->assertTrue($c1->equals($c2));
    }

    public function testTurnSelectColumnsToAliasesDuplicate()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c1 = new Criteria();
        $c1->addSelectColumn(BookPeer::ID);
        $c1->addSelectColumn(BookPeer::ID);
        $db->turnSelectColumnsToAliases($c1);

        $c2 = new Criteria();
        $c2->addAsColumn('book_id', BookPeer::ID);
        $c2->addAsColumn('book_id_1', BookPeer::ID);
        $this->assertTrue($c1->equals($c2));
    }

    public function testCreateSelectSqlPart()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c = new Criteria();
        $c->addSelectColumn(BookPeer::ID);
        $c->addAsColumn('book_ID', BookPeer::ID);
        $fromClause = array();
        $selectSql = $db->createSelectSqlPart($c, $fromClause);
        $this->assertEquals('SELECT book.id, book.id AS '.$db->quoteIdentifier('book_ID'), $selectSql, 'createSelectSqlPart() returns a SQL SELECT clause with both select and as columns');
        $this->assertEquals(array('book'), $fromClause, 'createSelectSqlPart() adds the tables from the select columns to the from clause');
    }

    public function testCreateSelectSqlPartWithFnc()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c = new Criteria();
        $c->addSelectColumn(BookPeer::ID);
        $c->addAsColumn('book_ID', 'IF(1, '.BookPeer::ID.', '.BookPeer::TITLE.')');
        $fromClause = array();
        $selectSql = $db->createSelectSqlPart($c, $fromClause);
        $this->assertEquals('SELECT book.id, IF(1, book.id, book.title) AS '.$db->quoteIdentifier('book_ID'), $selectSql, 'createSelectSqlPart() returns a SQL SELECT clause with both select and as columns');
        $this->assertEquals(array('book'), $fromClause, 'createSelectSqlPart() adds the tables from the select columns to the from clause');
    }

    public function testCreateSelectSqlPartSelectModifier()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c = new Criteria();
        $c->addSelectColumn(BookPeer::ID);
        $c->addAsColumn('book_ID', BookPeer::ID);
        $c->setDistinct();
        $fromClause = array();
        $selectSql = $db->createSelectSqlPart($c, $fromClause);
        $this->assertEquals('SELECT DISTINCT book.id, book.id AS '.$db->quoteIdentifier('book_ID'), $selectSql, 'createSelectSqlPart() includes the select modifiers in the SELECT clause');
        $this->assertEquals(array('book'), $fromClause, 'createSelectSqlPart() adds the tables from the select columns to the from clause');
    }

    public function testCreateSelectSqlPartAliasAll()
    {
        $db = Propel::getDB(BookPeer::DATABASE_NAME);
        $c = new Criteria();
        $c->addSelectColumn(BookPeer::ID);
        $c->addAsColumn('book_id', BookPeer::ID);
        $fromClause = array();
        $selectSql = $db->createSelectSqlPart($c, $fromClause, true);
        $this->assertEquals('SELECT book.id AS '.$db->quoteIdentifier('book_id_1').', book.id AS '.$db->quoteIdentifier('book_id'), $selectSql, 'createSelectSqlPart() aliases all columns if passed true as last parameter');
        $this->assertEquals(array(), $fromClause, 'createSelectSqlPart() does not add the tables from an all-aliased list of select columns');
    }
}
