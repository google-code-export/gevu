DROP PROCEDURE IF EXISTS gevu_solus.MoveNode;
CREATE PROCEDURE gevu_solus.`MoveNode`( IN node SMALLINT, IN parentleft SMALLINT, IN instant SMALLINT )
BEGIN
    BEGIN
      UPDATE gevu_lieux
        SET rgt = rgt + 2
      WHERE rgt > parentleft AND id_instant = instant;
      UPDATE gevu_lieux
        SET lft = lft + 2
      WHERE lft > parentleft AND id_instant = instant;
      UPDATE gevu_lieux
        SET lft = parentleft + 1, rgt = parentleft + 2
      WHERE id_lieu = node;
    END;
END;
