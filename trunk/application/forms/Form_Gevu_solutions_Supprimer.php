<?php

/**
 * Ce fichier contient la classe Form_Gevu_solutions_Supprimer.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * Supprimer une entrée Gevu_solutions.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Form_Gevu_solutions_Supprimer
{
           
    public function init()
    {
        
        $id_solution = new Zend_Form_Element_Text('id_solution');
        $id_solution->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        $lib = new Zend_Form_Element_Text('lib');
        $lib->setRequired(true)
            ->addValidators(array(new Zend_Validate_Alnum(true), new Zend_Validate_StringLength(300)));
        
        $id_type_solution = new Zend_Form_Element_Text('id_type_solution');
        $id_type_solution->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        $maj = new Zend_Form_Element_('maj');
        $maj->setRequired(true)
            ->addValidators(array());
        
        
        
        $this->addElements(array($id_solution, $lib, $id_type_solution, $maj, ));             
   
    }
            
}