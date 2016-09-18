<?php
	include_once('common.php');

	$postedData = json_decode(file_get_contents("php://input"), true);
	$data = json_decode($postedData['data']);
	$pronostiqueur = $data->Pronostiqueur;
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
	$dateDeNaissance = $data->Pronostiqueurs_DateDeNaissance;
	$lieuDeResidence = $data->Pronostiqueurs_LieuDeResidence;
	$ambitions = $data->Pronostiqueurs_Ambitions;
	$palmares = $data->Pronostiqueurs_Palmares;
	$carriere = $data->Pronostiqueurs_Carriere;
	$commentaire = $data->Pronostiqueurs_Commentaire;
	$equipeFavorite = $data->Pronostiqueurs_EquipeFavorite;

	$sql =		'	UPDATE			pronostiqueurs' .
				'	SET				Pronostiqueurs_NomUtilisateur = :nomUtilisateur, Pronostiqueurs_Nom = :nom, Pronostiqueurs_Prenom = :prenom' .
				'					,Pronostiqueurs_MotDePasse = :motDePasse, Pronostiqueurs_Administrateur = :administrateur' .
				'					,Pronostiqueurs_DateDebutPresence = :dateDebutPresence, Pronostiqueurs_DateFinPresence = :dateFinPresence' .
				'					,Pronostiqueurs_MEL = :mel, Pronostiqueurs_Photo = :photo, Pronostiqueurs_PremiereConnexion = :premiereConnexion' .
				'					,Pronostiqueurs_DateDeNaissance = :dateDeNaissance' .
				'					,Pronostiqueurs_LieuDeResidence = :lieuDeResidence, Pronostiqueurs_Ambitions = :ambitions' .
				'					,Pronostiqueurs_Palmares = :palmares, Pronostiqueurs_Carriere = :carriere, Pronostiqueurs_Commentaire = :commentaire, Pronostiqueurs_EquipeFavorite = :equipeFavorite' .
				'	WHERE			Pronostiqueur = :pronostiqueur';

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
	$stmt->bindParam(':pronostiqueur', $pronostiqueur);

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
?>



