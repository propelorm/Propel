<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once 'AggregateColumnRelationBehavior.php';

/**
 * Keeps an aggregate column updated with related table
 *
 * @author     François Zaninotto, Jose F. D'Silva (jose.dsilva@bombayworks.se)
 * @version    $Revision$
 * @package    propel.generator.behavior.aggregate_column
 */
class AggregateColumnBehavior extends Behavior
{

	// default parameters value
	protected $parameters = array(
		'name'           => null,
		'expression'     => null,
		'foreign_table'  => null,
		'foreign_schema' => null,
	);

	/**
	 * Add the aggregate key to the current table
	 */
	public function modifyTable()
	{
		$table = $this->getTable();
		// add the aggregate columns if not present
		$columnNames = $this->getColumnNames();
		foreach ($columnNames as $columnName) {
			if( ! $table->containsColumn($columnName)) {
				$column = $table->addColumn(array(
					'name'    => $columnName,
					'type'    => 'INTEGER',
				));
			}
		}

		// add behavior to foreign tables to autoupdate aggregate columns
		$foreignTables = $this->getForeignTables();
		$columns = $this->getColumns(); // get aggregate column objects
		foreach ($foreignTables as $k => $foreignTable) {
			//var_dump($foreignTable); //MARK
			if(!$foreignTable->hasBehavior('concrete_inheritance_parent')) {
				$relationBehaviorName = 'aggregate_column_relation_to_' . $table->getName() . '_' . $columnNames[$k];
				$relationBehavior = new AggregateColumnRelationBehavior();
				$relationBehavior->setName($relationBehaviorName);
				// FIXME does this really serve any purpose other than check whether a fk is defined?
				$foreignKey = $this->getForeignKey($foreignTable);
				$relationBehavior->addParameter(array('name' => 'foreign_table', 'value' => $table->getName()));
				$relationBehavior->addParameter(array('name' => 'update_method', 'value' => 'update' . $columns[$k]->getPhpName()));
				$relationBehavior->addParameter(array('name' => 'foreign_aggregate_column', 'value' => $columns[$k]->getPhpName()));
				$foreignTable->addBehavior($relationBehavior);
			}
		}
	}

	public function objectMethods($builder)
	{
		$script = '';
		$aggregateColumns = $this->getColumns();
		$expressions = $this->getExpressions();
		$fullyQualifiedForeignTables = $this->getFullyQualifiedForeignTables();
		//var_dump(count($fullyQualifiedForeignTables)); //MARK
		$index = 0;
		foreach ($fullyQualifiedForeignTables as $data) {
			$script .= $this->addObjectCompute($data['FullyQualifiedName'], $data['ForeignTableName'], $data['TableObject'], $aggregateColumns[$index], $expressions[$index]);
			$script .= $this->addObjectUpdate($aggregateColumns[$index]);
			++ $index;
		}
		return $script;
	}

	protected function addObjectCompute($fullyQualifiedForeignTableName, $foreignTableName, $foreignTable, $aggregateColumn, $expression)
	{
		$conditions = array();
		$bindings = array();
		$database = $this->getTable()->getDatabase();
		foreach ($this->getForeignKey($foreignTable)->getColumnObjectsMapping() as $index => $columnReference) {
			$conditions[] = $columnReference['local']->getFullyQualifiedName() . ' = :p' . ($index + 1);
			$bindings[$index + 1]   = $columnReference['foreign']->getPhpName();
		}
		$sql = sprintf('SELECT %s FROM %s WHERE %s',
			$expression,
			$database->getPlatform()->quoteIdentifier($fullyQualifiedForeignTableName),
			implode(' AND ', $conditions)
		);

		return $this->renderTemplate('objectCompute', array(
			'column'   => $aggregateColumn,
			'sql'      => $sql,
			'bindings' => $bindings,
		));
	}

	protected function addObjectUpdate($aggregateColumn)
	{
		return $this->renderTemplate('objectUpdate', array(
			'column'  => $aggregateColumn,
		));
	}

