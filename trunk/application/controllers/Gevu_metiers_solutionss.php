<?php

/**
 * Ce fichier contient la classe Gevu_metiers_solutionssController.
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
class Gevu_metiers_solutionssController
{

    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function indexAction()
    {
        
        $this->view->gevu_metiers_solutionss = Gevu_metiers_solutions::get();
        
        echo $this->view->render('gevu_metiers_solutionss/index.tpl');
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function ajouterAction()
    {
        
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiers_solutions_Ajouter();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiers_solutions::add($form->getValues());
                    $this->_redirect('/Gevu_metiers_solutionss');
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
        $gevu_metiers_solutions = Gevu_metiers_solutions::findByid_solution('id_solution');
        
        if ($gevu_metiers_solutions == null) {
            $this->_redirect('/Gevu_metiers_solutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiers_solutions_Modifier();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiers_solutions::edit($id_solution, $form->getValues());
                    $this->_redirect('/Gevu_metiers_solutionss');
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
        $gevu_metiers_solutions = Gevu_metiers_solutions::findByid_solution('id_solution');
        
        if ($gevu_metiers_solutions == null) {
            $this->_redirect('/Gevu_metiers_solutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_metiers_solutions_Supprimer();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_metiers_solutions::remove(id_solution);
                    $this->_redirect('/Gevu_metiers_solutionss');
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