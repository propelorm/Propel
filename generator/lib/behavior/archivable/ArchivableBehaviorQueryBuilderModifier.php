<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Keeps tracks of an ActiveRecord object, even after deletion
 *
 * @author     François Zaninotto
 * @package    propel.generator.behavior.archivable
 */
class ArchivableBehaviorQueryBuilderModifier
{
    protected $behavior, $table;

    public function __construct(ArchivableBehavior $behavior)
    {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
    }

    protected function getParameter($key)
    {
        return $this->behavior->getParameter($key);
    }

    /**
     * Add attributes to the
     *
     * @param QueryBuilder $builder
     *
     * @return string
     */
    public function queryAttributes(QueryBuilder $builder)
    {
        $script = '';
        if ($this->behavior->isArchiveOnUpdate()) {
            $script .= "protected \$archiveOnUpdate = true;
";
        }
        if ($this->behavior->isArchiveOnDelete()) {
            $script .= "protected \$archiveOnDelete = true;
";
        }

        return $script;
    }

    public function preDeleteQuery(QueryBuilder $builder)
    {
        if ($this->behavior->isArchiveOnDelete()) {
            return "
if (\$this->archiveOnDelete) {
    \$this->archive(\$con);
} else {
    \$this->archiveOnDelete = true;
}
";
        }

        return '';
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return string
     */
    public function postUpdateQuery(QueryBuilder $builder)
    {
        if ($this->behavior->isArchiveOnUpdate()) {
            return "
if (\$this->archiveOnUpdate) {
    \$this->archive(\$con);
} else {
    \$this->archiveOnUpdate = true;
}
";
        }

        return '';
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return string the PHP code to be added to the builder
     */
    public function queryMethods(QueryBuilder $builder)
    {
        $script = '';
        $script .= $this->addArchive($builder);
        if ($this->behavior->isArchiveOnUpdate()) {
            $script .= $this->addSetArchiveOnUpdate($builder);
            $script .= $this->addUpdateWithoutArchive($builder);
        }
        if ($this->behavior->isArchiveOnDelete()) {
            $script .= $this->addSetArchiveOnDelete($builder);
            $script .= $this->addDeleteWithoutArchive($builder);
        }

        return $script;
    }

    /**
     * @return string the PHP code to be added to the builder
     */
    protected function addArchive(QueryBuilder $builder)
    {
        return $this->behavior->renderTemplate('queryArchive', array(
            'archiveTablePhpName' => $this->behavior->getArchiveTablePhpName($builder),
            'modelPeerName'       => $builder->getPeerClassname(),
        ));
    }

    /**
     * @return string the PHP code to be added to the builder
     */
    public function addSetArchiveOnUpdate(QueryBuilder $builder)
    {
        return $this->behavior->renderTemplate('querySetArchiveOnUpdate');
    }

    /**
     * @return string the PHP code to be added to the builder
     */
    public function addUpdateWithoutArchive(QueryBuilder $builder)
    {
        return $this->behavior->renderTemplate('queryUpdateWithoutArchive');
    }

    /**
     * @return string the PHP code to be added to the builder
     */
    public function addSetArchiveOnDelete(QueryBuilder $builder)
    {
        return $this->behavior->renderTemplate('querySetArchiveOnDelete');
    }

    /**
     * @return string the PHP code to be added to the builder
     */
    public function addDeleteWithoutArchive(QueryBuilder $builder)
    {
        return $this->behavior->renderTemplate('queryDeleteWithoutArchive');
    }
}
