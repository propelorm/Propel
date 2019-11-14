<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreEmptyTestBase.php';

class ModelCriteriaUpdateTest extends BookstoreEmptyTestBase
{
    public function testUpdateQuery()
    {
        $book = new Book();
        $book->setTitle("Wr & Peacee");
        $book->setISBN('11223344');
        $book->save();
        $bookId = $book->getId();
        
        BookQuery::create()
            ->filterById($bookId)
            ->update(array(
                'Title' => 'War & Peace',
                'ISBN' => '44332211'
            ));
        
        $book1 = BookQuery::create()->findPk($bookId);
        
        $this->assertEquals('War & Peace', $book1->getTitle());
        $this->assertEquals('44332211', $book1->getISBN());
    }
}
