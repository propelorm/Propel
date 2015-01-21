<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreEmptyTestBase.php';

class ModelCriteriaGroupByTest extends BookstoreEmptyTestBase
{
    public function testGroupByArray()
    {
        $stephenson = new Author();
        $stephenson->setFirstName("Neal");
        $stephenson->setLastName("Stephenson");
        $stephenson->save();

        $byron = new Author();
        $byron->setFirstName("George");
        $byron->setLastName("Byron");
        $byron->save();
        
        $phoenix = new Book();
        $phoenix->setTitle("Harry Potter and the Order of the Phoenix");
        $phoenix->setISBN("043935806X");
        $phoenix->setAuthor($stephenson);
        $phoenix->save();
        
        $qs = new Book();
        $qs->setISBN("0380977427");
        $qs->setTitle("Quicksilver");
        $qs->setAuthor($stephenson);
        $qs->save();

        $dj = new Book();
        $dj->setISBN("0140422161");
        $dj->setTitle("Don Juan");
        $dj->setAuthor($stephenson);
        $dj->save();

        $td = new Book();
        $td->setISBN("067972575X");
        $td->setTitle("The Tin Drum");
        $td->setAuthor($byron);
        $td->save();
        
        $authors = AuthorQuery::create()
            ->leftJoinBook()
            ->select(array('FirstName', 'LastName'))
            ->withColumn('COUNT(Book.Id)', 'nbBooks')
            ->groupByArray(array('FirstName', 'LastName'))
            ->orderByLastName()
            ->find();
        
        $expectedSql = 'SELECT author.first_name AS `FirstName`, author.last_name AS `LastName`, COUNT(book.id) AS `nbBooks` FROM `author` LEFT JOIN `book` ON (author.id=book.author_id) GROUP BY author.first_name,author.last_name ORDER BY author.last_name ASC';
        
        $this->assertEquals($expectedSql, $this->con->getLastExecutedQuery());
        
        $this->assertEquals(2, count($authors));
        
        $this->assertEquals('George', $authors[0]['FirstName']);
        $this->assertEquals(1, $authors[0]['nbBooks']);
        
        $this->assertEquals('Neal', $authors[1]['FirstName']);
        $this->assertEquals(3, $authors[1]['nbBooks']);
    }
}