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
class SQLiteDebugPDOStatement extends DebugPDOStatement
{
        // dont use $_pdo member variable as it is already a protected member of
        // DebugPDOStatement
	/**
	 * 
	 * @var \Pdo
	 
	private $_pdo;
         * 
         */

	/**
	 * 
	 * Constructor
	 * 
	 * @param \Pdo $pdo
	 
	protected function __construct($pdo)
	{
		$this->_pdo = $pdo;
	}*/

        /**
	 * Returns the number of rows affected by the last SQL statement
	 * 
	 * @link http://www.php.net/manual/en/pdostatement.rowcount.php
	 * @return int the number of rows.
	 */
	public function rowCount()
	{
		// In case of sqllite, we don't have the row count
		// So we have to re run the statement in order to get the row count
		// And to avoid to move the current cursor
                $queryString = $this->getExecutedQueryString();
                $pos = strpos(strtolower($queryString),'select');
                if ($pos !== false && $pos<4) {//this is a select query
                    $this->useDebug = false;
                    $statement = $this->pdo->query($queryString);
                    $res =$statement->fetchAll();
                    $this->useDebug = true;
                    return count($res);
                } else {
                    return parent::rowCount();
                }
	}
}
