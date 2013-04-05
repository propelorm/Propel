<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../../../runtime/lib/Propel.php';

require_once dirname(__FILE__) . '/../../../../../generator/lib/reverse/mysql/MysqlSchemaParser.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/config/QuickGeneratorConfig.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/PropelTypes.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/Database.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/platform/DefaultPlatform.php';

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../../../../../generator/lib');
require_once dirname(__FILE__) . '/../../../../../generator/lib/task/PropelConvertConfTask.php';

/**
 * Tests for Mysql database schema parser.
 *
 * @author      William Durand
 * @version     $Revision$
 * @package     propel.generator.reverse.mysql
 */
class MysqlSchemaParserTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $xmlDom = new DOMDocument();
        $xmlDom->load(dirname(__FILE__) . '/../../../../fixtures/reverse/mysql/runtime-conf.xml');
        $xml = simplexml_load_string($xmlDom->saveXML());
        $phpconf = OpenedPropelConvertConfTask::simpleXmlToArray($xml);

        Propel::setConfiguration($phpconf);
        Propel::initialize();
    }

    protected function tearDown()
    {
        parent::tearDown();
        Propel::init(dirname(__FILE__) . '/../../../../fixtures/bookstore/build/conf/bookstore-conf.php');
    }

    public function testParse()
    {
        $parser = new MysqlSchemaParser(Propel::getConnection('reverse-bookstore'));
        $parser->setGeneratorConfig(new QuickGeneratorConfig());

        $database = new Database();
        $database->setPlatform(new DefaultPlatform());

        $this->assertEquals(2, $parser->parse($database), 'two tables and one view defined should return two as we exclude views');

        $tables = $database->getTables();
        $this->assertEquals(2, count($tables));

        $table = $tables[0];
        $this->assertEquals('Book', $table->getPhpName());
        $this->assertEquals(4, count($table->getColumns()));
    }

    public function testDecimal()
    {
        $t1 = new Table('foo');

        $schema = '<database name="reverse_bookstore"><table name="foo"><column name="longitude" type="DECIMAL" scale="7" size="10" /></table></database>';
        $xtad = new XmlToAppData();
        $appData = $xtad->parseString($schema);
        $database = $appData->getDatabase();
        $table = $database->getTable('foo');
        $c1 = $table->getColumn('longitude');

        $parser = new MysqlSchemaParser(Propel::getConnection('reverse-bookstore'));
        $parser->setGeneratorConfig(new QuickGeneratorConfig());

        $database = new Database();
        $database->setPlatform(new MysqlPlatform());
        $parser->parse($database);

        $table = $database->getTable('foo');

        $c2 = $table->getColumn('longitude');
        $this->assertEquals($c1->getSize(), $c2->getSize());
        $this->assertEquals($c1->getScale(), $c2->getScale());
    }
}

class OpenedPropelConvertConfTask extends PropelConvertConfTask
{
    public static function simpleXmlToArray($xml)
    {
        return parent::simpleXmlToArray($xml);
    }
}
