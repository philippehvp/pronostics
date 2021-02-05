<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>


<body>
	<?php
		$nomPage = 'utilitaires.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
	?>

	<?php

		/*$ordreSQL = 'UPDATE journees SET Journees_DateMAJ = NOW() WHERE Journee = 38';
		$req = $bdd->exec($ordreSQL);*/

		/*$ordreSQL = 'UPDATE modules SET Modules_CritereRafraichissement = NOW() WHERE Module = 5';
		$req = $bdd->exec($ordreSQL);*/

		$ordreSQL = 'UPDATE modules SET Modules_PageVerification = \'module_tchat_verification.php\' WHERE Modules_Page = \'module_tchat.php\'';
		$req = $bdd->exec($ordreSQL);


		/*$ordreSQL = 'SELECT Module, Modules_Nom, Modules_Page, Modules_PageVerification FROM modules WHERE Module < 10';
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetchAll();
		var_dump($donnees);*/

		/*$ordreSQL =	'	SELECT		MAX(Message) AS Message' .
					'	FROM		messages' .
					'	WHERE		TchatGroupes_TchatGroupe = ' . 500;
		$req = $bdd->query($ordreSQL);
		$message = $req->fetchAll();
		echo($message[0]["Message"]);*/


	?>


</body>
</html>