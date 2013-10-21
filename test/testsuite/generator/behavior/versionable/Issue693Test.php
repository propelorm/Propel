<?php

require_once dirname(__FILE__) . '/../../../../../generator/lib/util/PropelQuickBuilder.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/behavior/versionable/VersionableBehavior.php';
require_once dirname(__FILE__) . '/../../../../../runtime/lib/Propel.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/platform/PgsqlPlatform.php';

class Issue693Test extends PHPUnit_Framework_TestCase
{
    protected $builder = null;
    
    public function setUp()
    {
        if (null === $this->builder) {
            $this->builder = $this->createBuilder();
        }
            
        if (!class_exists('Issue693Version')) {
            $this->builder->buildClasses();
        }
    }
    
    public function testGeneratedSqlForPostgreWithSchema()
    {
        $expected = <<<EOF
-----------------------------------------------------------------------
-- foo.issue693_version
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "foo"."issue693_version" CASCADE;

CREATE TABLE "foo"."issue693_version"
(
    "id" INTEGER NOT NULL,
    "bar" INTEGER,
    "version" INTEGER DEFAULT 0 NOT NULL,
    PRIMARY KEY ("id","version")
);
EOF;
        
        $this->assertContains($expected, $this->builder->getSQL());
    }
    
    public function testConstantaColumnNameField()
    {
        $this->assertSame(Issue693VersionPeer::ID, 'foo.issue693_version.id');
        $this->assertSame(Issue693VersionPeer::BAR, 'foo.issue693_version.bar');
        $this->assertSame(Issue693VersionPeer::VERSION, 'foo.issue693_version.version');
    }
    
    protected function createBuilder()
    {
        $builder = new PropelQuickBuilder();
        $builder->setPlatform(new PgsqlPlatform());
        $builder->setSchema($this->getXMLSchema());
        
        return $builder;
    }
    
    protected function getXMLSchema()
    {
        $schema = <<<EOF
<database name="bookstore" schema="foo">
    <table name="issue693">
        <column name="id" primaryKey="true" type="INTEGER" autoIncrement="true" />
        <column name="bar" type="INTEGER" />
        <behavior name="versionable" />
    </table>
</database>
EOF;

        return $schema;
    }
}