<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/DefaultPlatform.php';
require_once dirname(__FILE__) . '/../model/Domain.php';

/**
 * MS SQL PropelPlatformInterface implementation.
 *
 * @author     Hans Lellelid <hans@xmpl.org> (Propel)
 * @author     Martin Poeschl <mpoeschl@marmot.at> (Torque)
 * @version    $Revision$
 * @package    propel.generator.platform
 */
class MssqlPlatform extends DefaultPlatform
{
    protected static $dropCount = 0;

    /**
     * Initializes db specific domain mapping.
     */
    protected function initialize()
    {
        parent::initialize();
        $this->setSchemaDomainMapping(new Domain(PropelTypes::INTEGER, "INT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BOOLEAN, "INT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::DOUBLE, "FLOAT"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::LONGVARCHAR, "VARCHAR(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::CLOB, "VARCHAR(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::DATE, "DATETIME"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BU_DATE, "DATETIME"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::TIME, "DATETIME"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::TIMESTAMP, "DATETIME"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BU_TIMESTAMP, "DATETIME"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BINARY, "BINARY(7132)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::VARBINARY, "VARBINARY(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::LONGVARBINARY, "VARBINARY(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::BLOB, "VARBINARY(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::OBJECT, "VARCHAR(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::PHP_ARRAY, "VARCHAR(MAX)"));
        $this->setSchemaDomainMapping(new Domain(PropelTypes::ENUM, "TINYINT"));
    }

    public function getMaxColumnNameLength()
    {
        return 128;
    }

    public function getNullString($notNull)
    {
        return ($notNull ? "NOT NULL" : "NULL");
    }

    public function supportsNativeDeleteTrigger()
    {
        return true;
    }

    public function supportsInsertNullPk()
    {
        return false;
    }

    public function getDropTableDDL(Table $table)
    {
        $ret = '';
        foreach ($table->getForeignKeys() as $fk) {
            $ret .= "
IF EXISTS (SELECT 1 FROM sysobjects WHERE type ='RI' AND name='" . $fk->getName() . "')
    ALTER TABLE " . $this->quoteIdentifier($table->getName()) . " DROP CONSTRAINT " . $this->quoteIdentifier($fk->getName()) . ";
";
        }

        self::$dropCount++;

        $ret .= "
IF EXISTS (SELECT 1 FROM sysobjects WHERE type = 'U' AND name = '" . $table->getName() . "')
BEGIN
    DECLARE @reftable_" . self::$dropCount . " nvarchar(60), @constraintname_" . self::$dropCount . " nvarchar(60)
    DECLARE refcursor CURSOR FOR
    select reftables.name tablename, cons.name constraintname
        from sysobjects tables,
            sysobjects reftables,
            sysobjects cons,
            sysreferences ref
        where tables.id = ref.rkeyid
            and cons.id = ref.constid
            and reftables.id = ref.fkeyid
            and tables.name = '" . $table->getName() . "'
    OPEN refcursor
    FETCH NEXT from refcursor into @reftable_" . self::$dropCount . ", @constraintname_" . self::$dropCount . "
    while @@FETCH_STATUS = 0
    BEGIN
        exec ('alter table '+@reftable_" . self::$dropCount . "+' drop constraint '+@constraintname_" . self::$dropCount . ")
        FETCH NEXT from refcursor into @reftable_" . self::$dropCount . ", @constraintname_" . self::$dropCount . "
    END
    CLOSE refcursor
    DEALLOCATE refcursor
    DROP TABLE " . $this->quoteIdentifier($table->getName()) . "
END
";

        return $ret;
    }

    public function getPrimaryKeyDDL(Table $table)
    {
        if ($table->hasPrimaryKey()) {
            $pattern = 'CONSTRAINT %s PRIMARY KEY (%s)';

            return sprintf($pattern,
                $this->quoteIdentifier($this->getPrimaryKeyName($table)),
                $this->getColumnListDDL($table->getPrimaryKey())
            );
        }
    }

    public function getAddForeignKeyDDL(ForeignKey $fk)
    {
        if ($fk->isSkipSql()) {
            return;
        }
        $pattern = "
BEGIN
ALTER TABLE %s ADD %s
END
;
";

        return sprintf($pattern,
            $this->quoteIdentifier($fk->getTable()->getName()),
            $this->getForeignKeyDDL($fk)
        );
    }

    public function getForeignKeyDDL(ForeignKey $fk)
    {
        if ($fk->isSkipSql()) {
            return;
        }
        $pattern = 'CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s)';
        $script = sprintf($pattern,
            $this->quoteIdentifier($fk->getName()),
            $this->getColumnListDDL($fk->getLocalColumns()),
            $this->quoteIdentifier($fk->getForeignTableName()),
            $this->getColumnListDDL($fk->getForeignColumns())
        );
        if ($fk->hasOnUpdate() && $fk->getOnUpdate() != ForeignKey::SETNULL) {
            $script .= ' ON UPDATE ' . $fk->getOnUpdate();
        }
        if ($fk->hasOnDelete() && $fk->getOnDelete() != ForeignKey::SETNULL) {
            $script .= ' ON DELETE ' . $fk->getOnDelete();
        }

        return $script;
    }

    /**
     * @see        Platform::supportsSchemas()
     */
    public function supportsSchemas()
    {
        return true;
    }

    public function hasSize($sqlType)
    {
        return !("INT" == $sqlType || "TEXT" == $sqlType);
    }

    public function quoteIdentifier($text)
    {
        return $this->isIdentifierQuotingEnabled ? '[' . strtr($text, array('.' => '].[')) . ']' : $text;
    }

    public function getTimestampFormatter()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * Builds the DDL SQL to modify a column
     *
     * @return string
     */
    public function getModifyColumnDDL(PropelColumnDiff $columnDiff)
    {
        $toColumn = $columnDiff->getToColumn();
        $pattern = "
ALTER TABLE %s ALTER COLUMN %s;
";

        return sprintf($pattern,
            $this->quoteIdentifier($toColumn->getTable()->getName()),
            $this->getColumnDDL($toColumn)
        );
    }

    /**
     * Builds the DDL SQL to modify a list of columns
     *
     * @return string
     */
    public function getModifyColumnsDDL($columnDiffs)
    {
        $lines = array();
        $tableName = null;
        foreach ($columnDiffs as $columnDiff) {
            $toColumn = $columnDiff->getToColumn();
            if (null === $tableName) {
                $tableName = $toColumn->getTable()->getName();
            }
            $lines[] = $this->getColumnDDL($toColumn);
        }

        $sep = ",
    ";

        $pattern = "
ALTER TABLE %s ALTER COLUMN
    %s
;
";

        return sprintf($pattern,
            $this->quoteIdentifier($tableName),
            implode($sep, $lines)
        );
    }

    /**
     * Builds the DDL SQL to rename a column
     *
     * @return string
     */
    public function getRenameColumnDDL($fromColumn, $toColumn)
    {
        $pattern = "
EXEC sp_RENAME '%s.%s', '%s', 'COLUMN';
";

        return sprintf($pattern,
            $this->quoteIdentifier($fromColumn->getTable()->getName()),
            $this->quoteIdentifier($fromColumn->getName()),
            $this->quoteIdentifier($toColumn->getName())
        );
    }

    /**
     * Builds the DDL SQL to remove a list of columns
     *
     * @return string
     */
    public function getAddColumnsDDL($columns)
    {
        $lines = array();
        $tableName = null;
        foreach ($columns as $column) {
            if (null === $tableName) {
                $tableName = $column->getTable()->getName();
            }
            $lines[] = $this->getColumnDDL($column);
        }

        $sep = ",
    ";

        $pattern = "
ALTER TABLE %s ADD
    %s
;
";

        return sprintf($pattern, $this->quoteIdentifier($tableName), implode($sep, $lines));
    }

    /**
     * Builds the DDL SQL for a Column object.
     *
     * @return string
     */
    public function getColumnDDL(Column $col)
    {
        $domain = $col->getDomain();
        $ddl = array($this->quoteIdentifier($col->getName()));
        $sqlType = $domain->getSqlType();

        if ($this->hasSize($sqlType) && $col->isDefaultSqlType($this)) {
            $ddl[] = $sqlType . $domain->printSize();
        } else {
            $ddl[] = $sqlType;
        }

        if ($notNull = $this->getNullString($col->isNotNull())) {
            $ddl[] = $notNull;
        }

        if ($autoIncrement = $col->getAutoIncrementString()) {
            $ddl[] = $autoIncrement;
        }

        return implode(' ', $ddl);
    }
}
