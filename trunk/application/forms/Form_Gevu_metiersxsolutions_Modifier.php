<?php

/**
 * Ce fichier contient la classe Form_Gevu_metiersxsolutions_Modifier.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * Modifier une entrée Gevu_metiersxsolutions.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Form_Gevu_metiersxsolutions_Modifier
{
           
    public function init()
    {
        
        $id_solution = new Zend_Form_Element_Text('id_solution');
        $id_solution->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        $id_metier = new Zend_Form_Element_Text('id_metier');
        $id_metier->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        
        
        $this->addElements(array($id_solution, $id_metier, ));             
   
    }
            
}