<?php

/**
 * Ce fichier contient la classe Gevu_types_solutionssController.
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
class Gevu_types_solutionssController
{

    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function indexAction()
    {
        
        $this->view->gevu_types_solutionss = Gevu_types_solutions::get();
        
        echo $this->view->render('gevu_types_solutionss/index.tpl');
    }
    
    /**
     * // TODO :: Description de l'action //
     *
     * @return void
     */
    public function ajouterAction()
    {
        
        // On crée une instance du formulaire
        $form = new Form_Gevu_types_solutions_Ajouter();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_types_solutions::add($form->getValues());
                    $this->_redirect('/Gevu_types_solutionss');
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
        $gevu_types_solutions = Gevu_types_solutions::findByid_type_solution('id_type_solution');
        
        if ($gevu_types_solutions == null) {
            $this->_redirect('/Gevu_types_solutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_types_solutions_Modifier();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_types_solutions::edit($id_type_solution, $form->getValues());
                    $this->_redirect('/Gevu_types_solutionss');
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
        $gevu_types_solutions = Gevu_types_solutions::findByid_type_solution('id_type_solution');
        
        if ($gevu_types_solutions == null) {
            $this->_redirect('/Gevu_types_solutionss');
        }
    
        // On crée une instance du formulaire
        $form = new Form_Gevu_types_solutions_Supprimer();
        
        if ($this->getRequest()->isPost()) {
             
            if ($form->isValid( $this->getRequest()->getPost() )) {
                
                try {
                    Gevu_types_solutions::remove(id_type_solution);
                    $this->_redirect('/Gevu_types_solutionss');
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