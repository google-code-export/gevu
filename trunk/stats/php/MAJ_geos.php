<?php

require_once 'codes.php';


$query = 'UPDATE gevu_geos SET  pitch = 0.00000000000, heading = 0.000000000000, zoom_cell = 0.00 WHERE gevu_geos.id_lieu = '.$_GET['idLieu']
AND isset(.$_GET['pitch'])
AND isset(.$_GET['heading'])
AND isset(.$_GET['zoom_cell'])
 ;



