<?php
require_once dirname(__FILE__) . '/../../../../../generator/lib/util/PropelQuickBuilder.php';
require_once dirname(__FILE__) . '/../../../../../runtime/lib/adapter/DBAdapter.php';
require_once dirname(__FILE__) . '/../../../../../runtime/lib/adapter/DBSQLite.php';
require_once dirname(__FILE__) . '/../../../../../runtime/lib/connection/PropelPDO.php';
require_once dirname(__FILE__) . '/../../../../../runtime/lib/Propel.php';
/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Tests for generated constants' names.
 *
 * @author Boban Acimovic <boban.acimovic@gmail.com>
 * @package generator.builder.om
 */
class GeneratedObjectConstantNameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test normal string as single inheritance key
     */
    public function testSingleInheritanceKeyNormalString()
    {
        $schema = <<<XML
<database name="constant_name_test" namespace="ConstantNameTest1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://xsd.propelorm.org/1.6/database.xsd">
  <table name="radcheck" phpName="UserCheck">
    <column name="id" type="INTEGER" sqlType="int(11) unsigned" primaryKey="true" autoIncrement="true" required="true"/>
    <column name="attribute" type="VARCHAR" size="64" required="true" inheritance="single">
      <inheritance key="Expiration" class="UserCheckExpiration" extends="UserCheck"/>
    </column>
  </table>
</database>
XML;
        $this->assertEmptyBuilderOutput($schema);
    }

    /**
     * Test string with dashes as single inheritance key (original cause for this whole test)
     */

    public function testSingleInheritanceKeyStringWithDashes()
    {
        $schema = <<<XML
<database name="constant_name_test" namespace="ConstantNameTest2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://xsd.propelorm.org/1.6/database.xsd">
  <table name="radcheck" phpName="UserCheck2">
    <column name="id" type="INTEGER" sqlType="int(11) unsigned" primaryKey="true" autoIncrement="true" required="true"/>
    <column name="attribute" type="VARCHAR" size="64" required="true" inheritance="single">
      <inheritance key="Calling-Station-Id" class="UserCheckMacAddress" extends="UserCheck2"/>
    </column>
  </table>
</database>
XML;
        $this->assertEmptyBuilderOutput($schema);
    }

    /**
     * Test string with special characters as single inheritance key
     */

    public function testSingleInheritanceKeyStringWithSpecialChars()
    {
        $schema = <<<XML
<database name="constant_name_test" namespace="ConstantNameTest3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://xsd.propelorm.org/1.6/database.xsd">
  <table name="radcheck" phpName="UserCheck3">
    <column name="id" type="INTEGER" sqlType="int(11) unsigned" primaryKey="true" autoIncrement="true" required="true"/>
    <column name="attribute" type="VARCHAR" size="64" required="true" inheritance="single">
      <inheritance key="Key.-_:*" class="UserCheckMacAddress" extends="UserCheck3"/>
    </column>
  </table>
</database>
XML;
        $this->assertEmptyBuilderOutput($schema);
    }

    protected function assertEmptyBuilderOutput($schema)
    {
        $builder = new PropelQuickBuilder();
        $builder->setSchema($schema);

        ob_start();
        $builder->buildClasses();
        $output = preg_replace('/[\r\n]/', '', ob_get_contents());
        ob_end_clean();
        $this->assertEquals('', $output);
    }
}
