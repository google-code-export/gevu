SELECT `node`.`lib` AS `parLib`, `parent`.`lib`, `parent`.`id_lieu`, `parent`.`niv` 
FROM `gevu_lieux` AS `node`
 INNER JOIN `gevu_lieux` AS `parent` ON node.lft BETWEEN parent.lft AND parent.rgt 
WHERE (node.id_lieu = 9) 
ORDER BY `parent`.`lft` ASC