<?php

require_once 'codes.php';


$query = 'UPDATE gevu_geos SET  pitch, heading, zoom_cell =  "0.000000000000" WHERE gevu_geos.id_lieu = '.$_GET['idLieu'];



