SELECT
      ld.id_lieu
      , d.id_diag
		  , crit.ref critRef, crit.id_critere, crit.affirmation, crit.handicateur_moteur, crit.handicateur_auditif, crit.handicateur_visuel, crit.handicateur_cognitif
      , GROUP_CONCAT(DISTINCT tc.id_type_critere) typeCrit
      , GROUP_CONCAT(DISTINCT dc.id_type_droit) droitCrit
    	, s.id_solution, s.lib AS solution, s.ref AS refSolu
		  , p.id_produit, p.ref AS refProd, p.description produit, p.marque, p.modele
		  , ds.unite dsunite, ds.pose dspose, ds.metre_lineaire dsmetre_lineaire, ds.metre_carre dsmetre_carre, ds.achat dsachat, ds.cout dscout
		FROM gevu_trouville.gevu_lieux as ld 
			INNER JOIN gevu_trouville.gevu_lieux as l ON l.id_lieu = 6671 AND ld.lft BETWEEN l.lft AND l.rgt 				
			INNER JOIN gevu_trouville.gevu_diagnostics as d ON d.id_lieu = ld.id_lieu AND d.id_reponse IN (124,2) AND d.last = 1
			INNER JOIN gevu_new.gevu_criteres as crit ON crit.id_critere = d.id_critere AND crit.affirmation != ''
			INNER JOIN gevu_new.gevu_criteresxtypesxcriteres as tc ON tc.id_critere = crit.id_critere
			INNER JOIN gevu_new.gevu_criteresxtypesxdroits as dc ON dc.id_critere = crit.id_critere
			LEFT JOIN gevu_trouville.gevu_diagnosticsxsolutions ds ON ds.id_diag = d.id_diag
		  LEFT JOIN gevu_new.gevu_solutions AS s ON s.id_solution = ds.id_solution
			LEFT JOIN gevu_new.gevu_produits as p ON p.id_produit = ds.id_produit
		GROUP BY d.id_diag
    ORDER BY ld.id_lieu, crit.ref