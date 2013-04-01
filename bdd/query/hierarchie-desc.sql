SELECT enfant.*
FROM gevu_lieux AS node, gevu_lieux AS enfant
WHERE enfant.lft BETWEEN node.lft AND node.rgt
AND node.id_lieu = 6671
ORDER BY enfant.lft;