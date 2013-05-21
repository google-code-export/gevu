SELECT a.id_lieu, a.ref
, la.lib, la.lft, la.rgt
, COUNT(DISTINCT s.id_stat) nbLog, SUM(2013-Annee_Construction) sumAge, SUM(2013-Annee_Construction)/COUNT(DISTINCT s.id_stat) moyAge
, lat, lng, kml
FROM gevu_antennes a
	INNER JOIN gevu_geos g ON g.id_lieu = a.id_lieu
  INNER JOIN gevu_lieux la ON la.id_lieu = a.id_lieu
  INNER JOIN gevu_lieux lg ON lg.lft BETWEEN la.lft AND la.rgt
  INNER JOIN gevu_stats s ON s.id_lieu = lg.id_lieu AND s.Categorie_Module = "L" AND s.Annee_construction != ""
WHERE a.ref != ""
GROUP BY a.id_lieu
ORDER BY ref;
