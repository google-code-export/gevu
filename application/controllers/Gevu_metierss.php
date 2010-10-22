<?php

/**
 * Ce fichier contient la classe Gevu_metierssController.
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
class Gevu_metierssController
{

    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function indexAction()
    {
        
        $this->view->gevu_metierss = Gevu_metiers::get();
        
        echo $this->view->render('gevu_metierss/index.tpl');
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function ajouterAction()
    {
        
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiers_Ajouter();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiers::add($form->getValues());
                    $this->_redirect('/Gevu_metierss');
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
        
        $id_metier = $this->getRequest()->getParam( 'id_metier' );
        $gevu_metiers = Gevu_metiers::findByid_metier('id_metier');
        
        if ($gevu_metiers == null) {
            $this->_redirect('/Gevu_metierss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiers_Modifier();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiers::edit($id_metier, $form->getValues());
                    $this->_redirect('/Gevu_metierss');
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
    
        $id_metier = $this->getRequest()->getParam( 'id_metier' );
        $gevu_metiers = Gevu_metiers::findByid_metier('id_metier');
        
        if ($gevu_metiers == null) {
            $this->_redirect('/Gevu_metierss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiers_Supprimer();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiers::remove(id_metier);
                    $this->_redirect('/Gevu_metierss');
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