SELECT fdc.valeur, count( DISTINCT r.id_rubrique), a.id_article
FROM `spip_forms_donnees_champs` fdc 
INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 85
INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
INNER JOIN spip_articles a ON a.id_article = fda.id_article 
INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
INNER JOIN spip_rubriques rpp ON rpp.id_rubrique = rp.id_parent AND rpp.id_rubrique
INNER JOIN spip_articles rppa ON rppa.id_rubrique = rpp.id_rubrique 
INNER JOIN spip_forms_donnees_articles fdarpp ON fdarpp.id_article = rppa.id_article  
INNER JOIN spip_forms_donnees fdrpp ON fdrpp.id_donnee = fdarpp.id_donnee AND fdrpp.id_form = 82
INNER JOIN spip_forms_donnees_champs fdcrpp ON fdcrpp.id_donnee = fdrpp.id_donnee AND fdcrpp.champ = 'ligne_2' AND fdcrpp.valeur = 'CV'    
WHERE fdc.champ = 'ligne_30'
GROUP BY fdc.valeur, rppa.id_article
