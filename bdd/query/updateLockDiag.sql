UPDATE gevu_lieux SET lock_diag = "toto" 
WHERE id_lieu IN (
  SELECT enfant.id_lieu
	FROM gevu_lieux AS node, gevu_lieux AS enfant
	WHERE enfant.lft BETWEEN node.lft AND node.rgt AND node.id_lieu = 6671
	ORDER BY enfant.lft)