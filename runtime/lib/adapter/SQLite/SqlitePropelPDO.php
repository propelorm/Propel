<?php

class SqlitePropelPDO extends PropelPDO
{
	function __construct($dsn,$username = null,$password = null,array $driver_options = null)
	{
		parent::__construct($dsn, $username, $password, $driver_options);
		
		$this->configureStatementClass('SqlitePropelPDOStatement', true);
	}
	
    /**
     * Enable or disable the query debug features
     *
     * @param boolean $value True to enable debug (default), false to disable it
     */
    public function useDebug($value = true)
    {
        if ($value) {
            $this->configureStatementClass('SqliteDebugPDOStatement', true);
        } else {
            // reset query logging
            $this->configureStatementClass('SqlitePropelPDOStatement');
            
            $this->setLastExecutedQuery('');
            $this->queryCount = 0;
        }
        $this->clearStatementCache();
        $this->useDebug = $value;
    }
}
