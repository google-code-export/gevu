<?php

/**
 * Ce fichier contient la classe %class%sController.
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
class %class%sController
{

    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function indexAction()
    {
        
        $this->view->%table%s = %class%::get();
        
        echo $this->view->render('%table%s/index.tpl');
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function ajouterAction()
    {
        
        // On crée une instance du formulaire
        $form = new Form_%class%_Ajouter();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    %class%::add($form->getValues());
                    $this->_redirect('/%class%s');
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
        
        $%primary% = $this->getRequest()->getParam( '%primary%' );
        $%table% = %class%::findBy%primary%('%primary%');
        
        if ($%table% == null) {
            $this->_redirect('/%class%s');
        }
    
        // On crée une instance du formulaire
        $form = new Form_%class%_Modifier();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    %class%::edit($%primary%, $form->getValues());
                    $this->_redirect('/%class%s');
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
    
        $%primary% = $this->getRequest()->getParam( '%primary%' );
        $%table% = %class%::findBy%primary%('%primary%');
        
        if ($%table% == null) {
            $this->_redirect('/%class%s');
        }
    
        // On crée une instance du formulaire
        $form = new Form_%class%_Supprimer();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    %class%::remove(%primary%);
                    $this->_redirect('/%class%s');
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