<?php
	session_start();
	include_once('commun.php');
	
	
	// Mise à 0 de la présence du pronostiqueur sur le site
	$ordreSQL =		'	UPDATE		pronostiqueurs_activite' .
					'	SET			PronostiqueursActivite_Date = NULL' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];
					
	$req = $bdd->exec($ordreSQL);
	
	unset($_SESSION["pronostiqueur"]);
	unset($_SESSION["nom_pronostiqueur"]);
	unset($_SESSION["prenom_pronostiqueur"]);
	unset($_SESSION["administrateur"]);
	unset($_SESSION["photo_pronostiqueur"]);
	unset($_SESSION["theme_pronostiqueur"]);
	
	header('Location: index.php');

?>

<?php
	
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