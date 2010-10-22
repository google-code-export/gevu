<?php

/**
 * Ce fichier contient la classe Form_Gevu_types_solutions_Supprimer.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * Supprimer une entrée Gevu_types_solutions.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Form_Gevu_types_solutions_Supprimer
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