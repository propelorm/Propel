<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
require_once dirname(__FILE__) . '/../../../../runtime/lib/Propel.php';
require_once dirname(__FILE__) . '/PlatformMigrationTestProvider.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/platform/MysqlPlatform.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/model/Column.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/reverse/mysql/MysqlSchemaParser.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/model/VendorInfo.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/reverse/mysql/MysqlSchemaParser.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/config/QuickGeneratorConfig.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/model/PropelTypes.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/model/Database.php';
require_once dirname(__FILE__) . '/../../../../generator/lib/platform/MysqlPlatform.php';

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../../../../generator/lib');
require_once dirname(__FILE__) . '/../../../../generator/lib/task/PropelConvertConfTask.php';

/**
 *
 * @package    generator.platform
 */
class MysqlPlatformMigrationDecimalTest extends PlatformMigrationTestProvider
{
    /**
     * Get the Platform object for this class
     *
     * @return Platform
     */
    protected function getPlatform()
    {
        return new MysqlPlatform();
    }

    /**
     * @dataProvider providerForTestGetModifyDatabaseDDL
     */
    public function testDecimal()
    {
        $xmlDom = new DOMDocument();
        $xmlDom->load(dirname(__FILE__) . '/../../../fixtures/reverse-decimal/mysql/runtime-conf.xml');
        $xml = simplexml_load_string($xmlDom->saveXML());
        $phpconf = OpenedPropelConvertConfTask::simpleXmlToArray($xml);

        Propel::setConfiguration($phpconf);
        Propel::initialize();	
	
	
        $t1 = new Table('foo');
		
		$schema = '<database name="test"><table name="foo"><column name="longitude" type="DECIMAL" scale="7" size="10" /></table></database>';
        $xtad = new XmlToAppData();
        $appData = $xtad->parseString($schema);
        $database = $appData->getDatabase();
        $table = $database->getTable('foo');
		$c1 = $table->getColumn('longitude');
		
		
        $parser = new MysqlSchemaParser(Propel::getConnection('reverse-decimal'));
        $parser->setGeneratorConfig(new QuickGeneratorConfig());		

        $database = new Database();
        $database->setPlatform(new MysqlPlatform());
		$parser->parse($database);
		
        $tables = $database->getTables();
		$table = $tables[0];
		
		$c2 = $table->getColumn('longitude');
		$this->assertEquals($c1->getSize(), $c2->getSize());
    }
}


class OpenedPropelConvertConfTask extends PropelConvertConfTask
{
    public static function simpleXmlToArray($xml)
    {
        return parent::simpleXmlToArray($xml);
    }
}

$x = new MysqlPlatformMigrationDecimalTest;
$x->testDecimal();