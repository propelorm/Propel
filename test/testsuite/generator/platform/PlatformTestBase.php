<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../../generator/lib/builder/util/XmlToAppData.php';

use PHPUnit\Framework\TestCase;

/**
 * Base class for all Platform tests
 * @package    generator.platform
 */
abstract class PlatformTestBase extends TestCase
{

    abstract protected function getPlatform();

    protected function getDatabaseFromSchema($schema)
    {
        $xtad = new XmlToAppData($this->getPlatform());
        $appData = $xtad->parseString($schema);

        return $appData->getDatabase();
    }

    protected function getTableFromSchema($schema, $tableName = 'foo')
    {
        return $this->getDatabaseFromSchema($schema)->getTable($tableName);
    }

}
