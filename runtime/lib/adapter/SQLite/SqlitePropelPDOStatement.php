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
class SQLitePropelPDOStatement extends PDOStatement
{
 	/**
	 * 
	 * @var \Pdo
	 */
	private $pdo;

        /**
         * @var       array  The values that have been bound
         */
        protected $boundValues = array();

        /**
	 * 
	 * Constructor
	 * 
	 * @param \Pdo $pdo
	 */
	protected function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

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
                    $statement = $this->pdo->query($queryString);
                    $res =$statement->fetchAll();
                    return count($res);
                } else {
                    return parent::rowCount();
                }
	}

    /**
     * @return string
     */
    public function getExecutedQueryString()
    {
        $sql = $this->queryString;
        $matches = array();
        if (preg_match_all('/(:p[0-9]+\b)/', $sql, $matches)) {
            $size = count($matches[1]);
            for ($i = $size-1; $i >= 0; $i--) {
                $pos = $matches[1][$i];
                $sql = str_replace($pos, $this->boundValues[$pos], $sql);
            }
        }
        return $sql;
    }

     /**
     * Binds a value to a corresponding named or question mark placeholder in the SQL statement
     * that was use to prepare the statement. Returns a boolean value indicating success.
     *
     * @param integer $pos   Parameter identifier (for determining what to replace in the query).
     * @param mixed   $value The value to bind to the parameter.
     * @param integer $type  Explicit data type for the parameter using the PDO::PARAM_* constants. Defaults to PDO::PARAM_STR.
     *
     * @return boolean
     */
    public function bindValue($pos, $value, $type = PDO::PARAM_STR)
    {
        $return   = parent::bindValue($pos, $value, $type);
        $valuestr = $type == PDO::PARAM_LOB ? '[LOB value]' : var_export($value, true);

        $this->boundValues[$pos] = $valuestr;

        return $return;
    }

    /**
     * Binds a PHP variable to a corresponding named or question mark placeholder in the SQL statement
     * that was use to prepare the statement. Unlike PDOStatement::bindValue(), the variable is bound
     * as a reference and will only be evaluated at the time that PDOStatement::execute() is called.
     * Returns a boolean value indicating success.
     *
     * @param integer $pos            Parameter identifier (for determining what to replace in the query).
     * @param mixed   $value          The value to bind to the parameter.
     * @param integer $type           Explicit data type for the parameter using the PDO::PARAM_* constants. Defaults to PDO::PARAM_STR.
     * @param integer $length         Length of the data type. To indicate that a parameter is an OUT parameter from a stored procedure, you must explicitly set the length.
     * @param mixed   $driver_options
     *
     * @return boolean
     */
    public function bindParam($pos, &$value, $type = PDO::PARAM_STR, $length = 0, $driver_options = null)
    {
        $return   = parent::bindParam($pos, $value, $type, $length, $driver_options);
        $valuestr = $length > 100 ? '[Large value]' : var_export($value, true);

        $this->boundValues[$pos] = $valuestr;

        return $return;
    }
}
