<?php
	include_once('common.php');

	$sql =		'	SELECT		Pronostiqueur' .
				'				,Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Nom, Pronostiqueurs_Prenom' .
				'				,Pronostiqueurs_MotDePasse, Pronostiqueurs_Administrateur' .
				'				,Pronostiqueurs_MEL, Pronostiqueurs_Photo, Pronostiqueurs_PremiereConnexion' .
				'				,CONCAT(MONTH(Pronostiqueurs_DateDeNaissance), "/", DAYOFMONTH(Pronostiqueurs_DateDeNaissance), "/", YEAR(Pronostiqueurs_DateDeNaissance)) AS Pronostiqueurs_DateDeNaissance' .
				'				,CONCAT(MONTH(Pronostiqueurs_DateDebutPresence), "/", DAYOFMONTH(Pronostiqueurs_DateDebutPresence), "/", YEAR(Pronostiqueurs_DateDebutPresence)) AS Pronostiqueurs_DateDebutPresence' .
				'				,CONCAT(MONTH(Pronostiqueurs_DateFinPresence), "/", DAYOFMONTH(Pronostiqueurs_DateFinPresence), "/", YEAR(Pronostiqueurs_DateFinPresence)) AS Pronostiqueurs_DateFinPresence' .
				'				,Pronostiqueurs_LieuDeResidence, Pronostiqueurs_Ambitions' .
				'				,Pronostiqueurs_Palmares, Pronostiqueurs_Carriere, Pronostiqueurs_Commentaire, Pronostiqueurs_EquipeFavorite' .
				'				,Pronostiqueurs_AfficherTropheesChampionnat, Pronostiqueurs_CodeCouleur, Themes_Theme' .
				'	FROM		pronostiqueurs';

	$query = $db->query($sql);
	$forecasters = $query->fetchAll();

	echo json_encode($forecasters);

?>



