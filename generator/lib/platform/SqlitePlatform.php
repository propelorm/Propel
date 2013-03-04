<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/DefaultPlatform.php';

/**
 * SQLite PropelPlatformInterface implementation.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @version    $Revision$
 * @package    propel.generator.platform
 */
class SqlitePlatform extends DefaultPlatform
{

    /**
     * If we should generate FOREIGN KEY statements.
     * This is since SQLite version 3.6.19 possible.
     *
     * @var bool
     */
    private $foreignKeySupport = true;

    /**
     * If we should alter the table through creating a temporarily crated table,
     * moving all item to the new one and finally rename the temp table.
     *
     * @var bool
     */
    private $tableAlteringWorkaround = true;

    /**
     * Initializes db specific domain mapping.
     */
    protected function initialize()
    {
        parent::initialize();
        $this->setSchemaDomainMapping(new Domain(PropelTypes::NUMERIC, "DECIMAL"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::LONGVARCHAR, "MEDIUMTEXT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::DATE, "DATETIME"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BINARY, "BLOB"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::VARBINARY, "MEDIUMBLOB"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::LONGVARBINARY, "LONGBLOB"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BLOB, "LONGBLOB"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::CLOB, "LONGTEXT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::OBJECT, "MEDIUMTEXT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::PHP_ARRAY, "MEDIUMTEXT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::ENUM, "TINYINT"));
    }

