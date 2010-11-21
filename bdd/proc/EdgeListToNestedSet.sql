DROP PROCEDURE IF EXISTS gevu_solus.EdgeListToNestedSet;
CREATE PROCEDURE gevu_solus.`EdgeListToNestedSet`( edgeTable CHAR(64), idCol CHAR(64), parentCol CHAR(64) )
BEGIN
  DECLARE maxrightedge, rows SMALLINT DEFAULT 0;
  DECLARE current SMALLINT DEFAULT 1;
  DECLARE nextedge SMALLINT DEFAULT 2;
  DROP TEMPORARY TABLE IF EXISTS tree;
  CREATE TEMPORARY TABLE tree( childID INT, parentID INT );
  SET @sql = CONCAT( 'INSERT INTO tree SELECT ', idCol, ',', parentCol, ' FROM ', edgeTable );
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  DROP TABLE IF EXISTS gevu_tree;
  CREATE TABLE gevu_tree (
    top SMALLINT, nodeID SMALLINT, leftedge SMALLINT, rightedge SMALLINT,
    KEY(nodeID,leftedge,rightedge)   /* not needed for small trees*/
  ) ENGINE=HEAP;
  SET @sql = CONCAT( 'SELECT DISTINCT f.', parentCol, ' INTO @root FROM ',
                     edgeTable, ' AS f LEFT JOIN ', edgeTable, ' AS t ON f.',
                     parentCol, '=', 't.', idCol, ' WHERE t.', idCol, ' IS NULL'
                   );
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DROP PREPARE stmt;
  SET maxrightedge = 2 * (1 + (SELECT + COUNT(*) FROM tree));
  INSERT INTO gevu_tree VALUES( 1, @root, 1, maxrightedge );
  WHILE nextedge < maxrightedge DO
    SELECT *
    FROM gevu_tree AS s
    JOIN tree AS t ON s.nodeID=t.parentID AND s.top=current;
    SET rows = FOUND_ROWS();
    IF rows > 0 THEN
      BEGIN
        INSERT INTO gevu_tree
          SELECT current+1, MIN(t.childID), nextedge, NULL
          FROM gevu_tree AS s
          JOIN tree AS t ON s.nodeID = t.parentID AND s.top = current;
        DELETE FROM tree
        WHERE childID = (SELECT nodeID FROM gevu_tree WHERE top=(current+1));
        SET nextedge = nextedge + 1;
        SET current = current + 1;
       END;
     ELSE
       BEGIN
         UPDATE gevu_tree
         SET rightedge=nextedge, top = -top
         WHERE top=current;
         SET nextedge=nextedge+1;
         SET current=current-1;
       END;
    END IF;
  END WHILE;
  SET rows := ( SELECT COUNT(*) FROM tree );
  DROP TEMPORARY TABLE tree;
  IF rows > 0 THEN
    SELECT 'Orphaned rows remain';
  END IF;
END;
