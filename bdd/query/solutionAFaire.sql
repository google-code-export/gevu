SELECT
    	 GROUP_CONCAT(DISTINCT d.id_diag) diags
    	, GROUP_CONCAT(DISTINCT ld.id_lieu) diags
      , COUNT(DISTINCT d.id_diag) nbDiag
    	, d.id_critere
      , c.ref
      , ds.id_solution
    	FROM gevu_lieux as ld
    	INNER JOIN gevu_lieux as l ON l.id_lieu = 6671	AND ld.lft BETWEEN l.lft AND l.rgt
    	INNER JOIN gevu_diagnostics as d ON d.id_lieu = ld.id_lieu AND d.id_reponse IN (124,2) AND d.last = 1
    	INNER JOIN gevu_criteres as c ON c.id_critere = d.id_critere
		  LEFT JOIN gevu_diagnosticsxsolutions ds ON ds.id_diag = d.id_diag
    	WHERE ds.id_solution is null
		GROUP BY d.id_critere
		