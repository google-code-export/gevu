SELECT
      ld.id_lieu, ld.lib, ld.id_instant, ld.lft, ld.rgt, ld.niv, ld.maj, ld.lieu_parent, ld.id_type_controle, ld.id_rubrique, ld.id_parent
		  , ds.id_diagsolus, ds.id_diag, ds.unite dsunite, ds.pose dspose, ds.metre_lineaire dsmetre_lineaire, ds.metre_carre dsmetre_carre, ds.achat dsachat, ds.cout dscout
		  , s.id_solution, s.lib AS solution, s.ref AS refSolu
		  , c.id_cout, c.unite, c.metre_lineaire, c.metre_carre, c.achat, c.pose
		  , p.id_produit, p.ref AS refProd, p.description produit, p.marque, p.modele
		FROM gevu_trouville.gevu_lieux as ld 
			INNER JOIN gevu_trouville.gevu_lieux as l ON l.id_lieu = 9730	AND ld.lft BETWEEN l.lft AND l.rgt 				
			INNER JOIN gevu_trouville.gevu_diagnostics as d ON d.id_lieu = ld.id_lieu
			LEFT JOIN gevu_trouville.gevu_diagnosticsxsolutions ds ON ds.id_diag = d.id_diag
			LEFT JOIN gevu_new.gevu_couts as c ON c.id_cout = ds.id_cout
		  LEFT JOIN gevu_new.gevu_solutions AS s ON s.id_solution = ds.id_solution
			LEFT JOIN gevu_new.gevu_produits as p ON p.id_produit = ds.id_produit

