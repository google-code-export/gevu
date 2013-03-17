SELECT 
diag.id_diag, diag.id_critere, diag.id_reponse
 , mc.titre AS reponse
, le.id_lieu AS dLieu, le.lib
, l.id_lieu, l.lib
 , sp.id_solution, sp.id_produit
 , pc.id_cout
FROM gevu_diagnostics AS diag
 INNER JOIN gevu_lieux AS le ON le.id_lieu = diag.id_lieu
 INNER JOIN gevu_lieux AS l ON le.lft BETWEEN l.lft AND l.rgt
 INNER JOIN gevu_new.gevu_motsclefs AS mc ON mc.id_motclef = diag.id_reponse AND diag.id_reponse IN (124,2)
 LEFT JOIN gevu_new.gevu_solutionsxcriteres as sc ON sc.id_critere = diag.id_critere
 LEFT JOIN gevu_new.gevu_solutionsxproduits as sp ON sp.id_solution = sc.id_solution
 LEFT JOIN gevu_new.gevu_produitsxcouts as pc ON pc.id_produit = sp.id_produit
WHERE (l.id_lieu = 6671) AND (diag.last = 1) 
ORDER BY diag.id_lieu ASC
