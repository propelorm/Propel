<?php
require_once dirname(__FILE__) . '/../../../runtime/lib/Propel.php';
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../fixtures/bookstore/build/classes'));
Propel::init(dirname(__FILE__) . '/../../fixtures/bookstore/build/conf/bookstore-conf.php');


class SelfReferringTest extends PHPUnit_Framework_TestCase
{
    public static $con;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Get the Connection
        self::$con = Propel::getConnection(SelfReferringPeer::DATABASE_NAME);

        $item = new SelfReferring();
        $item->setName('Item');

        $item2 = new SelfReferring();
        $item2->setName('Item2');
        $item2->setSelfReferringRelatedByRelatedId($item);

        $item->save();
    }


    public static function tearDownAfterClass()
    {
      // Cleanup the Database
      call_user_func(array('SelfReferringPeer', 'doDeleteAll'), self::$con);
      parent::tearDownAfterClass();
    }

    public function testBasicQuery()
    {
      $results = SelfReferringQuery::create()
        ->filterByName('Item')
        ->findOne();
    }

    public function testBasicQueryWithLeftJoin()
    {
      $results = SelfReferringQuery::create()
        ->where('Name', 'Item')
        ->leftJoinSelfReferringRelatedByRelatedId()
        ->findOne();
    }

    public function testBasicQueryWithNamedLeftJoin()
    {
      $results = SelfReferringQuery::create()
        ->filterByName('Item')
        ->leftJoinSelfReferringRelatedByRelatedId('self_referring2')
        ->findOne();
    }

    public function testBasicQueryWithNamedLeftJoinAndInlusionOfRelatedObject()
    {
      $results = SelfReferringQuery::create()
        ->filterByName('Item')
        ->leftJoinWithSelfReferringRelatedByRelatedId('self_referring2')
        ->findOne();
    }
}
