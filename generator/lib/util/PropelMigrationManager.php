<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../model/Table.php';
require_once dirname(__FILE__) . '/../model/Column.php';
require_once dirname(__FILE__) . '/PropelSQLParser.php';
require_once dirname(__FILE__) . '/../../../runtime/lib/Propel.php';

/**
 * Service class for preparing and executing migrations
 *
 * @author     François Zaninotto
 * @version    $Revision$
 * @package    propel.generator.util
 */
class PropelMigrationManager
{
	protected $connections;
	protected $pdoConnections = array();
	protected $migrationTable = 'propel_migration';
	protected $migrationLogTable = 'propel_migration_log';
	protected $migrationDir;

	/**
	 * Set the database connection settings
	 *
	 * @param array $connections
	 */
	public function setConnections($connections)
	{
		$this->connections = $connections;
	}

	/**
	 * Get the database connection settings
	 *
	 * @return array
	 */
	public function getConnections()
	{
		return $this->connections;
	}

	public function getConnection($datasource)
	{
		if (!isset($this->connections[$datasource])) {
			throw new InvalidArgumentException(sprintf('Unknown datasource "%s"', $datasource));
		}

		return $this->connections[$datasource];
	}

	public function getPdoConnection($datasource)
	{
		if (!isset($this->pdoConnections[$datasource])) {
			$buildConnection = $this->getConnection($datasource);
			$buildConnection['dsn'] = str_replace("@DB@", $datasource, $buildConnection['dsn']);

			$this->pdoConnections[$datasource] = Propel::initConnection($buildConnection, $datasource);
		}

		return $this->pdoConnections[$datasource];
	}

	public function getPlatform($datasource)
	{
		$params = $this->getConnection($datasource);
		$adapter = $params['adapter'];
		$adapterClass = ucfirst($adapter) . 'Platform';
		require_once sprintf('%s/../platform/%s.php',
				dirname(__FILE__),
				$adapterClass
		);

		return new $adapterClass();
	}

	/**
	 * Set the migration table name
	 *
	 * @param string $migrationTable
	 */
	public function setMigrationTable($migrationTable)
	{
		$this->migrationTable = $migrationTable;
	}

	/**
	 * get the migration table name
	 *
	 * @return string
	 */
	public function getMigrationTable()
	{
		return $this->migrationTable;
	}

	public function getMigrationLogTable()
	{
		return $this->migrationLogTable;
	}

	/**
	 * Set the path to the migration classes
	 *
	 * @param string $migrationDir
	 */
	public function setMigrationDir($migrationDir)
	{
		$this->migrationDir = $migrationDir;
	}

	/**
	 * Get the path to the migration classes
	 *
	 * @return string
	 */
	public function getMigrationDir()
	{
		return $this->migrationDir;
	}

	public function getOldestDatabaseVersion()
	{
		if (!$connections = $this->getConnections()) {
			throw new Exception('You must define database connection settings in a buildtime-conf.xml file to use migrations');
		}
		$oldestMigrationTimestamp = null;
		$migrationTimestamps = array();
		foreach ($connections as $name => $params) {
			$pdo = $this->getPdoConnection($name);
			$sql = sprintf('SELECT version FROM %s', $this->getMigrationTable());

			try {
				$stmt = $pdo->prepare($sql);
				$stmt->execute();
				if ($migrationTimestamp = $stmt->fetchColumn()) {
					$migrationTimestamps[$name] = $migrationTimestamp;
				}
			} catch (PDOException $e) {
				$this->createMigrationTable($name);
				$oldestMigrationTimestamp = 0;
			}
		}
		if ($oldestMigrationTimestamp === null && $migrationTimestamps) {
			sort($migrationTimestamps);
			$oldestMigrationTimestamp = array_shift($migrationTimestamps);
		}

		return $oldestMigrationTimestamp;
	}

