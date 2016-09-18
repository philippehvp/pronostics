<?php
	include_once('common.php');

	$postedData = json_decode(file_get_contents("php://input"), true);
	$data = json_decode($postedData['data']);
	$nomUtilisateur = $data->Pronostiqueurs_NomUtilisateur;
	$nom = $data->Pronostiqueurs_Nom;
	$prenom = $data->Pronostiqueurs_Prenom;
	$motDePasse = $data->Pronostiqueurs_MotDePasse;
	$administrateur = $data->Pronostiqueurs_Administrateur;
	$dateDebutPresence = $data->Pronostiqueurs_DateDebutPresence;
	$dateFinPresence = $data->Pronostiqueurs_DateFinPresence;
	$mel = $data->Pronostiqueurs_MEL;
	$photo = $data->Pronostiqueurs_Photo;
	$premiereConnexion = $data->Pronostiqueurs_PremiereConnexion;
	$dateDeNaissance = $data->Pronostiqueurs_DateNaissance;
	$lieuDeResidence = $data->Pronostiqueurs_LieuDeResidence;
	$ambitions = $data->Pronostiqueurs_Ambitions;
	$palmares = $data->Pronostiqueurs_Palmares;
	$carriere = $data->Pronostiqueurs_Carriere;
	$commentaire = $data->Pronostiqueurs_Commentaire;
	$equipeFavorite = $data->Pronostiqueurs_EquipeFavorite;

	$sql =		'	INSERT INTO		pronostiqueurs' .
				'					(Pronostiqueur, Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Nom, Pronostiqueurs_Prenom' .
				'					,Pronostiqueurs_MotDePasse, Pronostiqueurs_Administrateur' .
				'					,Pronostiqueurs_DateDebutPresence, Pronostiqueurs_DateFinPresence' .
				'					,Pronostiqueurs_MEL, Pronostiqueurs_Photo, Pronostiqueurs_PremiereConnexion' .
				'					,Pronostiqueurs_DateDeNaissance' .
				'					,Pronostiqueurs_LieuDeResidence, Pronostiqueurs_Ambitions' .
				'					,Pronostiqueurs_Palmares, Pronostiqueurs_Carriere, Pronostiqueurs_Commentaire, Pronostiqueurs_EquipeFavorite' .
				'					,Pronostiqueurs_AfficherTropheesChampionnat, Pronostiqueurs_CodeCouleur, Themes_Theme)' .
				'	SELECT			fn_dernierpronostiqueur() + 1, :nomUtilisateur, :nom, :prenom, :motDePasse, :administrateur, :dateDebutPresence, :dateFinPresence, :mel, :photo, :premiereConnexion, :dateDeNaissance' .
				'					,:lieuDeResidence, :ambitions, :palmares, :carriere, :commentaire, :equipeFavorite' .
				'					,0, \'\', 1';

	$stmt = $db->prepare($sql);
	$stmt->bindParam(':nomUtilisateur', $nomUtilisateur);
	$stmt->bindParam(':nom', $nom);
	$stmt->bindParam(':prenom', $prenom);

	$stmt->bindParam(':motDePasse', $motDePasse);
	$stmt->bindParam(':administrateur', $administrateur);
	$stmt->bindParam(':dateDebutPresence', $dateDebutPresence);

	$stmt->bindParam(':dateFinPresence', $dateFinPresence);
	$stmt->bindParam(':mel', $mel);
	$stmt->bindParam(':photo', $photo);

	$stmt->bindParam(':premiereConnexion', $premiereConnexion);
	$stmt->bindParam(':dateDeNaissance', $dateDeNaissance);
	$stmt->bindParam(':lieuDeResidence', $lieuDeResidence);
	
	$stmt->bindParam(':ambitions', $ambitions);
	$stmt->bindParam(':palmares', $palmares);
	$stmt->bindParam(':carriere', $carriere);

	$stmt->bindParam(':commentaire', $commentaire);
	$stmt->bindParam(':equipeFavorite', $equipeFavorite);

	$errorMessage = '';
	try {
		$stmt->execute();
	} catch (PDOException $e) {
    	$errorMessage = 'Error during execution of statement : ' . $e->getMessage();
	}
	finally {
		//$stmt->close();
	}

	echo json_encode($errorMessage);

	/*$sql =		'	SELECT		Pronostiqueur' .
				'				,Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Nom, Pronostiqueurs_Prenom' .
				'				,Pronostiqueurs_MotDePasse, Pronostiqueurs_Administrateur' .
				'				,Pronostiqueurs_DateDebutPresence, Pronostiqueurs_DateFinPresence' .
				'				,Pronostiqueurs_MEL, Pronostiqueurs_Photo, Pronostiqueurs_PremiereConnexion' .
				'				,CONCAT(MONTH(Pronostiqueurs_DateDeNaissance), "/", DAYOFMONTH(Pronostiqueurs_DateDeNaissance), "/", YEAR(Pronostiqueurs_DateDeNaissance)) AS Pronostiqueurs_DateDeNaissance' .
				'				,Pronostiqueurs_LieuDeResidence, Pronostiqueurs_Ambitions' .
				'				,Pronostiqueurs_Palmares, Pronostiqueurs_Carriere, Pronostiqueurs_Commentaire, Pronostiqueurs_EquipeFavorite' .
				'				,Pronostiqueurs_AfficherTropheesChampionnat, Pronostiqueurs_CodeCouleur, Themes_Theme' .
				'	FROM		pronostiqueurs';

	$query = $db->query($sql);
	$forecasters = $query->fetchAll();*/


?>



