<?php

/**
 * Ce fichier contient la classe Gevu_typesxsolutionssController.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * // TODO :: Description du contrôleur //
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Gevu_typesxsolutionssController
{

    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function indexAction()
    {
        
        $this->view->gevu_typesxsolutionss = Gevu_typesxsolutions::get();
        
        echo $this->view->render('gevu_typesxsolutionss/index.tpl');
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function ajouterAction()
    {
        
        // On crée une instance du formulaire
        $form = new Form_Gevu_typesxsolutions_Ajouter();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_typesxsolutions::add($form->getValues());
                    $this->_redirect('/Gevu_typesxsolutionss');
                }catch (Zend_Db_Exception $e) {
                    $this->view->messages = array('DbError' => $e->getMessage());
                }               
                
            } else {
                $this->view->values = $form->getValues();
                $this->view->messages = $form->getMessages();
            }

        }
        
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function modifierAction()
    {
        
        $id_type_solution = $this->getRequest()->getParam( 'id_type_solution' );
        $gevu_typesxsolutions = Gevu_typesxsolutions::findByid_type_solution('id_type_solution');
        
        if ($gevu_typesxsolutions == null) {
            $this->_redirect('/Gevu_typesxsolutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_typesxsolutions_Modifier();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_typesxsolutions::edit($id_type_solution, $form->getValues());
                    $this->_redirect('/Gevu_typesxsolutionss');
                }catch (Zend_Db_Exception $e) {
                    $this->view->messages = array('DbError' => $e->getMessage());
                }               
                
            } else {
                $this->view->values = $form->getValues();
                $this->view->messages = $form->getMessages();
            }

        }
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function supprimerAction()
    {
    
        $id_type_solution = $this->getRequest()->getParam( 'id_type_solution' );
        $gevu_typesxsolutions = Gevu_typesxsolutions::findByid_type_solution('id_type_solution');
        
        if ($gevu_typesxsolutions == null) {
            $this->_redirect('/Gevu_typesxsolutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_typesxsolutions_Supprimer();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_typesxsolutions::remove(id_type_solution);
                    $this->_redirect('/Gevu_typesxsolutionss');
                }catch (Zend_Db_Exception $e) {
                    $this->view->messages = array('DbError' => $e->getMessage());
                }               
                
            } else {
                $this->view->values = $form->getValues();
                $this->view->messages = $form->getMessages();
            }

        }
        
    }

}