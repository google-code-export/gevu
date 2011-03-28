SELECT parent.*
FROM gevu_lieux AS node, gevu_lieux AS parent
WHERE node.lft BETWEEN parent.lft AND parent.rgt
AND parent.id_instant = node.id_instant
AND node.id_lieu = 651
ORDER BY parent.lft;