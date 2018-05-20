<?php
	include('commun.php');
	
	// Lecture des paramètres passés à la page
	$motDePasse = isset($_POST["motDePasse"]) ? $_POST["motDePasse"] : null;
	$motDePasseConfirmation = isset($_POST["motDePasseConfirmation"]) ? $_POST["motDePasseConfirmation"] : null;
	
	// Vérification que les deux mots de passe ne sont pas diff�rents
	if($motDePasse == $motDePasseConfirmation && $motDePasse != null) {
		// Modification du mot de passe
		$req = $bdd->prepare('UPDATE cdm_pronostiqueurs SET Pronostiqueurs_MotDePasse = ?, Pronostiqueurs_PremiereConnexion = 0 WHERE Pronostiqueur = ?');
		$req->execute(array($motDePasse, $_SESSION["cdm_pronostiqueur"]));
		
		setcookie('cdm_mdp', $motDePasse, time() + (7 * 24 * 3600), null, null, false, true);
		
		$_SESSION["cdm_erreur_modification"] = 0;
		header('Location: accueil.php');
	}
	else {
		// Erreur
		$_SESSION["cdm_erreur_modification"] = 1;
		header('Location: premiere_connexion.php');
	}
?>
