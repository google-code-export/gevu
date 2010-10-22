<?php

/**
 * Ce fichier contient la classe Form_Gevu_metiers_Ajouter.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * Ajouter une entrée Gevu_metiers.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Form_Gevu_metiers_Ajouter
{
           
    public function init()
    {
        
        $id_metier = new Zend_Form_Element_Text('id_metier');
        $id_metier->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        $lib = new Zend_Form_Element_Text('lib');
        $lib->setRequired(true)
            ->addValidators(array(new Zend_Validate_Alnum(true), new Zend_Validate_StringLength(255)));
        
        
        
        $this->addElements(array($id_metier, $lib, ));             
   
    }
            
}