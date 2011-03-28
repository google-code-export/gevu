SELECT fdc.valeur, rpp.id_rubrique, count( DISTINCT r.id_rubrique)
FROM `spip_forms_donnees_champs` fdc 
INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 85
INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
INNER JOIN spip_articles a ON a.id_article = fda.id_article 
INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent AND rpp.id_rubrique = 8240 
WHERE `champ` = 'ligne_30'
GROUP BY fdc.valeur, rpp.id_rubrique 
