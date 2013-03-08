SELECT 
p.id_produit, p.ref, p.description
, i.id_interv
, i.interv, mcI.titre lblInterv
, i.unite, mcU.titre lblUnite
, i.frequence
, i.cout
FROM gevu_produits p
INNER JOIN gevu_interventions i ON i.id_produit = p.id_produit
INNER JOIN gevu_motsclefs mcI ON mcI.id_motclef = i.interv
INNER JOIN gevu_motsclefs mcU ON mcU.id_motclef = i.unite
WHERE p.id_produit = 1
ORDER BY p.id_produit
