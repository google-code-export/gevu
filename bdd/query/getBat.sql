SELECT fdc.valeur, count( DISTINCT rpp.titre) , count( DISTINCT r.titre) 
FROM `spip_forms_donnees_champs` fdc 
INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 82
INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
INNER JOIN spip_articles a ON a.id_article = fda.id_article 
INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent
WHERE `champ` = 'ligne_2' 
group by fdc.valeur