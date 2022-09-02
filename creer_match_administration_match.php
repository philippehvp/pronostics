<?php
	include_once('commun_administrateur.php');

	// Lecture des paramètres passés à la page
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;

	// Lecture des informations du match
	$ordreSQL =		'	SELECT				vue_matches.Match' .
					'						,EquipeDomicile, EquipesDomicile_Nom' .
					'						,EquipeVisiteur, EquipesVisiteur_Nom' .
					'						,Matches_CoteEquipeDomicile, Matches_CoteNul, Matches_CoteEquipeVisiteur' .
					'						,Matches_Date' .
					'						,HOUR(Matches_Date) AS Matches_Heure' .
					'						,MINUTE(Matches_Date) AS Matches_Minute' .
					'						,Matches_ScoreEquipeDomicile, Matches_ScoreEquipeVisiteur' .
					'						,Matches_ScoreAPEquipeDomicile, Matches_ScoreAPEquipeVisiteur' .
					'						,Matches_Vainqueur' .
					'						,Matches_MatchCS' .
					'						,Matches_Coefficient' .
					'						,Matches_Report' .
					'						,Matches_AvecProlongation' .
					'						,Matches_L1Europe' .
					'						,Matches_L1EuropeNom' .
					'						,Matches_MatchLie' .
					'						,Matches_PointsQualificationEquipeDomicile, Matches_PointsQualificationEquipeVisiteur' .
					'						,IFNULL(Matches_Direct, 0) AS Matches_Direct' .
					'						,Matches_LienPage' .
					'						,Matches_LienPageComplementaire' .
					'						,Matches_MatchIgnore' .
					'						,Matches_MatchHorsPronostic' .
					'	FROM				vue_matches' .
					'	WHERE				vue_matches.Match = ' . $match .
					'	ORDER BY			vue_matches.Match';
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();

	echo '<div class="liste-matches--entete">';
		echo '<label class="bouton" onclick="calculerResultats_calculerResultats(' . $journee . ');">Calculer les scores</label>';

		foreach($matches as $unMatch) {
			$numeroMatch = $unMatch["Match"];
			$matchLie = $unMatch["Matches_MatchLie"];
			$scoreEquipeDomicile = $unMatch["Matches_ScoreEquipeDomicile"] != null ? $unMatch["Matches_ScoreEquipeDomicile"] : -1;
			$scoreAPEquipeDomicile = $unMatch["Matches_ScoreAPEquipeDomicile"] != null ? $unMatch["Matches_ScoreAPEquipeDomicile"] : -1;
			$scoreEquipeVisiteur = $unMatch["Matches_ScoreEquipeVisiteur"] != null ? $unMatch["Matches_ScoreEquipeVisiteur"] : -1;
			$scoreAPEquipeVisiteur = $unMatch["Matches_ScoreAPEquipeVisiteur"] != null ? $unMatch["Matches_ScoreAPEquipeVisiteur"] : -1;
			$matchDirect = $unMatch["Matches_Direct"] != null ? $unMatch["Matches_Direct"] : 0;
			$equipeDomicile = $unMatch["EquipeDomicile"] != null ? $unMatch["EquipeDomicile"] : 0;
			$equipeVisiteur = $unMatch["EquipeVisiteur"] != null ? $unMatch["EquipeVisiteur"] : 0;
			$matchL1Europe = $unMatch["Matches_L1Europe"] != null ? $unMatch["Matches_L1Europe"] : 0;
			$coteEquipeDomicile = $unMatch["Matches_CoteEquipeDomicile"] != null ? $unMatch["Matches_CoteEquipeDomicile"] : 0;
			$coteNul = $unMatch["Matches_CoteNul"] != null ? $unMatch["Matches_CoteNul"] : 0;
			$coteEquipeVisiteur = $unMatch["Matches_CoteEquipeVisiteur"] != null ? $unMatch["Matches_CoteEquipeVisiteur"] : 0;
			$matchesAvecProlongation = $unMatch["Matches_AvecProlongation"] != null ? $unMatch["Matches_AvecProlongation"] : 0;
			$pointsQualificationEquipeDomicile = $unMatch["Matches_PointsQualificationEquipeDomicile"] != null ? $unMatch["Matches_PointsQualificationEquipeDomicile"] : 0;
			$pointsQualificationEquipeVisiteur = $unMatch["Matches_PointsQualificationEquipeVisiteur"] != null ? $unMatch["Matches_PointsQualificationEquipeVisiteur"] : 0;
			$matchVainqueur = $unMatch["Matches_Vainqueur"] != null ? $unMatch["Matches_Vainqueur"] : 0;
			$matchDate = $unMatch["Matches_Date"];
			$matchHeure = $unMatch["Matches_Heure"];
			$matchMinute = $unMatch["Matches_Minute"];
			$matchCS = $unMatch["Matches_MatchCS"] != null ? $unMatch["Matches_MatchCS"] : 0;
			$matchL1EuropeNom = $unMatch["Matches_L1EuropeNom"] != null ? $unMatch["Matches_L1EuropeNom"] : '';
			$matchCoefficient = $unMatch["Matches_Coefficient"] != null ? $unMatch["Matches_Coefficient"] : 1;
			$matchReport = $unMatch["Matches_Report"] != null ? $unMatch["Matches_Report"] : 0;
			$matchLienPage = $unMatch["Matches_LienPage"] != null ? $unMatch["Matches_LienPage"] : '';
			$matchLienPageComplementaire = $unMatch["Matches_LienPageComplementaire"] != null ? $unMatch["Matches_LienPageComplementaire"] : '';
			$matchIgnore = $unMatch["Matches_MatchIgnore"] != null ? $unMatch["Matches_MatchIgnore"] : 0;
			$matchHorsPronostic = $unMatch["Matches_MatchHorsPronostic"] != null ? $unMatch["Matches_MatchHorsPronostic"] : 0;

			echo '<div id="divMatch_match_' . $numeroMatch . '" class="match">';
				include('creer_match_administration_match_detail.php');
			echo '</div>';
		echo '</div>';
	}

?>
