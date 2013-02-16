<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
require_once dirname(__FILE__) . '/../../../../runtime/lib/adapter/DBAdapter.php';
require_once dirname(__FILE__) . '/../../../../runtime/lib/adapter/DBPostgres.php';

/**
 * Tests the DBMSSQL adapter
 *
 * @see BookstoreDataPopulator
 * @author Kévin Gomez <contact@kevingomez.fr<
 * @package runtime.adapter
 */
class DBMSSQLTest extends PHPUnit_Framework_TestCase
{
    function test_select_join_order_alias ()
    {
        $adapter = new DBMSSQL();
        $sql = 'SELECT Field, Related.Field AS [RelatedField] FROM Record LEFT JOIN Related ON Record.RelatedID = Related.ID ORDER BY [RelatedField] ASC';
        $adapter->applyLimit($sql, 10, 5);
        $this->assertEquals($sql, 'SELECT [Field], [RelatedField] FROM (SELECT ROW_NUMBER() OVER(ORDER BY Related.Field ASC) AS [RowNumber], Field AS [Field], Related.Field AS [RelatedField] FROM Record LEFT JOIN Related ON Record.RelatedID = Related.ID) AS derivedb WHERE RowNumber BETWEEN 11 AND 15');
    }
}
