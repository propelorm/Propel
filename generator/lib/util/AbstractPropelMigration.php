<?php

/**
 * Class AbstractPropelMigration
 */
abstract class AbstractPropelMigration implements IPropelMigration
{
    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function preUp(PropelMigrationManager $manager)
    {
        return true;
    }

    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function postUp(PropelMigrationManager $manager)
    {
        return true;
    }

    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function preDown(PropelMigrationManager $manager)
    {
        return true;
    }

    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function postDown(PropelMigrationManager $manager)
    {
        return true;
    }
}