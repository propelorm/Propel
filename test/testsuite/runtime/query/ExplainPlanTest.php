<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreTestBase.php';
require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreDataPopulator.php';

/**
 * Test class for SubQueryTest.
 *
 * @author     Francois Zaninotto
 * @version    $Id$
 * @package    runtime.query
 */
class ExplainPlanTest extends BookstoreTestBase
{
	public function testMysqlExplain()
	{
		BookstoreDataPopulator::depopulate($this->con);
		BookstoreDataPopulator::populate($this->con);

		$c = new ModelCriteria('bookstore', 'Book');
		$c->join('Book.Author');
		$c->where('Author.FirstName = ?', 'Neal');
		$c->select('Title');
		$explain = $c->explain($this->con);

		$this->assertEquals(sizeof($explain), 2, 'Explain plan return two lines');

		$this->assertEquals($explain[0]['select_type'], 'SIMPLE', 'Line 1, select_type is equal to "SIMPLE"');
		$this->assertEquals($explain[0]['table'], 'book', 'Line 1, table is equal to "book"');
		$this->assertEquals($explain[0]['type'], 'ALL', 'Line 1, type is equal to "ALL"');
		$this->assertEquals($explain[0]['possible_keys'], 'book_FI_2', 'Line 1, possible_keys is equal to "book_FI_2"');
		$this->assertEquals($explain[0]['key'], null, 'Line 1, key is equal to "NULL"');

		$this->assertEquals($explain[1]['select_type'], 'SIMPLE', 'Line 1, select_type is equal to "SIMPLE"');
		$this->assertEquals($explain[1]['table'], 'author', 'Line 1, table is equal to "author"');
		$this->assertEquals($explain[1]['type'], 'eq_ref', 'Line 1, type is equal to "eq_ref"');
		$this->assertEquals($explain[1]['possible_keys'], 'PRIMARY', 'Line 1, possible_keys is equal to "PRIMARY"');
		$this->assertEquals($explain[1]['key'], 'PRIMARY', 'Line 1, key is equal to "PRIMARY"');
	}
}