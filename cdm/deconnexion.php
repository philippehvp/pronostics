<?php
	session_start();
	
	$_SESSION["cdm_pronostiqueur"] = 0;
	$_SESSION["cdm_nom_pronostiqueur"] = NULL;
	$_SESSION["cdm_erreur_login"] = 0;
	$_SESSION["cdm_administrateur"] = 0;
	
	header('Location: index.php');

?>

<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>
<body>
</body>
</html>