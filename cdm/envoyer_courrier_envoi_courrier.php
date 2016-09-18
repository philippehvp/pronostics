<?php
	include('commun_administrateur.php');
	
	// Lecture des paramètres passés à la page
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;

	// Fonction de lecture du corps du message
	function lireCorpsCourrier($journee, $bdd) {
		$ordreSQL =		'	SELECT		Courriers_Message' .
						'	FROM		cdm_courriers' .
						'	WHERE		Courriers_JourneeEnCours = ' . $journee;
		$req = $bdd->query($ordreSQL);
		$message = $req->fetchAll();
		return $message;
	}


	// Adresse de destination
	$adresseDestinataire = 'philippe.hvp@gmail.com';
	
	// Fabrication de la fin de chaîne (différente selon les serveurs de mai)
	if(!preg_match('#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#', $adresseDestinataire))
		$retourLigne = '\r\n';
	else
		$retourLigne = '\n';

	// Création de la frontière
	//$frontiere = '-----='.md5(rand());
	 
	// Sujet du message
	$sujetCourrier = 'Coupe du Monde 2014 : compte-rendu de journée';
	 
	// En-tête du courrier
	$enteteCourrier = 'MIME-Version: 1.0' . $retourLigne;
	$enteteCourrier.= 'Content-Type: text/html; charset=ISO-8859-1' . $retourLigne;
	$enteteCourrier .= 'From: Alexandre Pueyo <alexandrepueyo@hotmail.com>' . $retourLigne;
	$enteteCourrier .= 'Reply-to: Alexandre Pueyo <alexandrepueyo@hotmail.com>' . $retourLigne;
	 
	// Corps du courrier
	$messageCourrier = lireCorpsCourrier($journee, $bdd) . $retourLigne;
	
	if($messageCourrier) {
		$envoiCourrier = mail($adresseDestinataire, $sujetCourrier, $messageCourrier, $enteteCourrier);
	}
?>
