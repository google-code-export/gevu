SELECT 
s.id_solution, s.lib AS solution
 , cS.id_cout, cS.unite, cS.metre_lineaire, cS.metre_carre, cS.achat, cS.pose
 , p.id_produit, p.ref AS refProd, p.description, p.marque, p.modele
 , cP.id_cout, cP.unite, cP.metre_lineaire, cP.metre_carre, cP.achat, cP.pose
FROM gevu_solutions AS s
 INNER JOIN gevu_solutionsxcriteres as sc ON s.id_solution = sc.id_solution AND sc.id_critere = 140
 INNER JOIN gevu_solutionsxcouts as sco ON sco.id_solution = s.id_solution
 INNER JOIN gevu_couts as cS ON cS.id_cout = sco.id_cout
 LEFT JOIN gevu_solutionsxproduits as sp ON sp.id_solution = sc.id_solution
 LEFT JOIN gevu_produits as p ON p.id_produit = sp.id_produit
 LEFT JOIN gevu_produitsxcouts as pc ON pc.id_produit = p.id_produit
 LEFT JOIN gevu_couts as cP ON cP.id_cout = pc.id_cout
 
