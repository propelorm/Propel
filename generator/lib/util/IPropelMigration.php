<?php

/**
 * Interface IPropelMigration
 */
interface IPropelMigration
{
    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function preUp(PropelMigrationManager $manager);

    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function postUp(PropelMigrationManager $manager);

    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function preDown(PropelMigrationManager $manager);

    /**
     * @param PropelMigrationManager $manager
     * @return bool
     */
    public function postDown(PropelMigrationManager $manager);

    /**
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL();

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL();
}