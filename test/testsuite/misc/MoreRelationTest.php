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
 * Tests for SortableBehavior class
 *
 * @author		MArc J. Schmidt
 * @version		$Revision$
 * @package		generator.misc
 */
class MoreRelationTest extends PHPUnit_Framework_TestCase
{


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
</database>
EOF;

            $builder = new PropelQuickBuilder();
            $builder->setSchema($schema);
            $builder->build();
        }

        \MoreRelationTest\PagePeer::doDeleteAll();
        \MoreRelationTest\ContentPeer::doDeleteAll();

        for($i=1;$i<=5;$i++){

            $page = new \MoreRelationTest\Page();

            $page->setTitle('Page '.$i);
            for($j=1;$j<=3;$j++){

                $content = new \MoreRelationTest\Content();
                $content->setTitle('Content '.$j);
                $content->setContent(str_repeat('Content', $j));
                $page->addContent($content);

            }
            $page->save();
        }

    }

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