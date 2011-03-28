SELECT fdc0.valeur, fdc.valeur, r0.id_rubrique, r0.titre, r.id_rubrique, r.titre
FROM `spip_forms_donnees_champs` fdc0 
/*récupère les antennes*/
  INNER JOIN spip_forms_donnees fd0 ON fd0.id_donnee = fdc0.id_donnee AND fd0.id_form = 87
  INNER JOIN spip_forms_donnees_articles fda0 ON fda0.id_donnee = fdc0.id_donnee 
  INNER JOIN spip_articles a0 ON a0.id_article = fda0.id_article 
  INNER JOIN spip_rubriques r0 ON r0.id_rubrique = a0.id_rubrique 

/*récupère les établissement*/
  INNER JOIN spip_forms_donnees_champs fdc ON fdc.champ = "ligne_2" AND fdc.valeur = fdc0.valeur  
  INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee AND fd.id_form = 82
  INNER JOIN spip_forms_donnees_articles fda ON fda.id_donnee = fdc.id_donnee 
  INNER JOIN spip_articles a ON a.id_article = fda.id_article 
  INNER JOIN spip_rubriques r ON r.id_rubrique = a.id_rubrique 
WHERE fdc0.champ = 'ligne_1'
GROUP BY fdc0.valeur, r0.id_rubrique, r0.titre, r.id_rubrique, r.titre
ORDER BY fdc0.valeur, r0.titre, r.titre