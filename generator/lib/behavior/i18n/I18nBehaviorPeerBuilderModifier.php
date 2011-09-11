<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
 
/**
 * Allows translation of text columns through transparent one-to-many relationship.
 * Modifier for the peer builder.
 *
 * @author     François Zaninotto
 * @version    $Revision$
 * @package    propel.generator.behavior.i18n
 */
class I18nBehaviorPeerBuilderModifier
{
	protected $behavior, $table, $builder; 
	
	public function __construct($behavior)
	{
		$this->behavior = $behavior;
    $this->table = $behavior->getTable();
	}
  
  public function staticMethods($builder)  
 	{  
    $this->builder = $builder;  
    $script = '';  
    $script .= $this->addGetI18nModel();  
 	
    return $script;          
 	} 

	public function staticConstants()
	{
		return "
/**
 * The default locale to use for translations
 * @var        string
 */
const DEFAULT_LOCALE = '{$this->behavior->getDefaultLocale()}';";
	}
  
  public function addGetI18nModel()  
 	{  
    $i18nTable = $this->behavior->getI18nTable();  
 	  return $this->behavior->renderTemplate('staticGetI18nModel', array(  
      'i18nTablePhpName' => $this->builder->getNewStubObjectBuilder($i18nTable)->getClassname()  
 	  ));  
 	}  
}