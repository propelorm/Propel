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
 * Test class for Right Join with additional condition and hydration process. try to get all Publishers and know on which of them Stephenson has published any book.
 *
 * @author     John Arevalo
 */
class RightJoinAdditionalConditionTest extends BookstoreTestBase
{
    protected function setUp()
    {
        parent::setUp();
        BookstoreDataPopulator::depopulate();
        BookstoreDataPopulator::populate(); //Using this sequence to be able to execute generated query.
    }

    public function testRightJoinAdditionalCondition()
    {
        $stephenson = AuthorQuery::create()->findOneByLastName('Stephenson');
        $formatter = ModelCriteria::FORMAT_OBJECT;
        //$formatter = ModelCriteria::FORMAT_ARRAY; //Using this formatter gets a different, and also unexpected, result
        $books = BookQuery::create()
            ->rightJoinWith('Publisher')
            ->setFormatter($formatter)
            ->addJoinCondition('Publisher', BookPeer::AUTHOR_ID . ' = ?', $stephenson->getId())
            ->find();
        echo "Last Query: " . $this->con->getLastExecutedQuery() . "\n";
        $this->assertEquals(count($books), 4);
        foreach($books as $book)
        {
            $this->assertContains($book->getAuthor()->getId(), array($stephenson->getId(), NULL));
        }
        echo $this->con->getLastExecutedQuery();
    }

    protected function tearDown()
    {
        parent::tearDown();
//        BookstoreDataPopulator::depopulate();
    }
    
}
