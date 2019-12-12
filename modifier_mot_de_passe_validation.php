<?php
	include_once('commun.php');

	// Lecture des paramètres passés à la page
	$motDePasseActuel = isset($_POST["motDePasseActuel"]) ? $_POST["motDePasseActuel"] : null;
	$motDePasse = isset($_POST["motDePasse"]) ? $_POST["motDePasse"] : null;
	$motDePasseConfirmation = isset($_POST["motDePasseConfirmation"]) ? $_POST["motDePasseConfirmation"] : null;


	// Vérification de l'ancien mot de passe
	$req = $bdd->prepare('SELECT Pronostiqueurs_MotDePasse FROM pronostiqueurs WHERE Pronostiqueur = ? LIMIT 1');
	$req->execute(array($_SESSION["pronostiqueur"]));

	while($donnees = $req->fetch())
		$verificationMotDePasseActuel = $donnees["Pronostiqueurs_MotDePasse"];
	$req->closeCursor();

	// Le mot de passe actuel est-il le bon ?
	if($motDePasseActuel != $verificationMotDePasseActuel) {
		// Erreur
		$_SESSION["erreurModification"] = 1;
		header('Location: modifier_mot_de_passe.php');
	}

	// Vérification que les deux mots de passe ne sont pas différents
	if($motDePasse == $motDePasseConfirmation && $motDePasse != null) {
		// Modification du mot de passe
		$req = $bdd->prepare('UPDATE pronostiqueurs SET Pronostiqueurs_MotDePasse = ?, Pronostiqueurs_PremiereConnexion = 0 WHERE Pronostiqueur = ?');
		$req->execute(array($motDePasse, $_SESSION["pronostiqueur"]));

		setcookie('lepoulpeg_mdp', $motDePasse, time() + (7 * 24 * 3600), null, null, false, true);

		$_SESSION["erreurModification"] = 0;
		header('Location: modifier_mot_de_passe.php?motdepasse=1');
	}
	else {
		// Erreur
		$_SESSION["erreurModification"] = 1;
		header('Location: modifier_mot_de_passe.php');
	}
?>
