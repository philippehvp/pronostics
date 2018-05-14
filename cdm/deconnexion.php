<?php
	session_start();
	
	$_SESSION["cdm_pronostiqueur"] = 0;
	$_SESSION["nomPronostiqueur"] = NULL;
	$_SESSION["erreurLogin"] = 0;
	$_SESSION["administrateur"] = 0;
	
	header('Location: index.php');

?>

<?php
	include('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
</head>
<body>
</body>
</html>