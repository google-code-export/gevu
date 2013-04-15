SELECT node.lib AS parLib, parent.lib, parent.id_lieu, parent.niv, parent.lock_diag 
FROM gevu_lieux AS node
 INNER JOIN gevu_lieux AS parent ON node.lft BETWEEN parent.lft AND parent.rgt 
WHERE (node.id_lieu = 6662) 
ORDER BY parent.lft ASC