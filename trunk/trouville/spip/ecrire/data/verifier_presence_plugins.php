<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
	// http://doc.spip.org/@verifier_presence_plugins
	function verifier_presence_plugins(){
		$ok = true;
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_options.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_fonctions.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_pipeline.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_pipeline.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_pipeline.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_pipeline.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_filtres.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_filtres.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'grilles/forms_filtres.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'groupe2groupes/g2g_admin.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'groupe2groupes/g2g_admin.php');
		$ok = $ok & @is_readable(_DIR_PLUGINS.'groupe2groupes/g2g_admin.php');
		return $ok;
	}
?>