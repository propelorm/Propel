<?php

/*
 *	$Id: VersionableBehaviorTest.php 1460 2010-01-17 22:36:48Z francois $
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/util/PropelQuickBuilder.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/behavior/versionable/VersionableBehavior.php';
require_once dirname(__FILE__) . '/../../../../../runtime/lib/Propel.php';

/**
 * Tests for VersionableBehavior class
 *
 * @author     François Zaninotto
 * @version    $Revision$
 * @package    generator.behavior.versionable
 */
class VersionableBehaviorObjectBuilderModifierTest extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		if (!class_exists('VersionableBehaviorTest1')) {
			$schema = <<<EOF
<database name="versionable_behavior_test_1">
	<table name="versionable_behavior_test_1">
		<column name="id" primaryKey="true" type="INTEGER" autoIncrement="true" />
		<column name="bar" type="INTEGER" />
		<behavior name="versionable" />
	</table>
	<table name="versionable_behavior_test_2">
		<column name="id" primaryKey="true" type="INTEGER" autoIncrement="true" />
		<column name="bar" type="INTEGER" />
		<behavior name="versionable">
			<parameter name="version_column" value="foo_ver" />
		</behavior>
	</table>
	<table name="versionable_behavior_test_3">
		<column name="id" primaryKey="true" type="INTEGER" autoIncrement="true" />
		<column name="bar" type="INTEGER" />
		<behavior name="versionable">
			<parameter name="version_table" value="foo_ver" />
		</behavior>
	</table>
	<table name="versionable_behavior_test_4">
		<column name="id" primaryKey="true" type="INTEGER" autoIncrement="true" />
		<column name="bar" type="INTEGER" />
		<behavior name="versionable">
			<parameter name="log_created_at" value="true" />
			<parameter name="log_created_by" value="true" />
			<parameter name="log_comment" value="true" />
		</behavior>
	</table>
</database>>
EOF;
			PropelQuickBuilder::buildSchema($schema);
		}
	}

	public function testGetVersionExists()
	{
		$this->assertTrue(method_exists('VersionableBehaviorTest1', 'getVersion'));
		$this->assertTrue(method_exists('VersionableBehaviorTest2', 'getVersion'));
	}

	public function testSetVersionExists()
	{
		$this->assertTrue(method_exists('VersionableBehaviorTest1', 'setVersion'));
		$this->assertTrue(method_exists('VersionableBehaviorTest2', 'setVersion'));
	}
	
	public function providerForNewActiveRecordTests()
	{
		// Damn you phpUnit, why do providers execute before setUp() ?
		$this->setUp();
		return array(
			array(new VersionableBehaviorTest1()),
			array(new VersionableBehaviorTest2()),
		);
	}

	/**
	 * @dataProvider providerForNewActiveRecordTests
	 */
	public function testVersionGetterAndSetter($o)
	{
		$o->setVersion(1234);
		$this->assertEquals(1234, $o->getVersion());
	}
	
	/**
	 * @dataProvider providerForNewActiveRecordTests
	 */
	public function testVersionDefaultValue($o)
	{
		$this->assertEquals(0, $o->getVersion());
	}

	/**
	 * @dataProvider providerForNewActiveRecordTests
	 */
	public function testVersionValueInitializesOnInsert($o)
	{
		$o->save();
		$this->assertEquals(1, $o->getVersion());
	}

	/**
	 * @dataProvider providerForNewActiveRecordTests
	 */
	public function testVersionValueIncrementsOnUpdate($o)
	{
		$o->save();
		$this->assertEquals(1, $o->getVersion());
		$o->setBar(12);
		$o->save();
		$this->assertEquals(2, $o->getVersion());
		$o->setBar(13);
		$o->save();
		$this->assertEquals(3, $o->getVersion());
		$o->setBar(12);
		$o->save();
		$this->assertEquals(4, $o->getVersion());
	}

	/**
	 * @dataProvider providerForNewActiveRecordTests
	 */
	public function testVersionDoesNotIncrementOnUpdateWithNoChange($o)
	{
		$o->setBar(12);
		$o->save();
		$this->assertEquals(1, $o->getVersion());
		$o->setBar(12);
		$o->save();
		$this->assertEquals(1, $o->getVersion());
	}

	/**
	 * @dataProvider providerForNewActiveRecordTests
	 */
	public function testVersionDoesNotIncrementWhenVersioningIsDisabled($o)
	{
		VersionableBehaviorTest1Peer::disableVersioning();
		VersionableBehaviorTest2Peer::disableVersioning();
		$o->setBar(12);
		$o->save();
		$this->assertEquals(0, $o->getVersion());
		$o->setBar(13);
		$o->save();
		$this->assertEquals(0, $o->getVersion());
		VersionableBehaviorTest1Peer::enableVersioning();
		VersionableBehaviorTest2Peer::enableVersioning();

	}
	
	public function testNewVersionCreatesRecordInVersionTable()
	{
		VersionableBehaviorTest1Query::create()->deleteAll();
		VersionableBehaviorTest1VersionQuery::create()->deleteAll();
		$o = new VersionableBehaviorTest1();
		$o->save();
		$versions = VersionableBehaviorTest1VersionQuery::create()->find();
		$this->assertEquals(1, $versions->count());
		$this->assertEquals($o, $versions[0]->getVersionableBehaviorTest1());
		$o->save();
		$versions = VersionableBehaviorTest1VersionQuery::create()->find();
		$this->assertEquals(1, $versions->count());
		$o->setBar(123);
		$o->save();
		$versions = VersionableBehaviorTest1VersionQuery::create()->orderByVersion()->find();
		$this->assertEquals(2, $versions->count());
		$this->assertEquals($o->getId(), $versions[0]->getId());
		$this->assertNull($versions[0]->getBar());
		$this->assertEquals($o->getId(), $versions[1]->getId());
		$this->assertEquals(123, $versions[1]->getBar());
	}
	
	public function testNewVersionCreatesRecordInVersionTableWithCustomName()
	{
		VersionableBehaviorTest3Query::create()->deleteAll();
		VersionableBehaviorTest3VersionQuery::create()->deleteAll();
		$o = new VersionableBehaviorTest3();
		$o->save();
		$versions = VersionableBehaviorTest3VersionQuery::create()->find();
		$this->assertEquals(1, $versions->count());
		$this->assertEquals($o, $versions[0]->getVersionableBehaviorTest3());
		$o->save();
		$versions = VersionableBehaviorTest3VersionQuery::create()->find();
		$this->assertEquals(1, $versions->count());
		$o->setBar(123);
		$o->save();
		$versions = VersionableBehaviorTest3VersionQuery::create()->orderByVersion()->find();
		$this->assertEquals(2, $versions->count());
		$this->assertEquals($o->getId(), $versions[0]->getId());
		$this->assertNull($versions[0]->getBar());
		$this->assertEquals($o->getId(), $versions[1]->getId());
		$this->assertEquals(123, $versions[1]->getBar());
	}

	public function testNewVersionDoesNotCreateRecordInVersionTableWhenVersioningIsDisabled()
	{
		VersionableBehaviorTest1Query::create()->deleteAll();
		VersionableBehaviorTest1VersionQuery::create()->deleteAll();
		VersionableBehaviorTest1Peer::disableVersioning();
		$o = new VersionableBehaviorTest1();
		$o->save();
		$versions = VersionableBehaviorTest1VersionQuery::create()->find();
		$this->assertEquals(0, $versions->count());
		VersionableBehaviorTest1Peer::enableVersioning();
	}

	public function testDeleteObjectDeletesRecordInVersionTable()
	{
		VersionableBehaviorTest1Query::create()->deleteAll();
		VersionableBehaviorTest1VersionQuery::create()->deleteAll();
		$o = new VersionableBehaviorTest1();
		$o->save();
		$o->setBar(123);
		$o->save();
		$nbVersions = VersionableBehaviorTest1VersionQuery::create()->count();
		$this->assertEquals(2, $nbVersions);
		$o->delete();
		$nbVersions = VersionableBehaviorTest1VersionQuery::create()->count();
		$this->assertEquals(0, $nbVersions);
	}

	public function testDeleteObjectDeletesRecordInVersionTableWithCustomName()
	{
		VersionableBehaviorTest3Query::create()->deleteAll();
		VersionableBehaviorTest3VersionQuery::create()->deleteAll();
		$o = new VersionableBehaviorTest3();
		$o->save();
		$o->setBar(123);
		$o->save();
		$nbVersions = VersionableBehaviorTest3VersionQuery::create()->count();
		$this->assertEquals(2, $nbVersions);
		$o->delete();
		$nbVersions = VersionableBehaviorTest3VersionQuery::create()->count();
		$this->assertEquals(0, $nbVersions);
	}
	
	public function testToVersion()
	{
		$o = new VersionableBehaviorTest1();
		$o->setBar(123); // version 1
		$o->save();
		$o->setBar(456); // version 2
		$o->save();
		$o->toVersion(1);
		$this->assertEquals(123, $o->getBar());
		$o->toVersion(2);
		$this->assertEquals(456, $o->getBar());
	}
	
	public function testToVersionAllowsFurtherSave()
	{
		$o = new VersionableBehaviorTest1();
		$o->setBar(123); // version 1
		$o->save();
		$o->setBar(456); // version 2
		$o->save();
		$o->toVersion(1);
		$this->assertTrue($o->isModified());
		$o->save();
		$this->assertEquals(3, $o->getVersion());
	}

	/**
	 * @expectedException PropelException
	 */
	public function testToVersionThrowsExceptionOnIncorrectVersion()
	{
		$o = new VersionableBehaviorTest1();
		$o->setBar(123); // version 1
		$o->save();
		$o->toVersion(2);
	}

	public function testGetLastVersionNumber()
	{
		$o = new VersionableBehaviorTest1();
		$this->assertEquals(0, $o->getLastVersionNumber());
		$o->setBar(123); // version 1
		$o->save();
		$this->assertEquals(1, $o->getLastVersionNumber());
		$o->setBar(456); // version 2
		$o->save();
		$this->assertEquals(2, $o->getLastVersionNumber());
		$o->toVersion(1);
		$o->save();
		$this->assertEquals(3, $o->getLastVersionNumber());
	}
	
	public function testIsLastVersion()
	{
		$o = new VersionableBehaviorTest1();
		$this->assertTrue($o->isLastVersion());
		$o->setBar(123); // version 1
		$o->save();
		$this->assertTrue($o->isLastVersion());
		$o->setBar(456); // version 2
		$o->save();
		$this->assertTrue($o->isLastVersion());
		$o->toVersion(1);
		$this->assertFalse($o->isLastVersion());
		$o->save();
		$this->assertTrue($o->isLastVersion());
	}
	
	public function testIsVersioningNecessary()
	{
		$o = new VersionableBehaviorTest1();
		$this->assertTrue($o->isVersioningNecessary());
		$o->save();
		$this->assertFalse($o->isVersioningNecessary());
		$o->setBar(123);
		$this->assertTrue($o->isVersioningNecessary());
		$o->save();
		$this->assertFalse($o->isVersioningNecessary());

		VersionableBehaviorTest1Peer::disableVersioning();
		$o = new VersionableBehaviorTest1();
		$this->assertFalse($o->isVersioningNecessary());
		$o->save();
		$this->assertFalse($o->isVersioningNecessary());
		$o->setBar(123);
		$this->assertFalse($o->isVersioningNecessary());
		$o->save();
		$this->assertFalse($o->isVersioningNecessary());
		VersionableBehaviorTest1Peer::enableVersioning();
	}
	
	public function testAddVersionNewObject()
	{
		VersionableBehaviorTest1Peer::disableVersioning();
		VersionableBehaviorTest1Query::create()->deleteAll();
		VersionableBehaviorTest1VersionQuery::create()->deleteAll();
		$o = new VersionableBehaviorTest1();
		$o->addVersion();
		$o->save();
		$versions = VersionableBehaviorTest1VersionQuery::create()->find();
		$this->assertEquals(1, $versions->count());
		$this->assertEquals($o, $versions[0]->getVersionableBehaviorTest1());
	}

	public function testVersionCreatedAt()
	{
		$o = new VersionableBehaviorTest4();
		$t = time();
		$o->save();
		$version = VersionableBehaviorTest4VersionQuery::create()
			->filterByVersionableBehaviorTest4($o)
			->findOne();
		$this->assertEquals($t, $version->getVersionCreatedAt('U'));
		
		$o = new VersionableBehaviorTest4();
		$inThePast = time() - 123456;
		$o->setVersionCreatedAt($inThePast);
		$o->save();
		$this->assertEquals($inThePast, $o->getVersionCreatedAt('U'));
		$version = VersionableBehaviorTest4VersionQuery::create()
			->filterByVersionableBehaviorTest4($o)
			->findOne();
		$this->assertEquals($o->getVersionCreatedAt(), $version->getVersionCreatedAt());
	}

	public function testVersionCreatedBy()
	{
		$o = new VersionableBehaviorTest4();
		$o->setVersionCreatedBy('me me me');
		$o->save();
		$version = VersionableBehaviorTest4VersionQuery::create()
			->filterByVersionableBehaviorTest4($o)
			->findOne();
		$this->assertEquals('me me me', $version->getVersionCreatedBy());
	}

	public function testVersionComment()
	{
		$o = new VersionableBehaviorTest4();
		$o->setVersionComment('Because you deserve it');
		$o->save();
		$version = VersionableBehaviorTest4VersionQuery::create()
			->filterByVersionableBehaviorTest4($o)
			->findOne();
		$this->assertEquals('Because you deserve it', $version->getVersionComment());
	}

	public function testToVersionWorksWithComments()
	{
		$o = new VersionableBehaviorTest4();
		$o->setVersionComment('Because you deserve it');
		$o->setBar(123); // version 1
		$o->save();
		$o->setVersionComment('Unless I change my mind');
		$o->setBar(456); // version 2
		$o->save();
		$o->toVersion(1);
		$this->assertEquals('Because you deserve it', $o->getVersionComment());
		$o->toVersion(2);
		$this->assertEquals('Unless I change my mind', $o->getVersionComment());
	}
}