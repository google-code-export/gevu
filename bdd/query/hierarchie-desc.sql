SELECT
 node.id_lieu
-- , enfant.id_lieu, enfant.lib, enfant.id_instant, enfant.lft, enfant.rgt, enfant.niv, enfant.maj, enfant.lieu_parent, enfant.id_type_controle, enfant.id_rubrique, enfant.id_parent, enfant.lock_diag
 , GROUP_CONCAT(enfant.id_lieu ORDER BY enfant.rgt DESC)
FROM gevu_lieux AS node, gevu_lieux AS enfant
WHERE enfant.lft BETWEEN node.lft AND node.rgt
  AND node.id_lieu = 6169
 GROUP BY node.id_lieu
ORDER BY enfant.niv;