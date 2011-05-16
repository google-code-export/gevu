<?php
class GEVU_Diagnostique{
    public function getAll()
    {
        $dbSolus = new Model_DbTable_Gevu_lieux();
        $rs = $dbSolus->getAll();
        return $rs->toArray();
    }
    
    public function getOne($idLieu)
    {
        $dbSolus = new Model_DbTable_Gevu_lieux();
        $rs = $dbSolus->findById_lieu($idLieu);
        return $rs->toArray();
    }
}
?>