	protected function getExpressions() {
		if(!($expressionString = $this->getParameter('expression')) )
			throw new InvalidArgumentException(sprintf('You must define an \'expression\' parameter for the \'aggregate_column\' behavior in the \'%s\' table', $this->getTable()->getName()));

		$expressions = explode(',', $expressionString);
		return array_map('trim', $expressions);
	}

	protected function getForeignTableNames() {
		if(!($foreignTableString = $this->getParameter('foreign_table')) ) {
			throw new InvalidArgumentException(sprintf('You must define a \'foreign_table\' parameter for the \'aggregate_column\' behavior in the \'%s\' table', $this->getTable()->getName()));
		}

		$foreignTableNames = explode(',', $foreignTableString);
		return array_map('trim', $foreignTableNames);
	}

	protected function getFullyQualifiedForeignTables() {
		$database = $this->getTable()->getDatabase();
		$foreignTableNames = $this->getForeignTableNames();
		$foreignSchemaString = $this->getParameter('foreign_schema');
		$hasForeignSchema = false;
		if($foreignSchemaString && $database->getPlatform()->supportsSchemas()) {
			$foreignSchemaNames = explode(',', $foreignSchemaString);
			$foreignSchemaNames = array_map('trim', $foreignSchemaNames);
			$hasForeignSchema = true;
		}

		$foreignTables = array();
		foreach ($foreignTableNames as $k => $foreignTableName) {
			$tableName = (($hasForeignSchema && $foreignSchemaNames[$k]) ? $foreignSchemaNames[$k] . '.' : '') . $database->getTablePrefix() . $foreignTableName;
			$foreignTables[] = array(
				'ForeignTableName' => $foreignTableName,
				'FullyQualifiedName' => $tableName,
				'TableObject' => $database->getTable($tableName),
			);
		}
		return $foreignTables;
	}

	protected function getForeignTables()
	{
		$database = $this->getTable()->getDatabase();
		$foreignTableNames = $this->getForeignTableNames();
		//var_dump($foreignTableNames); //MARK
		$foreignSchemaString = $this->getParameter('foreign_schema');
		$hasForeignSchema = false;
		if($foreignSchemaString && $database->getPlatform()->supportsSchemas()) {
			$foreignSchemaNames = explode(',', $foreignSchemaString);
			$hasForeignSchema = true;
		}

		$foreignTables = array();
		foreach ($foreignTableNames as $k => $foreignTableName) {
			$tableName = (($hasForeignSchema && $foreignSchemaNames[$k]) ? $foreignSchemaNames[$k] . '.' : '') . $database->getTablePrefix() . $foreignTableName;
			$foreignTables[] = $database->getTable($tableName);
		}

		return $foreignTables;
	}

	protected function getForeignKeys() {
		$foreignKeys = array();
		foreach ($this->getForeignTables() as $foreignTable) {
			$foreignKeys[] = $this->getForeignKey($foreignTable);
		}
		return $foreignKeys;
	}

	protected function getForeignKey($foreignTable)
	{
		// let's infer the relation from the foreign table
		$fks = $foreignTable->getForeignKeysReferencingTable($this->getTable()->getName());
		if (!$fks) {
			throw new InvalidArgumentException(sprintf('You must define a foreign key to the \'%s\' table in the \'%s\' table to enable the \'aggregate_column\' behavior', $this->getTable()->getName(), $foreignTable->getName()));
		}
		// FIXME doesn't work when more than one fk to the same table
		return array_shift($fks);
	}

	protected function getColumnNames() {
		if ( !($columnNameString = $this->getParameter('name')) ) {
			throw new InvalidArgumentException(sprintf('You must define a \'name\' parameter for the \'aggregate_column\' behavior in the \'%s\' table', $this->getTable()->getName()));
		}

		$columnNames = explode(',', $columnNameString);
		return array_map('trim', $columnNames);
	}

	protected function getColumns()
	{
		$columnNames = $this->getColumnNames();
		$columns = array();
		$table = $this->getTable();
		foreach($columnNames as $columnName) {
			$columns[] = $table->getColumn($columnName);
		}
		return $columns;
	}

}