<?php
	include_once('commun.php');

	// Affichage du détail des points d'un pronostiqueur

	// Lecture des paramètres passés à la page
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : 0;
	
	
	function afficherTropheesL1($bdd, $pronostiqueurConsulte, $trophees) {
		// Ligue 1
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_L1_Premier_Nombre, PantheonsPoints_L1_Deuxieme_Nombre, PantheonsPoints_L1_Troisieme_Nombre, PantheonsPoints_L1_Quatrieme_Nombre, PantheonsPoints_L1_Cinquieme_Nombre' .
						'				,PantheonsPoints_L1_NombreCompetiteurs, PantheonsPoints_L1_SoulierOr_Nombre, PantheonsPoints_L1_Brandao_Nombre, PantheonsPoints_L1_DjaDjeDje_Nombre' .
						'				,PantheonsPoints_L1_NombrePoulpesOr_Nombre, PantheonsPoints_L1_NombrePoulpesArgent_Nombre, PantheonsPoints_L1_NombrePoulpesBronze_Nombre' .
						'				,PantheonsPoints_L1_NombreSouliersOr_Nombre, PantheonsPoints_L1_NombreBrandao_Nombre, PantheonsPoints_L1_NombreDjaDjeDje_Nombre, PantheonsPoints_L1_RecordPoints_Nombre, PantheonsPoints_L1_RecordPointsButeur_Nombre' .
						'				,PantheonsPoints_L1_DixSurOnze_Nombre, PantheonsPoints_L1_OnzeSurOnze_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesL1 = $req->fetchAll();
		if($donneesL1[0]["PantheonsPoints_L1_Premier_Nombre"] != 0)					echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesL1[0]["PantheonsPoints_L1_Premier_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesL1[0]["PantheonsPoints_L1_Deuxieme_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesL1[0]["PantheonsPoints_L1_Troisieme_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_Quatrieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">4ème place : ' . $donneesL1[0]["PantheonsPoints_L1_Quatrieme_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_Cinquieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">5ème place : ' . $donneesL1[0]["PantheonsPoints_L1_Cinquieme_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombreCompetiteurs"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Points classement : ' . $donneesL1[0]["PantheonsPoints_L1_NombreCompetiteurs"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_SoulierOr_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Soulier d\'Or : ' . $donneesL1[0]["PantheonsPoints_L1_SoulierOr_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_Brandao_Nombre"] != 0)					echo '<label class="pantheon--detail--nom-categorie">Brandao : ' . $donneesL1[0]["PantheonsPoints_L1_Brandao_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_DjaDjeDje_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Dja Djé Djé : ' . $donneesL1[0]["PantheonsPoints_L1_DjaDjeDje_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombrePoulpesOr_Nombre"] != 0)			echo '<label class="pantheon--detail--nom-categorie">Nb poulpes d\'Or : ' . $donneesL1[0]["PantheonsPoints_L1_NombrePoulpesOr_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombrePoulpesArgent_Nombre"] != 0)		echo '<label class="pantheon--detail--nom-categorie">Nb poulpes d\'Argent : ' . $donneesL1[0]["PantheonsPoints_L1_NombrePoulpesArgent_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombrePoulpesBronze_Nombre"] != 0)		echo '<label class="pantheon--detail--nom-categorie">Nb poulpes de Bronze : ' . $donneesL1[0]["PantheonsPoints_L1_NombrePoulpesBronze_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombreSouliersOr_Nombre"] != 0)		echo '<label class="pantheon--detail--nom-categorie">Nb souliers d\'Or : ' . $donneesL1[0]["PantheonsPoints_L1_NombreSouliersOr_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombreBrandao_Nombre"] != 0)			echo '<label class="pantheon--detail--nom-categorie">Nb trophées Brandao : ' . $donneesL1[0]["PantheonsPoints_L1_NombreBrandao_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_NombreDjaDjeDje_Nombre"] != 0)			echo '<label class="pantheon--detail--nom-categorie">Nb trophées Dja Djé Djé : ' . $donneesL1[0]["PantheonsPoints_L1_NombreDjaDjeDje_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_RecordPoints_Nombre"] != 0)			echo '<label class="pantheon--detail--nom-categorie">Nb records pts : ' . $donneesL1[0]["PantheonsPoints_L1_RecordPoints_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_RecordPointsButeur_Nombre"] != 0)		echo '<label class="pantheon--detail--nom-categorie">Nb records buteur : ' . $donneesL1[0]["PantheonsPoints_L1_RecordPointsButeur_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_DixSurOnze_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Nb 10/11 : ' . $donneesL1[0]["PantheonsPoints_L1_DixSurOnze_Nombre"] . '</label>';
		if($donneesL1[0]["PantheonsPoints_L1_OnzeSurOnze_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Nb 11/11 : ' . $donneesL1[0]["PantheonsPoints_L1_OnzeSurOnze_Nombre"] . '</label>';
	}
	
	function afficherTropheesLDC($bdd, $pronostiqueurConsulte, $trophees) {
		// Ligue des Champions
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_LDC_Premier_Nombre, PantheonsPoints_LDC_Deuxieme_Nombre, PantheonsPoints_LDC_Troisieme_Nombre, PantheonsPoints_LDC_Quatrieme_Nombre, PantheonsPoints_LDC_Cinquieme_Nombre' .
						'				,PantheonsPoints_LDC_SoulierOr_Nombre, PantheonsPoints_LDC_Brandao_Nombre, PantheonsPoints_LDC_DjaDjeDje_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesLDC = $req->fetchAll();
		if($donneesLDC[0]["PantheonsPoints_LDC_Premier_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesLDC[0]["PantheonsPoints_LDC_Premier_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesLDC[0]["PantheonsPoints_LDC_Deuxieme_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesLDC[0]["PantheonsPoints_LDC_Troisieme_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_Quatrieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">4ème place : ' . $donneesLDC[0]["PantheonsPoints_LDC_Quatrieme_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_Cinquieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">5ème place : ' . $donneesLDC[0]["PantheonsPoints_LDC_Cinquieme_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_SoulierOr_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Soulier d\'Or : ' . $donneesLDC[0]["PantheonsPoints_LDC_SoulierOr_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_Brandao_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Brandao : ' . $donneesLDC[0]["PantheonsPoints_LDC_Brandao_Nombre"] . '</label>';
		if($donneesLDC[0]["PantheonsPoints_LDC_DjaDjeDje_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Dja Djé Djé : ' . $donneesLDC[0]["PantheonsPoints_LDC_DjaDjeDje_Nombre"] . '</label>';
	}
	
	function afficherTropheesEL($bdd, $pronostiqueurConsulte, $trophees) {
		// Europa League
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_EL_Premier_Nombre, PantheonsPoints_EL_Deuxieme_Nombre, PantheonsPoints_EL_Troisieme_Nombre, PantheonsPoints_EL_Quatrieme_Nombre, PantheonsPoints_EL_Cinquieme_Nombre' .
						'				,PantheonsPoints_EL_SoulierOr_Nombre, PantheonsPoints_EL_Brandao_Nombre, PantheonsPoints_EL_DjaDjeDje_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesEL = $req->fetchAll();
		if($donneesEL[0]["PantheonsPoints_EL_Premier_Nombre"] != 0)					echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesEL[0]["PantheonsPoints_EL_Premier_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesEL[0]["PantheonsPoints_EL_Deuxieme_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesEL[0]["PantheonsPoints_EL_Troisieme_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_Quatrieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">4ème place : ' . $donneesEL[0]["PantheonsPoints_EL_Quatrieme_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_Cinquieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">5ème place : ' . $donneesEL[0]["PantheonsPoints_EL_Cinquieme_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_SoulierOr_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Soulier d\'Or : ' . $donneesEL[0]["PantheonsPoints_EL_SoulierOr_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_Brandao_Nombre"] != 0)					echo '<label class="pantheon--detail--nom-categorie">Brandao : ' . $donneesEL[0]["PantheonsPoints_EL_Brandao_Nombre"] . '</label>';
		if($donneesEL[0]["PantheonsPoints_EL_DjaDjeDje_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Dja Djé Djé : ' . $donneesEL[0]["PantheonsPoints_EL_DjaDjeDje_Nombre"] . '</label>';
	}

	function afficherTropheesCDF($bdd, $pronostiqueurConsulte, $trophees) {
		// Coupe de France
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_CDF_Premier_Nombre, PantheonsPoints_CDF_Deuxieme_Nombre, PantheonsPoints_CDF_Troisieme_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesCDF = $req->fetchAll();
		if($donneesCDF[0]["PantheonsPoints_CDF_Premier_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesCDF[0]["PantheonsPoints_CDF_Premier_Nombre"] . '</label>';
		if($donneesCDF[0]["PantheonsPoints_CDF_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesCDF[0]["PantheonsPoints_CDF_Deuxieme_Nombre"] . '</label>';
		if($donneesCDF[0]["PantheonsPoints_CDF_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesCDF[0]["PantheonsPoints_CDF_Troisieme_Nombre"] . '</label>';
	}
	
	function afficherTropheesCDM($bdd, $pronostiqueurConsulte, $trophees) {
		// Coupe du Monde
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_CDM_Premier_Nombre, PantheonsPoints_CDM_Deuxieme_Nombre, PantheonsPoints_CDM_Troisieme_Nombre, PantheonsPoints_CDM_Quatrieme_Nombre, PantheonsPoints_CDM_Cinquieme_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesCDM = $req->fetchAll();
		if($donneesCDM[0]["PantheonsPoints_CDM_Premier_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesCDM[0]["PantheonsPoints_CDM_Premier_Nombre"] . '</label>';
		if($donneesCDM[0]["PantheonsPoints_CDM_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesCDM[0]["PantheonsPoints_CDM_Deuxieme_Nombre"] . '</label>';
		if($donneesCDM[0]["PantheonsPoints_CDM_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesCDM[0]["PantheonsPoints_CDM_Troisieme_Nombre"] . '</label>';
		if($donneesCDM[0]["PantheonsPoints_CDM_Quatrieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">4ème place : ' . $donneesCDM[0]["PantheonsPoints_CDM_Quatrieme_Nombre"] . '</label>';
		if($donneesCDM[0]["PantheonsPoints_CDM_Cinquieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">5ème place : ' . $donneesCDM[0]["PantheonsPoints_CDM_Cinquieme_Nombre"] . '</label>';
	}
	
	function afficherTropheesCA($bdd, $pronostiqueurConsulte, $trophees) {
		// Copa America
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_CA_Premier_Nombre, PantheonsPoints_CA_Deuxieme_Nombre, PantheonsPoints_CA_Troisieme_Nombre, PantheonsPoints_CA_Quatrieme_Nombre, PantheonsPoints_CA_Cinquieme_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesCA = $req->fetchAll();
		if($donneesCA[0]["PantheonsPoints_CA_Premier_Nombre"] != 0)					echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesCA[0]["PantheonsPoints_CA_Premier_Nombre"] . '</label>';
		if($donneesCA[0]["PantheonsPoints_CA_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesCA[0]["PantheonsPoints_CA_Deuxieme_Nombre"] . '</label>';
		if($donneesCA[0]["PantheonsPoints_CA_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesCA[0]["PantheonsPoints_CA_Troisieme_Nombre"] . '</label>';
		if($donneesCA[0]["PantheonsPoints_CA_Quatrieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">4ème place : ' . $donneesCA[0]["PantheonsPoints_CA_Quatrieme_Nombre"] . '</label>';
		if($donneesCA[0]["PantheonsPoints_CA_Cinquieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">5ème place : ' . $donneesCA[0]["PantheonsPoints_CA_Cinquieme_Nombre"] . '</label>';
	}

	function afficherTropheesEuro($bdd, $pronostiqueurConsulte, $trophees) {
		// Euro
		echo '<label class="pantheon--detail--nom-categorie">Trophées : ' . $trophees[0]["Nombre_Trophees"] . ' (' . $trophees[0]["Points_Trophees"] . ' points)</label>';
		
		// Lecture des trophées obttenus
		$ordreSQL =		'	SELECT		PantheonsPoints_EURO_Premier_Nombre, PantheonsPoints_EURO_Deuxieme_Nombre, PantheonsPoints_EURO_Troisieme_Nombre, PantheonsPoints_EURO_Quatrieme_Nombre, PantheonsPoints_EURO_Cinquieme_Nombre' .
						'	FROM		vue_pointspantheon' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueurConsulte;
		$req = $bdd->query($ordreSQL);
		$donneesEuro = $req->fetchAll();
		if($donneesEuro[0]["PantheonsPoints_EURO_Premier_Nombre"] != 0)					echo '<label class="pantheon--detail--nom-categorie">Victoires : ' . $donneesEuro[0]["PantheonsPoints_EURO_Premier_Nombre"] . '</label>';
		if($donneesEuro[0]["PantheonsPoints_EURO_Deuxieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">2ème place : ' . $donneesEuro[0]["PantheonsPoints_EURO_Deuxieme_Nombre"] . '</label>';
		if($donneesEuro[0]["PantheonsPoints_EURO_Troisieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">3ème place : ' . $donneesEuro[0]["PantheonsPoints_EURO_Troisieme_Nombre"] . '</label>';
		if($donneesEuro[0]["PantheonsPoints_EURO_Quatrieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">4ème place : ' . $donneesEuro[0]["PantheonsPoints_EURO_Quatrieme_Nombre"] . '</label>';
		if($donneesEuro[0]["PantheonsPoints_EURO_Cinquieme_Nombre"] != 0)				echo '<label class="pantheon--detail--nom-categorie">5ème place : ' . $donneesEuro[0]["PantheonsPoints_EURO_Cinquieme_Nombre"] . '</label>';
	}
	
	// L'affichage du détail concerne uniquement les compétitions pour lesquelles le pronostiqueur consulté a obtenu au moins un trophé / récompense

	// Lecture du nombre de trophées et de points par compétition principale
	$ordreSQL =		'	SELECT		fn_nombretrophees_l1(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_l1(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesL1 = $req->fetchAll();

	$ordreSQL =		'	SELECT		fn_nombretrophees_ldc(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_ldc(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesLDC = $req->fetchAll();

	$ordreSQL =		'	SELECT		fn_nombretrophees_el(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_el(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesEL = $req->fetchAll();

	$ordreSQL =		'	SELECT		fn_nombretrophees_cdf(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_cdf(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesCDF = $req->fetchAll();
	
	$ordreSQL =		'	SELECT		fn_nombretrophees_ca(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_ca(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesCA = $req->fetchAll();

	$ordreSQL =		'	SELECT		fn_nombretrophees_cdm(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_cdm(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesCDM = $req->fetchAll();

	$ordreSQL =		'	SELECT		fn_nombretrophees_euro(' . $pronostiqueurConsulte . ') AS Nombre_Trophees' .
					'				,fn_pointstrophees_euro(' . $pronostiqueurConsulte . ') AS Points_Trophees';
	$req = $bdd->query($ordreSQL);
	$tropheesEuro = $req->fetchAll();

	// Affichage de la photo et du nom du pronostiqueur consulté
	$ordreSQL =		'	SELECT		Pronostiqueurs_NomUtilisateur, IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
					'	FROM		pronostiqueurs' .
					'	WHERE		Pronostiqueur = ' . $pronostiqueurConsulte .
					'	UNION ALL' .
					'	SELECT		Pronostiqueurs_NomUtilisateur, \'_inconnu.png\' AS Pronostiqueurs_Photo' .
					'	FROM		pronostiqueurs_anciens' .
					'	WHERE		Pronostiqueur = ' . $pronostiqueurConsulte;

	$req = $bdd->query($ordreSQL);
	$pronostiqueur = $req->fetchAll();
	
	echo '<div class="colle-gauche gauche pantheon--detail--pronostiqueur">';
		echo '<img src="images/pronostiqueurs/' . $pronostiqueur[0]["Pronostiqueurs_Photo"] . '" alt="" title="" /><br />';
		echo '<label class="pantheon--detail--nom">' . $pronostiqueur[0]["Pronostiqueurs_NomUtilisateur"] . '</label>';
	echo '</div>';
	
	// Les données sont affichées sous forme de tableau
	// Il est donc nécessaire de savoir avant la création du tableau combien de colonnes celui-ci comportera
	// Chaque colonne affiche les trophées obtenus dans une compétition
	// La difficulté est de savoir quelles compétitions afficher pour un pronostiqueur
	$tableauColonnes = array();
	if($tropheesL1[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '1', 'tableau' => $tropheesL1));
	if($tropheesLDC[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '2', 'tableau' => $tropheesLDC));
	if($tropheesEL[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '3', 'tableau' => $tropheesEL));
	if($tropheesCDF[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '4', 'tableau' => $tropheesCDF));
	if($tropheesCDM[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '5', 'tableau' => $tropheesCDM));
	if($tropheesCA[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '6', 'tableau' => $tropheesCA));
	if($tropheesEuro[0]["Nombre_Trophees"] > 0)				array_push($tableauColonnes, array('code' => '7', 'tableau' => $tropheesEuro));
	$nombreColonnes = count($tableauColonnes);
	
	echo '<div class="gauche pantheon--detail--liste-categories">';
		//echo '<span class="pantheon--detail--separateur"></span>';
		echo '<table class="pantheon--detail--tableau-trophees">';
			// Affichage de la ligne d'en-tête
			echo '<thead>';
				echo '<tr>';
					// Parcours des différentes compétitions à afficher
					for($i = 0; $i < $nombreColonnes; $i++) {
						$tableau = $tableauColonnes[$i];
						echo '<th>';
							if($tableau['code'] == 1)					echo 'Ligue 1';
							if($tableau['code'] == 2)					echo 'Ligue des Champions';
							if($tableau['code'] == 3)					echo 'Europa League';
							if($tableau['code'] == 4)					echo 'Coupe de France';
							if($tableau['code'] == 5)					echo 'Coupe du Monde';
							if($tableau['code'] == 6)					echo 'Copa America';
							if($tableau['code'] == 7)					echo 'Euro';
						echo '</th>';
					}
				echo '</tr>';
			echo '</thead>';
		
			echo '<tbody>';
				echo '<tr>';
					// Parcours des différentes compétitions à afficher
					$nombreColonnes = count($tableauColonnes);
					for($i = 0; $i < $nombreColonnes; $i++) {
						$tableau = $tableauColonnes[$i];
						$trophees = $tableau['tableau'];
						echo '<td>';
							if($tableau['code'] == 1)					afficherTropheesL1($bdd, $pronostiqueurConsulte, $trophees);
							if($tableau['code'] == 2)					afficherTropheesLDC($bdd, $pronostiqueurConsulte, $trophees);
							if($tableau['code'] == 3)					afficherTropheesEL($bdd, $pronostiqueurConsulte, $trophees);
							if($tableau['code'] == 4)					afficherTropheesCDF($bdd, $pronostiqueurConsulte, $trophees);
							if($tableau['code'] == 5)					afficherTropheesCDM($bdd, $pronostiqueurConsulte, $trophees);
							if($tableau['code'] == 6)					afficherTropheesCA($bdd, $pronostiqueurConsulte, $trophees);
							if($tableau['code'] == 7)					afficherTropheesEuro($bdd, $pronostiqueurConsulte, $trophees);
						echo '</td>';
					}
				echo '</tr>';
		echo '</table>';
	echo '</div>';
	
	echo '<div class="colle-gauche"></div>';

?>