<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/ObjectBuilder.php';

/**
 * Generates the empty PHP5 stub object class for use with inheritance in the user object model (OM).
 *
 * This class produces the empty stub class that can be customized with application
 * business logic, custom behavior, etc.
 *
 * @author     Hans Lellelid <hans@xmpl.org>
 * @package    propel.generator.builder.om
 */
class PHP5MultiExtendObjectBuilder extends ObjectBuilder
{

    /**
     * The current child "object" we are operating on.
     */
    private $child;

    /**
     * Returns the name of the current class being built.
     *
     * @return string
     */
    public function getUnprefixedClassname()
    {
        return $this->getChild()->getClassname();
    }

    /**
     * Override method to return child package, if specified.
     *
     * @return string
     */
    public function getPackage()
    {
        return ($this->child->getPackage() ? $this->child->getPackage() : parent::getPackage());
    }

    /**
     * Set the child object that we're operating on currently.
     *
     * @param   $child Inheritance
     */
    public function setChild(Inheritance $child)
    {
        $this->child = $child;
    }

    /**
     * Returns the child object we're operating on currently.
     *
     * @return Inheritance
     * @throws BuildException - if child was not set.
     */
    public function getChild()
    {
        if (!$this->child) {
            throw new BuildException("The PHP5MultiExtendObjectBuilder needs to be told which child class to build (via setChild() method) before it can build the stub class.");
        }

        return $this->child;
    }

    /**
     * Returns classpath to parent class.
     *
     * @return string
     */
    protected function getParentClasspath()
    {
        if ($this->getChild()->getAncestor()) {
            return $this->getChild()->getAncestor();
        } else {
            return $this->getObjectBuilder()->getClasspath();
        }
    }

    /**
     * Returns classname of parent class.
     *
     * @return string
     */
    protected function getParentClassname()
    {
        return ClassTools::classname($this->getParentClasspath());
    }

    /**
     * Gets the file path to the parent class.
     *
     * @return string
     */
    protected function getParentClassFilePath()
    {
        return ClassTools::getFilePath($this->getParentClasspath());
    }

    /**
     * Adds the include() statements for files that this class depends on or utilizes.
     *
     * @param string &$script The script will be modified in this method.
     */
    protected function addIncludes(&$script)
    {
    } // addIncludes()

    /**
     * Adds class phpdoc comment and opening of class.
     *
     * @param string &$script The script will be modified in this method.
     */
    protected function addClassOpen(&$script)
    {
        if ($this->getChild()->getAncestor()) {
            $ancestorClassName = $this->getChild()->getAncestor();
            if ($this->getDatabase()->hasTableByPhpName($ancestorClassName)) {
                $this->declareClassFromBuilder($this->getNewStubObjectBuilder($this->getDatabase()->getTableByPhpName($ancestorClassName)));
            } else {
                $this->declareClassNamespace($ancestorClassName, $this->getNamespace());
            }
        } else {
            $this->declareClassFromBuilder($this->getObjectBuilder());
        }
        $table = $this->getTable();
        $tableName = $table->getName();
        $tableDesc = $table->getDescription();

        $baseClassname = $this->getObjectBuilder()->getClassname();

        $script .= "

/**
 * Skeleton subclass for representing a row from one of the subclasses of the '$tableName' table.
 *
 * $tableDesc
 *";
        if ($this->getBuildProperty('addTimeStamp')) {
            $now = strftime('%c');
            $script .= "
 * This class was autogenerated by Propel " . $this->getBuildProperty('version') . " on:
 *
 * $now
 *";
        }
        $script .= "
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator." . $this->getPackage() . "
 */
class " . $this->getClassname() . " extends " . $this->getParentClassname() . " {
";
    }

    /**
     * Specifies the methods that are added as part of the stub object class.
     *
     * By default there are no methods for the empty stub classes; override this method
     * if you want to change that behavior.
     *
     * @see        ObjectBuilder::addClassBody()
     */
    protected function addClassBody(&$script)
    {
        $this->declareClassFromBuilder($this->getStubPeerBuilder());
        $child = $this->getChild();
        $col = $child->getColumn();
        $cfc = $col->getPhpName();

        $const = "CLASSKEY_" . $child->getSanitizedKey();

        $script .= "
    /**
     * Constructs a new " . $this->getChild()->getClassname() . " class, setting the " . $col->getName() . " column to " . $this->getPeerClassname() . "::$const.
     */
    public function __construct()
    {";
        $script .= "
        parent::__construct();
        \$this->set$cfc(" . $this->getPeerClassname() . "::CLASSKEY_" . $child->getSanitizedKey() . ");
    }
";
    }

    /**
     * Closes class.
     *
     * @param string &$script The script will be modified in this method.
     */
    protected function addClassClose(&$script)
    {
        $script .= "
} // " . $this->getClassname() . "
";
    }
} // PHP5ExtensionObjectBuilder