    /**
     * @link       http://www.sqlite.org/autoinc.html
     */
    public function getAutoIncrement()
    {
        return "PRIMARY KEY AUTOINCREMENT";
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxColumnNameLength()
    {
        return 1024;
    }


    /**
     * {@inheritdoc}
     */
    public function setGeneratorConfig(GeneratorConfigInterface $generatorConfig)
    {
        if ($foreignKeySupport = $generatorConfig->getBuildProperty('sqliteForeignkey')) {
            $this->foreignKeySupport = !!$foreignKeySupport;
        }
        if ($tableAlteringWorkaround = $generatorConfig->getBuildProperty('sqliteTableAlteringWorkaround')) {
            $this->tableAlteringWorkaround = !!$tableAlteringWorkaround;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getModifyTableDDL(PropelTableDiff $tableDiff)
    {

        $this->checkCompositePk($tableDiff->getToTable());

        $changedNotEditableThroughDirectDDL = $this->tableAlteringWorkaround && (false
            || $tableDiff->hasModifiedFks()
            || $tableDiff->hasModifiedIndices()
            || $tableDiff->hasModifiedColumns()
            || $tableDiff->hasRenamedColumns()

            || $tableDiff->hasRemovedFks()
            || $tableDiff->hasRemovedIndices()
            || $tableDiff->hasRemovedColumns()

            || $tableDiff->hasAddedIndices()
            || $tableDiff->hasAddedFks()
            || $tableDiff->hasAddedPkColumns()
        );

        if ($this->tableAlteringWorkaround && !$changedNotEditableThroughDirectDDL && $tableDiff->hasAddedColumns()){

            $addedCols = $tableDiff->getAddedColumns();
            foreach ($addedCols as $column){

                $sqlChangeNotSupported = false

                    //The column may not have a PRIMARY KEY or UNIQUE constraint.
                    || $column->isPrimaryKey()
                    || $column->isUnique()

                    //The column may not have a default value of CURRENT_TIME, CURRENT_DATE, CURRENT_TIMESTAMP,
                    //or an expression in parentheses.
                    || false !== array_search(
                        $column->getDefaultValue(), array('CURRENT_TIME', 'CURRENT_DATE', 'CURRENT_TIMESTAMP'))
                    || substr(trim($column->getDefaultValue()), 0, 1) == '('

                    //If a NOT NULL constraint is specified, then the column must have a default value other than NULL.
                    || ($column->isNotNull() && $column->getDefaultValue() == 'NULL')
                ;

                if ($sqlChangeNotSupported){
                    $changedNotEditableThroughDirectDDL = true;
                    break;
                }

            }
        }

        if ($changedNotEditableThroughDirectDDL){
            return $this->getMigrationTableDDL($tableDiff);
        } else {
            return parent::getModifyTableDDL($tableDiff);
        }

    }

    /**
     * Creates a temporarily created table with the new schema,
     * moves all items into it and drops the origin as well as renames the temp table to the origin then.
     *
     * @param PropelTableDiff $tableDiff
     * @return string
     */
    public function getMigrationTableDDL(PropelTableDiff $tableDiff){

        $pattern = "
%s;
INSERT INTO %s (%s)
    SELECT %s FROM %s;
DROP TABLE %s;
ALTER TABLE %s RENAME TO %s;
";

        $originTable     = $tableDiff->getFromTable();
        $newTable        = $tableDiff->getToTable();

        $originTableName = $originTable->getName();
        $tempTableName   = $newTable->getCommonName().'__migration_temp';
        $newTable->setCommonName($tempTableName);

        $newTableFields    = $this->getColumnListDDL($newTable->getColumns());
        $originTableFields = $this->getColumnListDDL($originTable->getColumns());

        //todo check field diff



        $sql = sprintf($pattern,
            $this->getAddTableDDL($newTable),
            $this->quoteIdentifier($originTableName),
            $newTableFields,
            $originTableFields,
            $this->quoteIdentifier($originTableName),
            $this->quoteIdentifier($originTableName),
            $this->quoteIdentifier($tempTableName),
            $this->quoteIdentifier($originTableName)
        );

        if ($this->foreignKeySupport){
            $sql = sprintf("
PRAGMA foreign_keys = OFF;
%s
PRAGMA foreign_keys = ON;
",
            $sql);
        }

        return $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddTablesDDL(Database $database)
    {
        $ret = $this->getBeginDDL();
        foreach ($database->getTablesForSql() as $table) {
            $ret .= $this->getCommentBlockDDL($table->getName());
            $ret .= $this->getDropTableDDL($table);
            $ret .= $this->getAddTableDDL($table);
            $ret .= $this->getAddIndicesDDL($table);
        }
        $ret .= $this->getEndDDL();

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddTableDDL(Table $table)
    {
        $tableDescription = $table->hasDescription() ? $this->getCommentLineDDL($table->getDescription()) : '';

        $this->checkCompositePk($table);

        $lines = array();

        foreach ($table->getColumns() as $column) {
            $lines[] = $this->getColumnDDL($column);
        }

        if ($table->hasPrimaryKey()) {
          $pk = $table->getPrimaryKey();
          if (count($pk) > 1 || !$pk[0]->isAutoIncrement()) {
            $lines[] = $this->getPrimaryKeyDDL($table);
          }
        }

        foreach ($table->getUnices() as $unique) {
            $lines[] = $this->getUniqueDDL($unique);
        }

        foreach ($table->getForeignKeys() as $foreignKey) {
            if ($line = $this->getForeignKeyDDL($foreignKey)){
                $lines[] = str_replace("\n", "\n    ", $line);
            }
        }

        $sep = ",
    ";

        $pattern = "
%sCREATE TABLE %s
(
    %s
);
";

        return sprintf($pattern,
            $tableDescription,
            $this->quoteIdentifier($table->getName()),
            implode($sep, $lines)
        );
    }

    /**
     * Unfortunately, SQLite does not support composite pks where one is AUTOINCREMENT,
     * so we have so flag both as NOT NULL and create a UNIQUE constraint.
     *
     * @param Table $table
     */
    public function checkCompositePk(Table $table)
    {
        if (count($pks = $table->getPrimaryKey()) > 1 && $table->hasAutoIncrementPrimaryKey()){
            foreach ($pks as $pk){
                //no pk can be NULL, as usual
                $pk->setNotNull(true);
                //in SQLite the column with the AUTOINCREMENT MUST be a primary key, too.
                if (!$pk->isAutoIncrement()){
                    //for all other sub keys we remove it, since we create a UNIQUE constraint over all primary keys.
                    $pk->setPrimaryKey(false);
                }
            }

            //search if there is already a UNIQUE constraint over the primary keys
            $pkUniqueExist = false;
            foreach ($table->getUnices() as $unique){
                $allPk = false;
                foreach ($unique->getColumns() as $columnName){
                    $allPk &= $table->getColumn($columnName)->isPrimaryKey();
                }
                if ($allPk){
                    //there's already a unique constraint with the composite pk
                    $pkUniqueExist = true;
                    break;
                }
            }

            //there is none, let's create it
            if (!$pkUniqueExist){
                $unique = new Unique();
                foreach ($pks as $pk){
                    $unique->addColumn($pk);
                }
                $table->addUnique($unique);
            }
        }

    }


    /**
     * Returns the SQL for the primary key of a Table object
     * @return string
     */
    public function getPrimaryKeyDDL(Table $table)
    {

        return parent::getPrimaryKeyDDL($table);
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoveColumnDDL(Column $column){
        //not supported
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getRenameColumnDDL($fromColumn, $toColumn)
    {
        //not supported
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getModifyColumnDDL(PropelColumnDiff $columnDiff){
        //not supported
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getModifyColumnsDDL($columnDiffs){
        //not supported
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getDropPrimaryKeyDDL(Table $table)
    {
        //not supported
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getAddPrimaryKeyDDL(Table $table)
    {
        //not supported
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getAddForeignKeyDDL(ForeignKey $fk)
    {
        //not supported
        return '
--- SQLite does not support altering foreign keys directly; this is just for reference
';
    }

    /**
     * {@inheritdoc}
     */
    public function getDropForeignKeyDDL(ForeignKey $fk)
    {
        //not supported
        return '';
    }

    public function getDropTableDDL(Table $table)
    {
        return "
DROP TABLE IF EXISTS " . $this->quoteIdentifier($table->getName()) . ";
";
    }

    public function getForeignKeyDDL(ForeignKey $fk)
    {

        if ($fk->isSkipSql() || !$this->foreignKeySupport) {
            return;
        }

        $pattern = "FOREIGN KEY (%s) REFERENCES %s (%s)";

        $script = sprintf($pattern,
            $this->getColumnListDDL($fk->getLocalColumns()),
            $fk->getForeignTableName(),
            $this->getColumnListDDL($fk->getForeignColumns())
        );

        if ($fk->hasOnUpdate()) {
            $script .= "
    ON UPDATE " . $fk->getOnUpdate();
        }
        if ($fk->hasOnDelete()) {
            $script .= "
    ON DELETE " . $fk->getOnDelete();
        }

        return $script;
    }

    public function hasSize($sqlType)
    {
        return !("MEDIUMTEXT" == $sqlType || "LONGTEXT" == $sqlType
                || "BLOB" == $sqlType || "MEDIUMBLOB" == $sqlType
                || "LONGBLOB" == $sqlType);
    }

    /**
     * Escape the string for RDBMS.
     * @param  string $text
     * @return string
     */
    public function disconnectedEscapeText($text)
    {
        if (function_exists('sqlite_escape_string')) {
            return sqlite_escape_string($text);
        } else {
            return parent::disconnectedEscapeText($text);
        }
    }

    public function quoteIdentifier($text)
    {
        return $this->isIdentifierQuotingEnabled ? '[' . $text . ']' : $text;
    }
}
