<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
// Pipeline affichage_final 
function execute_pipeline_affichage_final($val){
$val = minipipe('f_surligne', $val);
$val = minipipe('f_tidy', $val);
$val = minipipe('f_admin', $val);
return $val;
}

// Pipeline affiche_droite 
function execute_pipeline_affiche_droite($val){
@include_once(_DIR_PLUGINS.'grilles/forms_pipeline.php');
$val = minipipe('Forms_affiche_droite', $val);
return $val;
}

// Pipeline affiche_gauche 
function execute_pipeline_affiche_gauche($val){
return $val;
}

// Pipeline affiche_milieu 
function execute_pipeline_affiche_milieu($val){
@include_once(_DIR_PLUGINS.'grilles/forms_pipeline.php');
$val = minipipe('Forms_affiche_milieu', $val);
return $val;
}

// Pipeline ajouter_boutons 
function execute_pipeline_ajouter_boutons($val){
@include_once(_DIR_PLUGINS.'grilles/forms_pipeline.php');
$val = minipipe('Forms_ajouter_boutons', $val);
return $val;
}

// Pipeline ajouter_onglets 
function execute_pipeline_ajouter_onglets($val){
return $val;
}

// Pipeline body_prive 
function execute_pipeline_body_prive($val){
return $val;
}

// Pipeline exec_init 
function execute_pipeline_exec_init($val){
return $val;
}

// Pipeline header_prive 
function execute_pipeline_header_prive($val){
@include_once(_DIR_PLUGINS.'grilles/forms_pipeline.php');
$val = minipipe('Forms_header_prive', $val);
return $val;
}

// Pipeline insert_head 
function execute_pipeline_insert_head($val){
@include_once(_DIR_PLUGINS.'grilles/forms_filtres.php');
$val = minipipe('Forms_insert_head', $val);
return $val;
}

// Pipeline nettoyer_raccourcis_typo 
function execute_pipeline_nettoyer_raccourcis_typo($val){
return $val;
}

// Pipeline pre_indexation 
function execute_pipeline_pre_indexation($val){
return $val;
}

// Pipeline pre_propre 
function execute_pipeline_pre_propre($val){
@include_once(_DIR_PLUGINS.'grilles/forms_filtres.php');
$val = minipipe('extraire_multi', $val);
$val = minipipe('Forms_forms_avant_propre', $val);
return $val;
}

// Pipeline pre_syndication 
function execute_pipeline_pre_syndication($val){
return $val;
}

// Pipeline pre_typo 
function execute_pipeline_pre_typo($val){
$val = minipipe('extraire_multi', $val);
return $val;
}

// Pipeline post_propre 
function execute_pipeline_post_propre($val){
@include_once(_DIR_PLUGINS.'grilles/forms_filtres.php');
$val = minipipe('Forms_forms_apres_propre', $val);
return $val;
}

// Pipeline post_syndication 
function execute_pipeline_post_syndication($val){
return $val;
}

// Pipeline post_typo 
function execute_pipeline_post_typo($val){
$val = minipipe('quote_amp', $val);
return $val;
}

// Pipeline agenda_rendu_evenement 
function execute_pipeline_agenda_rendu_evenement($val){
return $val;
}

// Pipeline taches_generales_cron 
function execute_pipeline_taches_generales_cron($val){
return $val;
}

// Pipeline affiche_groupe_mot 
function execute_pipeline_affiche_groupe_mot($val){
@include_once(_DIR_PLUGINS.'groupe2groupes/g2g_admin.php');
$val = minipipe('g2g_affiche_groupe_mot', $val);
return $val;
}

// Pipeline arbo_groupe 
function execute_pipeline_arbo_groupe($val){
@include_once(_DIR_PLUGINS.'groupe2groupes/g2g_admin.php');
$val = minipipe('g2g_arbo_groupe', $val);
return $val;
}

// Pipeline dans_groupe 
function execute_pipeline_dans_groupe($val){
@include_once(_DIR_PLUGINS.'groupe2groupes/g2g_admin.php');
$val = minipipe('g2g_dans_groupe', $val);
return $val;
}


?>