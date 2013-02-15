<?php

/*
 *	$Id$
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Tests for More relations
 *
 * @author		MArc J. Schmidt
 * @version		$Revision$
 * @package		generator.builder.om
 */
class GeneratedObjectMoreRelationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Setup schema und some default data
     */
    public function setUp()
    {
        parent::setUp();

        if (!class_exists('MoreRelationTest\Page')) {
            $schema = <<<EOF
<database name="more_relation_test" namespace="MoreRelationTest">

    <table name="more_relation_test_page" phpName="Page">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
        <column name="title" type="VARCHAR" size="100" primaryString="true" />
    </table>
    <table name="more_relation_test_content" phpName="Content">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
        <column name="title" type="VARCHAR" size="100" />
        <column name="content" type="LONGVARCHAR" required="false" />
        <column name="page_id" type="INTEGER" required="false" />
        <foreign-key foreignTable="more_relation_test_page" onDelete="cascade">
          <reference local="page_id" foreign="id"/>
        </foreign-key>
    </table>

    <table name="more_relation_test_comment" phpName="Comment">
        <column name="user_id" required="true" primaryKey="true" type="INTEGER" />
        <column name="page_id" required="true" primaryKey="true" type="INTEGER" />
        <column name="comment" type="VARCHAR" size="100" />
        <foreign-key foreignTable="more_relation_test_page" onDelete="cascade">
          <reference local="page_id" foreign="id"/>
        </foreign-key>
    </table>
</database>
EOF;

            $builder = new PropelQuickBuilder();
            $builder->setSchema($schema);
            $builder->build();
        }

        \MoreRelationTest\PagePeer::doDeleteAll();
        \MoreRelationTest\ContentPeer::doDeleteAll();

        for($i=1;$i<=2;$i++){

            $page = new \MoreRelationTest\Page();

            $page->setTitle('Page '.$i);
            for($j=1;$j<=3;$j++){

                $content = new \MoreRelationTest\Content();
                $content->setTitle('Content '.$j);
                $content->setContent(str_repeat('Content', $j));
                $page->addContent($content);

                $comment = new \MoreRelationTest\Comment();
                $comment->setUserId($j);
                $comment->setComment(str_repeat('Comment', $j));
                $page->addComment($comment);

            }
            $page->save();
        }

    }

    /**
     * Composite PK deletion of a 1-to-n relation through set<RelationName>()
     * where the PK is at the same time a FK.
     */
    public function testCommentsDeletion(){

        $commentCollection = new PropelObjectCollection();
        $commentCollection->setModel('MoreRelationTest\\Comment');

        $comment = new \MoreRelationTest\Comment();
        $comment->setComment('I should be alone :-(');
        $comment->setUserId(123);

        $commentCollection[] = $comment;

        $page = \MoreRelationTest\PageQuery::create()->findOne();
        $id = $page->getId();

        $count = \MoreRelationTest\CommentQuery::create()->filterByPageId($id)->count();
        $this->assertEquals(3, $count, 'We created for each page 3 comments.');


        $page->setComments($commentCollection);
        $page->save();

        unset($page);

        $count = \MoreRelationTest\CommentQuery::create()->filterByPageId($id)->count();
        $this->assertEquals(1, $count, 'We assigned a collection of only one item.');

    }

    /**
     * Basic deletion of a 1-to-n relation through set<RelationName>().
     *
     */
    public function testContentsDeletion(){


        $contentCollection = new PropelObjectCollection();
        $contentCollection->setModel('MoreRelationTest\\Content');

        $content = new \MoreRelationTest\Content();
        $content->setTitle('I should be alone :-(');

        $contentCollection[] = $content;

        $page = \MoreRelationTest\PageQuery::create()->findOne();
        $id = $page->getId();

        $count = \MoreRelationTest\ContentQuery::create()->filterByPageId($id)->count();
        $this->assertEquals(3, $count, 'We created for each page 3 contents.');


        $page->setContents($contentCollection);
        $page->save();

        unset($page);

        $count = \MoreRelationTest\ContentQuery::create()->filterByPageId($id)->count();
        $this->assertEquals(1, $count, 'We assigned a collection of only one item.');

    }






}