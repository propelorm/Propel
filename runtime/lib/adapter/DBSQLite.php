<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * This is used in order to connect to a SQLite database.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @version    $Revision$
 * @package    propel.runtime.adapter
 */
class DBSQLite extends DBAdapter
{
    /**
     * Prepare connection parameters.
     *
     * @param  array $params
     * @return array
     *
     * @throws PropelException
     */
    public function prepareParams($params)
    {
        $params = parent::prepareParams($params);

        if (isset($params['classname']) && $params['classname'] == 'DebugPDO') {
            $params['classname'] = 'SqliteDebugPDO';
        } else {
            $params['classname'] = 'SqlitePropelPDO';
        }
        return $params;
    }

    /**
     * For SQLite this method has no effect, since SQLite doesn't support specifying a character
     * set (or, another way to look at it, it doesn't require a single character set per DB).
     *
     * @param PDO    $con     A PDO connection instance.
     * @param string $charset The charset encoding.
     *
     * @throws PropelException If the specified charset doesn't match sqlite_libencoding()
     */
    public function setCharset(PDO $con, $charset)
    {
    }

    /**
     * This method is used to ignore case.
     *
     * @param  string $in The string to transform to upper case.
     * @return string The upper case string.
     */
    public function toUpperCase($in)
    {
        return 'UPPER(' . $in . ')';
    }

    /**
     * This method is used to ignore case.
     *
     * @param  string $in The string whose case to ignore.
     * @return string The string in a case that can be ignored.
     */
    public function ignoreCase($in)
    {
        return 'UPPER(' . $in . ')';
    }

    /**
     * Returns SQL which concatenates the second string to the first.
     *
     * @param string $s1 String to concatenate.
     * @param string $s2 String to append.
     *
     * @return string
     */
    public function concatString($s1, $s2)
    {
        return "($s1 || $s2)";
    }

    /**
     * Returns SQL which extracts a substring.
     *
     * @param string  $s   String to extract from.
     * @param integer $pos Offset to start from.
     * @param integer $len Number of characters to extract.
     *
     * @return string
     */
    public function subString($s, $pos, $len)
    {
        return "substr($s, $pos, $len)";
    }

    /**
     * Returns SQL which calculates the length (in chars) of a string.
     *
     * @param  string $s String to calculate length of.
     * @return string
     */
    public function strLength($s)
    {
        return "length($s)";
    }

    /**
     * @see       DBAdapter::quoteIdentifier()
     *
     * @param  string $text
     * @return string
     */
    public function quoteIdentifier($text)
    {
        return '`' . $text . '`';
    }

    /**
     * @see       DBAdapter::quoteIdentifierTable()
     *
     * @param  string $table
     * @return string
     */
    public function quoteIdentifierTable($table)
    {
        // e.g. 'database.table alias' should be escaped as '`database`.`table` `alias`'
        return '`' . strtr($table, array('.' => '`.`', ' ' => '` `')) . '`';
    }

    /**
     * @see       DBAdapter::useQuoteIdentifier()
     *
     * @return boolean
     */
    public function useQuoteIdentifier()
    {
        return true;
    }


    /**
     * @see        DBAdapter::applyLimit()
     *
     * @param string  $sql
     * @param integer $offset
     * @param integer $limit
     */
    public function applyLimit(&$sql, $offset, $limit)
    {
        if ( $limit > 0 ) {
            $sql .= " LIMIT " . ($offset > 0 ? $offset . ", " : "") . $limit;
        } elseif ( $offset > 0 ) {
            $sql .= " LIMIT " . $offset . ", 18446744073709551615";
        }
    }

    /**
     * @param  string $seed
     * @return string
     */
    public function random($seed = NULL)
    {
        return 'random()';
    }
    
    /**
     * Do Explain Plan for query object or query string
     *
     * @param  PropelPDO            $con   propel connection
     * @param  ModelCriteria|string $query query the criteria or the query string
     * @throws PropelException
     * @return PDOStatement         A PDO statement executed using the connection, ready to be fetched
     */
    public function doExplainPlan(PropelPDO $con, $query)
    {
        if ($query instanceof ModelCriteria) {
            $params = array();
            $dbMap = Propel::getDatabaseMap($query->getDbName());
            $sql = BasePeer::createSelectSql($query, $params);
            $sql = 'EXPLAIN ' . $sql;
        } else {
            $sql = 'EXPLAIN ' . $query;
        }

        $stmt = $con->prepare($sql);

        if ($query instanceof ModelCriteria) {
            $this->bindValues($stmt, $params, $dbMap);
        }

        $stmt->execute();

        return $stmt;
    }
}
