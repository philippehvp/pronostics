<?php
	include_once('commun_administrateur.php');
	
	// Activation / désactivation d'une journée
	// La requête va basculer l'état d'activation comme un interrupteur
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	
	$ordreSQL =		'	UPDATE		journees' .
					'	SET			Journees_Active =	CASE' .
					'										WHEN	Journees_Active = 1' .
					'										THEN	0' .
					'										ELSE	1' .
					'									END' .
					'	WHERE		Journee = ' . $journee;
	$bdd->exec($ordreSQL);
	
	$ordreSQL =		'	SELECT		Journees_Active' .
					'	FROM		journees'.
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetch();
	if($donnees["Journees_Active"] == 1)
		echo 'Journée activée';
	else
		echo 'Journée inactive';
	
?>