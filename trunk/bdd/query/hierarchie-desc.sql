SELECT enfant.*
FROM gevu_lieux AS node, gevu_lieux AS enfant
WHERE enfant.lft BETWEEN node.lft AND node.rgt
AND node.id_lieu = 22446
ORDER BY enfant.lft;