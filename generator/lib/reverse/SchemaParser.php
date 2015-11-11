<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Interface for reverse engineering schema parsers.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @version    $Revision$
 * @package    propel.generator.reverse
 */
interface SchemaParser
{

    /**
     * Gets the database connection.
     *
     * @return PDO
     */
    public function getConnection();

    /**
     * Sets the database connection.
     *
     * @param PDO $dbh
     */
    public function setConnection(PDO $dbh);

    /**
     * Sets the GeneratorConfig to use in the parsing.
     *
     * @param GeneratorConfigInterface $config
     */
    public function setGeneratorConfig(GeneratorConfigInterface $config);

    /**
     * Gets a specific propel (renamed) property from the build.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getBuildProperty($name);

    /**
     * Gets array of warning messages.
     *
     * @return array string[]
     */
    public function getWarnings();

    /**
     * Parse the schema and populate passed-in Database model object.
     *
     * @param Database $database
     * @param Task     $task
     *
     * @return int number of generated tables
     */
    public function parse(Database $database, Task $task = null);
}
