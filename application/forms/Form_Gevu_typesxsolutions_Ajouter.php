<?php

/**
 * Ce fichier contient la classe Form_Gevu_typesxsolutions_Ajouter.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * Ajouter une entrée Gevu_typesxsolutions.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Form_Gevu_typesxsolutions_Ajouter
{
           
    public function init()
    {
        
        $id_type_solution = new Zend_Form_Element_Text('id_type_solution');
        $id_type_solution->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        $lib = new Zend_Form_Element_Text('lib');
        $lib->setRequired(true)
            ->addValidators(array(new Zend_Validate_Alnum(true), new Zend_Validate_StringLength(255)));
        
        
        
        $this->addElements(array($id_type_solution, $lib, ));             
   
    }
            
}