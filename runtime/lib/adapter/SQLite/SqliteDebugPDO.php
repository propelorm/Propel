<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * PDOStatement that provides some functionnal fix for sqlite rowcount()
 * http://php.net/manual/en/pdostatement.rowcount.php
 * 
 * simply transform the rowcount into a fetchall
 * @package    propel.runtime.connection
 */
class SqliteDebugPDO extends SqlitePropelPDO
{
    public $useDebug = true;
}

