SELECT node.lib AS parLib, parent.lib, parent.id_lieu, parent.niv 
FROM gevu_lieux AS node
 INNER JOIN gevu_lieux AS parent ON node.lft BETWEEN parent.lft AND parent.rgt 
 INNER JOIN gevu_lieux lh ON lh.id_lieu = 6671 AND lh.niv < parent.niv 
WHERE (node.id_lieu = 6729) 
ORDER BY parent.lft ASC