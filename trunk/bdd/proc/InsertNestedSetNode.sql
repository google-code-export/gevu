DROP PROCEDURE IF EXISTS gevu_solus.InsertNestedSetNode;
CREATE PROCEDURE gevu_solus.`InsertNestedSetNode`( IN node SMALLINT, IN parent SMALLINT )
BEGIN
  DECLARE parentleft, parentright SMALLINT DEFAULT 0;
  SELECT lft, rgt
    INTO parentleft, parentright
  FROM gevu_lieux
  WHERE id_lieu = parent;
  IF FOUND_ROWS() = 1 THEN
    BEGIN
      UPDATE gevu_lieux
        SET rgt = rgt + 2
      WHERE rgt > parentleft;
      UPDATE gevu_lieux
        SET lft = lft + 2
      WHERE lft > parentleft;
      INSERT INTO gevu_lieux
        VALUES ( 0, node, parentleft + 1, parentleft + 2 );
    END;
  END IF;
END;
