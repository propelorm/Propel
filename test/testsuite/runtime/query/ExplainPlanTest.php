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

		$db = Propel::getDb(BookPeer::DATABASE_NAME);

		$c = new ModelCriteria('bookstore', 'Book');
		$c->join('Book.Author');
		$c->where('Author.FirstName = ?', 'Neal');
		$c->select('Title');
		$explain = $c->explain($this->con);

		if ($db instanceof DBMySQL) {
			$this->assertEquals(sizeof($explain), 2, 'Explain plan return two lines');

			// explain can change sometime, test can't be strict
			$this->assertTrue(!empty($explain[0]['select_type']), 'Line 1, select_type is equal to "SIMPLE"');
			$this->assertTrue(!empty($explain[0]['table']), 'Line 1, table is equal to "book"');
			$this->assertTrue(!empty($explain[0]['type']), 'Line 1, type is equal to "ALL"');
			$this->assertTrue(!empty($explain[0]['possible_keys']), 'Line 1, possible_keys is equal to "book_FI_2"');

			$this->assertTrue(!empty($explain[1]['select_type']), 'Line 2, select_type is equal to "SIMPLE"');
			$this->assertTrue(!empty($explain[1]['table']), 'Line 2, table is equal to "author"');
			$this->assertTrue(!empty($explain[1]['type']), 'Line 2, type is equal to "eq_ref"');
			$this->assertTrue(!empty($explain[1]['possible_keys']), 'Line 2, possible_keys is equal to "PRIMARY"');
		} elseif($db instanceof DBOracle) {
			$this->assertTrue(sizeof($explain) > 2, 'Explain plan return more than 2 lines');
		} else {
			$this->markTestSkipped('Cannot test explain plan on adapter ' . get_class($db));
		}
	}
}