	public function migrationTableExists($datasource)
	{
		$pdo = $this->getPdoConnection($datasource);
		$sql = sprintf('SELECT version FROM %s', $this->getMigrationTable());
		$stmt = $pdo->prepare($sql);
		try {
			$stmt->execute();

			return true;
		} catch (PDOException $e) {
			return false;
		}
	}

	public function ensureMigrationLogTableExists($datasource): void
	{
		if (!$this->migrationLogTableExists($datasource)) {
			$this->createMigrationLogTable($datasource);
		}
	}

	public function migrationLogTableExists($datasource)
	{
		$pdo = $this->getPdoConnection($datasource);
		$sql = sprintf('SELECT id FROM %s', $this->getMigrationLogTable());
		$stmt = $pdo->prepare($sql);
		try {
			$stmt->execute();

			return true;
		} catch (PDOException $e) {
			return false;
		}
	}

	public function createMigrationTable($datasource)
	{
		$platform = $this->getPlatform($datasource);
		// modelize the table
		$database = new Database($datasource);
		$database->setPlatform($platform);
		$table = new Table($this->getMigrationTable());
		$database->addTable($table);
		$column = new Column('version');
		$column->getDomain()->copy($platform->getDomainForType('INTEGER'));
		$column->setDefaultValue(0);
		$table->addColumn($column);
		// insert the table into the database
		$statements = $platform->getAddTableDDL($table);
		$pdo = $this->getPdoConnection($datasource);
		$res = PropelSQLParser::executeString($statements, $pdo);
		if (!$res) {
			throw new Exception(sprintf('Unable to create migration table in datasource "%s"', $datasource));
		}
	}

	public function createMigrationLogTable($datasource)
	{
		$platform = $this->getPlatform($datasource);
		// modelize the table
		$database = new Database($datasource);
		$database->setPlatform($platform);
		$table = new Table($this->getMigrationLogTable());
		$database->addTable($table);

		// id
		$column = new Column('id');
		$column->getDomain()->copy($platform->getDomainForType('INTEGER'));
		$column->setAutoIncrement(true);
		$column->setPrimaryKey(true);
		$table->addColumn($column);

		// name
		$column = new Column('name');
		$column->getDomain()->copy($platform->getDomainForType('VARCHAR'));
		$column->setSize(255);
		$table->addColumn($column);

		// action
		$column = new Column('action');
		$column->getDomain()->copy($platform->getDomainForType('VARCHAR'));
		$column->setSize(255);
		$table->addColumn($column);

		// executed_at
		$column = new Column('executed_at');
		$column->getDomain()->copy($platform->getDomainForType('TIMESTAMP'));
		$table->addColumn($column);

		// insert the table into the database
		$statements = $platform->getAddTableDDL($table);
		$pdo = $this->getPdoConnection($datasource);
		$res = PropelSQLParser::executeString($statements, $pdo);
		if (!$res) {
			throw new Exception(sprintf('Unable to create migration table in datasource "%s"', $datasource));
		}
	}

