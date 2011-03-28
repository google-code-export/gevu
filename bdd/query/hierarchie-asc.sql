SELECT CONCAT( REPEAT(' - ', COUNT(parent.id_lieu)), node.lib, node.id_lieu) AS name
FROM gevu_lieux AS node,
gevu_lieux AS parent
WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.niv < 6
GROUP BY node.id_lieu
ORDER BY node.lft