<?php 
session_start();
extract($_SESSION,EXTR_OVERWRITE);
 
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {    setcookie(session_name(), '', time()-42000, '/');}
session_destroy ();

if(isset($_GET['site'])){
	$urlsite = "diagnostic.php?site=".$_GET['site'];
}else{
	$urlsite = "diagnostic.php";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Document sans nom</title>
</head>
<body>
<SCRIPT LANGUAGE="JavaScript">
 if (window !=top ) {top.location=window.location;}
</SCRIPT>

<script language='Javascript'>
location.href = '<?php echo $urlsite; ?>'
</script>
</body>
</html>
