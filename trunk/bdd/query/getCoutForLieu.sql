SELECT 
diag.id_diag, diag.id_critere, diag.id_reponse
 , mc.titre AS reponse
, le.id_lieu AS diagIdLieu, le.lib AS diagLieu
, l.id_lieu, l.lib
 , typc.id_type_critere
 , crit.ref, crit.affirmation
 , tc.lib AS controle
 , s.id_solution, s.lib AS solution
 , cS.id_cout, cS.unite, cS.metre_lineaire, cS.metre_carre, cS.achat, cS.pose
 , p.id_produit, p.ref AS refProd, p.description, p.marque, p.modele
 , cP.id_cout, cP.unite, cP.metre_lineaire, cP.metre_carre, cP.achat, cP.pose
FROM gevu_diagnostics AS diag
 INNER JOIN gevu_lieux AS le ON le.id_lieu = diag.id_lieu
 INNER JOIN gevu_lieux AS l ON le.lft BETWEEN l.lft AND l.rgt
 INNER JOIN gevu_new.gevu_criteres AS crit ON diag.id_critere = crit.id_critere
 INNER JOIN gevu_new.gevu_typesxcontroles AS tc ON tc.id_type_controle = crit.id_type_controle
 INNER JOIN gevu_new.gevu_criteresxtypesxcriteres AS typc ON crit.id_critere = typc.id_critere  
 INNER JOIN gevu_new.gevu_motsclefs AS mc ON mc.id_motclef = diag.id_reponse AND diag.id_reponse IN (124,2)
 LEFT JOIN gevu_new.gevu_solutionsxcriteres as sc ON sc.id_critere = crit.id_critere
 LEFT JOIN gevu_new.gevu_solutions as s ON s.id_solution = sc.id_solution
 LEFT JOIN gevu_new.gevu_solutionsxcouts as sco ON sco.id_solution = s.id_solution
 LEFT JOIN gevu_new.gevu_couts as cS ON cS.id_cout = sco.id_cout
 LEFT JOIN gevu_new.gevu_solutionsxproduits as sp ON sp.id_solution = sc.id_solution
 LEFT JOIN gevu_new.gevu_produits as p ON p.id_produit = sp.id_produit
 LEFT JOIN gevu_new.gevu_produitsxcouts as pc ON pc.id_produit = p.id_produit
 LEFT JOIN gevu_new.gevu_couts as cP ON cP.id_cout = pc.id_cout
WHERE (l.id_lieu = 10694) AND (diag.last = 1)  AND crit.affirmation != ''
ORDER BY diag.id_lieu ASC
