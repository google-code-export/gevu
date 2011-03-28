SELECT DISTINCT r.id_rubrique
						, oed.idDonRep
						, oed.idDonCont
            , gc.ref valRef
						, gc.affirmation affirm
FROM spip_rubriques_enfants r 
					INNER JOIN ona_etatdiag oe ON oe.id_rubrique = r.id_rubrique AND oe.handi = 0 AND visu
					INNER JOIN ona_etatdiag_donnees oed ON oed.id_etatdiag = oe.id_etatdiag

						INNER JOIN spip_forms_donnees_champs fdcRef ON fdcRef.id_donnee = oed.idDonRep  
						INNER JOIN spip_mots m ON m.id_mot = fdcRef.valeur AND m.titre='Oui'

						INNER JOIN spip_forms_donnees_champs fdcCont ON fdcCont.id_donnee = oed.idDonCont AND fdcCont.champ = 'ligne_1'  
            
            INNER JOIN gevu.gevu_criteres gc ON gc.ref = fdcCont.valeur AND handicateur_moteur = 3 
                
				 WHERE r.id_parent = 7305
					ORDER BY r.id_rubrique
				