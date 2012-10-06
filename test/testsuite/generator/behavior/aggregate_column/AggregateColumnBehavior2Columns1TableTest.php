<?php

require_once dirname(__FILE__) . '/../../../../tools/helpers/bookstore/BookstoreTestBase.php';

$propel_dir = dirname(__FILE__) . '/../../../../..';
require_once $propel_dir . '/generator/lib/util/PropelQuickBuilder.php';
require_once $propel_dir . '/runtime/lib/Propel.php';
require_once $propel_dir . '/generator/lib/util/PropelQuickBuilder.php';
require_once $propel_dir . '/generator/lib/util/PropelPHPParser.php';
require_once $propel_dir . '/generator/lib/behavior/aggregate_column/AggregateColumnBehavior.php';

/**
 * Description of AggregateColumnBehavior2Columns1TableTest
 *
 * @package     ???
 * @subpackage  ???
 * @author      Ivan Plamenov Tanev aka Crafty_Shadow @ WEBWORLD.BG <vankata.t@gmail.com>
 */
class AggregateColumnBehavior2Columns1TableTest extends BookstoreTestBase
{
  public function setUp()
  {
    parent::setUp();
    if (!class_exists('AggregateColumnBehaviro2Columns1Table'))
    {
      $schema = <<<EOF
<database name="aggregate_column_behavior_2_columns_1_table">
  <table name="aggregate_column_behavior_2_columns_1_table">
    <column name="id" type="INTEGER" primaryKey="true" autoincrement="true" />
    <column name="name" type="VARCHAR" size="255" />
    <behavior name="aggregate_column">
      <parameter name="name" value="nb_cups" />
      <parameter name="foreign_table" value="aggregate_column_cups" />
    </behavior>
    <behavior name="aggregate_column">
      <parameter name="name" value="nb_other_cups" />
      <parameter name="foreign_table" value="aggregate_column_cups" />
    </behavior>
    <behavior name="aggregate_column">
      <parameter name="name" value="nb_girls" />
      <parameter name="foreign_table" value="aggregate_column_girls" />
    </behavior>
  </table>

  <table name="aggregate_column_cups" identifier="1">
    <column name="id" type="INTEGER" primaryKey="true" autoincrement="true" />
    <column name="aggregate_id" type="INTEGER" />
    <foreign-key foreignTable="aggregate_column_behavior_2_columns_1_table" onDelete="cascade">
      <reference local="aggregate_id" foreign="id" />
    </foreign-key>
  </table>

  <table name="aggregate_column_girls" identifier="2">
    <column name="id" type="INTEGER" primaryKey="true" autoincrement="true" />
    <column name="aggregate_id" type="INTEGER" />
    <foreign-key foreignTable="aggregate_column_behavior_2_columns_1_table" onDelete="cascade">
      <reference local="aggregate_id" foreign="id" />
    </foreign-key>
  </table>

</database>
EOF;
			PropelQuickBuilder::buildSchema($schema);
    }
  }

  public function allGeneratedClassesDataProvider()
  {
    return array(
      array('AggregateColumnBehavior2Columns1Table'),
    );
  }

  /**
   * @dataProvider allGeneratedClassesDataProvider
   */
  public function testMethodExists($class)
  {
    $obj = new $class();
    $this->assertTrue(method_exists($obj, 'getNbGirls'));
    $this->assertTrue(method_exists($obj, 'getNbCups'));
    $this->assertTrue(method_exists($obj, 'getNbOtherCups'));
  }
}