	public function updateLatestMigrationTimestamp($datasource, $timestamp)
	{
		$platform = $this->getPlatform($datasource);
		$pdo = $this->getPdoConnection($datasource);
		$sql = sprintf('DELETE FROM %s', $this->getMigrationTable());
		$pdo->beginTransaction();
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$sql = sprintf('INSERT INTO %s (%s) VALUES (?)',
				$this->getMigrationTable(),
				$platform->quoteIdentifier('version')
		);
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $timestamp, PDO::PARAM_INT);
		$stmt->execute();
		$pdo->commit();
	}

	// Takes a migration script name and logs it to the migration log table
	public function logMigrationScriptExecution($datasource, string $scriptName, string $action): void
	{
		$platform = $this->getPlatform($datasource);
		$pdo = $this->getPdoConnection($datasource);
		$sql = sprintf('INSERT INTO %s (%s, %s, %s) VALUES (?, ?, ?)',
				$this->getMigrationLogTable(),
				$platform->quoteIdentifier('name'),
				$platform->quoteIdentifier('action'),
				$platform->quoteIdentifier('executed_at')
		);
		//get current date/time as string with milliseconds
		$now = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 8);
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(1, $scriptName, PDO::PARAM_STR);
		$stmt->bindParam(2, $action, PDO::PARAM_STR);
		$stmt->bindParam(3, $now, PDO::PARAM_STR);
		$stmt->execute();
	}

	public function getMigrationTimestamps()
	{
		$path = $this->getMigrationDir();
		$migrationTimestamps = array();

		if (is_dir($path)) {
			$files = scandir($path);
			foreach ($files as $file) {
				if (preg_match('/^PropelMigration_(\d+)\.php$/', $file, $matches)) {
					$migrationTimestamps[] = (integer)$matches[1];
				}
			}
		}

		return $migrationTimestamps;
	}

	public function getValidMigrationTimestamps()
	{
		$oldestMigrationTimestamp = $this->getOldestDatabaseVersion();
		$migrationTimestamps = $this->getMigrationTimestamps();
		// removing already executed migrations
		foreach ($migrationTimestamps as $key => $timestamp) {
			if ($timestamp <= $oldestMigrationTimestamp) {
				unset($migrationTimestamps[$key]);
			}
		}
		sort($migrationTimestamps);

		return $migrationTimestamps;
	}

	public function hasPendingMigrations()
	{
		return array() !== $this->getValidMigrationTimestamps();
	}

	public function getAlreadyExecutedMigrationTimestamps()
	{
		$oldestMigrationTimestamp = $this->getOldestDatabaseVersion();
		$migrationTimestamps = $this->getMigrationTimestamps();
		// removing already executed migrations
		foreach ($migrationTimestamps as $key => $timestamp) {
			if ($timestamp > $oldestMigrationTimestamp) {
				unset($migrationTimestamps[$key]);
			}
		}
		sort($migrationTimestamps);

		return $migrationTimestamps;
	}

	public function getFirstUpMigrationTimestamp()
	{
		$validTimestamps = $this->getValidMigrationTimestamps();

		return array_shift($validTimestamps);
	}

	public function getFirstDownMigrationTimestamp()
	{
		return $this->getOldestDatabaseVersion();
	}

	public static function getMigrationClassName($timestamp)
	{
		return sprintf('PropelMigration_%d', $timestamp);
	}

	public function getMigrationObject($timestamp): string
	{
		$className = $this->getMigrationClassName($timestamp);
		require_once sprintf('%s/%s.php',
				$this->getMigrationDir(),
				$className
		);

		return new $className();
	}

	public function getMigrationClassBody($migrationsUp, $migrationsDown, $timestamp)
	{
		$timeInWords = date('Y-m-d H:i:s', $timestamp);
		$migrationAuthor = ($author = $this->getUser()) ? 'by ' . $author : '';
		$migrationClassName = $this->getMigrationClassName($timestamp);
		$migrationUpString = var_export($migrationsUp, true);
		$migrationDownString = var_export($migrationsDown, true);
		$migrationClassBody = <<<EOP
<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version $timestamp.
 * Generated on $timeInWords $migrationAuthor
 */
class $migrationClassName
{

    public function preUp(\$manager)
    {
        // add the pre-migration code here
    }

    public function postUp(\$manager)
    {
        // add the post-migration code here
    }

    public function preDown(\$manager)
    {
        // add the pre-migration code here
    }

    public function postDown(\$manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return $migrationUpString;
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return $migrationDownString;
    }

}
EOP;

		return $migrationClassBody;
	}

	public static function getMigrationFileName($timestamp)
	{
		return sprintf('%s.php', self::getMigrationClassName($timestamp));
	}

	public static function getUser()
	{
		if (function_exists('posix_getuid')) {
			$currentUser = posix_getpwuid(posix_getuid());
			if (isset($currentUser['name'])) {
				return $currentUser['name'];
			}
		}

		return '';
	}
}
