<?php

/**
 * Ce fichier contient la classe Gevu_metiersxsolutionssController.
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
class Gevu_metiersxsolutionssController
{

    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function indexAction()
    {
        
        $this->view->gevu_metiersxsolutionss = Gevu_metiersxsolutions::get();
        
        echo $this->view->render('gevu_metiersxsolutionss/index.tpl');
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function ajouterAction()
    {
        
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiersxsolutions_Ajouter();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiersxsolutions::add($form->getValues());
                    $this->_redirect('/Gevu_metiersxsolutionss');
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
        
        $id_solution = $this->getRequest()->getParam( 'id_solution' );
        $gevu_metiersxsolutions = Gevu_metiersxsolutions::findByid_solution('id_solution');
        
        if ($gevu_metiersxsolutions == null) {
            $this->_redirect('/Gevu_metiersxsolutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiersxsolutions_Modifier();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiersxsolutions::edit($id_solution, $form->getValues());
                    $this->_redirect('/Gevu_metiersxsolutionss');
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
    
        $id_solution = $this->getRequest()->getParam( 'id_solution' );
        $gevu_metiersxsolutions = Gevu_metiersxsolutions::findByid_solution('id_solution');
        
        if ($gevu_metiersxsolutions == null) {
            $this->_redirect('/Gevu_metiersxsolutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiersxsolutions_Supprimer();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiersxsolutions::remove(id_solution);
                    $this->_redirect('/Gevu_metiersxsolutionss');
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