<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * dblib doesn't support transactions so we need to add a workaround for transactions, last insert ID, and quoting
 *
 * @package    propel.runtime.adapter.MSSQL
 */
class MssqlPropelPDO extends PropelPDO
{
    /**
     * Begin a transaction.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     *
     * @return integer
     */
    public function beginTransaction()
    {
        $return = self::exec('BEGIN TRANSACTION');
        if ($this->useDebug) {
            $this->log('Begin transaction', null, __METHOD__);
        }

        return $return;
    }

    /**
     * Commit a transaction.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     *
     * @return integer
     *
     * @throws PropelException
     */
    public function commit()
    {
        $return = self::exec('COMMIT TRANSACTION');
        if ($this->useDebug) {
            $this->log('Commit transaction', null, __METHOD__);
        }

        return $return;
    }

    /**
     * Roll-back a transaction.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     *
     * @return integer
     */
    public function rollBack()
    {
        $return = self::exec('ROLLBACK TRANSACTION');
        if ($this->useDebug) {
            $this->log('Rollback transaction', null, __METHOD__);
        }

        return $return;
    }

    /**
     * Rollback the whole transaction, even if this is a nested rollback
     * and reset the nested transaction count to 0.
     *
     * It is necessary to override the abstract PDO transaction functions here, as
     * the PDO driver for MSSQL does not support transactions.
     *
     * @return integer
     */
    public function forceRollBack()
    {
        // If we're in a transaction, always roll it back
        // regardless of nesting level.
        $return = self::exec('ROLLBACK TRANSACTION');

        if ($this->useDebug) {
            $this->log('Rollback transaction', null, __METHOD__);
        }

        return $return;
    }

    /**
     * @param string $seqname
     *
     * @return integer
     */
    public function lastInsertId($seqname = null)
    {
        $result = self::query('SELECT SCOPE_IDENTITY()');

        return (int) $result->fetchColumn();
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function quoteIdentifier($text)
    {
        return '[' . $text . ']';
    }

    /**
     * @return boolean
     */
    public function useQuoteIdentifier()
    {
        return true;
    }
}
