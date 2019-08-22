<?php
	include_once('commun_administrateur.php');

	// Lecture des matches de la journée dont le numéro a été passé en paramètre
	if(isset($_POST["journee"])) {
		include_once('commun.php');
		$journee = $_POST["journee"];
	}

	// Spécificité de la ligue 1 - les 10 premiers matches sont joués par des équipes de ligue 1 pure
	// Le match 11 se joue avec des équipes identifiées comme appartenant à la ligue 1 mais jouant dans hors ligue 1 (match européen)

	// Lecture du nom des équipes
	$ordreSQL =		'	SELECT		Equipe, Equipes_Nom'.
					'				,CASE' .
					'					WHEN	championnats.Championnats_Regle = 1' .
					'					THEN	Equipes_L1Europe' .
					'					ELSE	NULL' .
					'				END AS Equipes_L1Europe' .
					'				,Championnats_Regle' .
					'	FROM		equipes' .
					'	JOIN		engagements' .
					'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
					'	JOIN		journees' .
					'				ON		journees.Championnats_Championnat = engagements.Championnats_Championnat' .
					'	JOIN		championnats' .
					'				ON		journees.Championnats_Championnat = championnats.Championnat' .
					'	WHERE		journees.Journee = ' . $journee .
					'	ORDER BY	Equipes_Nom';

	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();

	// On détermine si la journée en cours est active ou non pour l'afficher
	$ordreSQL =		'	SELECT			Journees_Active, Championnats_Championnat, Journees_LienPage, Journees_MatchCanalSelectionnable' .
					'	FROM			journees' .
					'	WHERE			Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$active = $journees[0]["Journees_Active"];
	if($active == null)
		$active = 0;
	$championnat = $journees[0]["Championnats_Championnat"];
	if($championnat == null)
		$championnat = 0;
	$matchCanalSelectionnable = $journees[0]["Journees_MatchCanalSelectionnable"];

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
					'	WHERE				Journees_Journee = ' . $journee .
					'	ORDER BY			vue_matches.Match';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();

	echo '<div class="liste-matches--entete">';
		// echo '<label class="detail">Lire la journée</label>';
		// echo '<label class="bouton" onclick="creerMatch_lireLiensMatches(' . $journee . ');">Page des matches MeD</label> - <label class="bouton" onclick="creerMatch_lireLiensMatchesScoresPro(' . $journee . ');">Page des matches SP</label>';
		// echo '<br />';

		echo '<label class="detail">Remplir la journée</label>';
		echo '<input type="text" class="lien-page" id="lien_journee_' . $journee . '" value="' . $journees[0]["Journees_LienPage"] . '" onchange="creerMatch_sauvegarderJournee(' . $journee . ');" />';
		echo '<br />';

		echo '<label class="detail">Commencer par</label>';
		echo '<select id="selectMatch">';
			foreach($matches as $unMatch) {
				if($unMatch["Matches_L1Europe"] == 0) {
					echo '<option value="' . $unMatch["Match"] . '">' . $unMatch["Match"] . '</option>';
				}
			}
		echo '</select>';

		echo '<label class="bouton" onclick="creerMatch_remplirMatches(' . $journee . ');">Remplir</label>';
		echo '<br />';

		echo '<input type="hidden" id="matchCanalSelectionnable" value="' . $matchCanalSelectionnable . '">';
		// if($matchCanalSelectionnable == 1) {
		// 	echo '<label class="detail">Initialiser match Canal</label>';
		// 	echo '<label class="bouton" onclick="creerMatch_initialiserMatchCanal(' . $journee . ');">Initialiser match Canal</label>';
		// 	echo '<br />';
		// }

		if($active == 1) {
			// La journée est active
			echo '<label class="detail vert" id="labelEtatJournee">Journée active</label>';
			echo '<label class="bouton" id="labelActiverDesactiverJournee">Désactiver la journée</label>';
		} else {
			// La journée est inactive
			echo '<label class="detail rouge" id="labelEtatJournee">Journée inactive</label>';
			if($matchCanalSelectionnable == 1) {
				echo '<label class="bouton" id="labelActiverDesactiverJournee">Activer la journée et initialiser match Canal</label>';
			} else {
				echo '<label class="bouton" id="labelActiverDesactiverJournee">Activer la journée</label>';
			}
		}

		echo '<br />';
		echo '<label class="detail">Lancement des calculs</label><label class="bouton" onclick="calculerResultats_calculerResultats();">Calculer les scores</label>';

		// Spécificité de la Coupe de France : il faut afficher un bouton qui permette de déplacer les vainqueurs des confrontations vers le tour suivant
		if($championnat == 5)
			echo ' - <label class="bouton" onclick="calculerResultats_finaliserConfrontations();">Finaliser la journée de Coupe de France</label>';

		echo '<br />';
		echo '<label class="detail">Page des trophées</label><label class="bouton" onclick="creerMatch_afficherTrophees(' . $championnat .');">Afficher la page des trophées</label>';

		echo '<br />';
		echo '<label class="detail">Compte-rendu</label>';
		echo '<label class="bouton" onclick="creerMatch_genererCR(' . $journee . ');">Générer le compte-rendu</label>&nbsp;';
		echo '<label class="bouton" onclick="consulterCanal(' . $journee . ');">Consulter le match Canal</label>';

	echo '</div>';


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
			include('creer_match_affichage_match.php');

		echo '</div>';

	}

?>
<script>
	$(function() {
		$('#labelActiverDesactiverJournee').click(function(event) {
			var matchCanalSelectionnable = $('#matchCanalSelectionnable').val();

			if($('#labelEtatJournee').hasClass('vert')) {
				// La journée était active, on la désactive
				creerMatch_activerDesactiverJournee(0, 0);
				$('#labelEtatJournee').removeClass('vert');
				$('#labelEtatJournee').addClass('rouge');

				if(matchCanalSelectionnable)
					$('#labelActiverDesactiverJournee').html('Activer la journée et initialiser match Canal');
				else
				$('#labelActiverDesactiverJournee').html('Activer la journée');
			} else {
				// La journée était inactive, on l'active
				creerMatch_activerDesactiverJournee(1, matchCanalSelectionnable);
				$('#labelEtatJournee').removeClass('rouge');
				$('#labelEtatJournee').addClass('vert');
				$('#labelActiverDesactiverJournee').html('Désactiver la journée');
			}
		});

		// Changement de l'adresse URL de la page pour qu'elle reflète le numéro de journée
		var stateObj = { foo: 'bar' };
		history.pushState(stateObj, "Le Poulpe d'Or", "creer_match.php?journee=" + $('#selectJournee').val());

	});

	function consulterCanal(numeroJournee) {
		// On affiche un message de confirmation si la journée est active
		if($('#labelEtatJournee').hasClass('vert')) {
			if(confirm('La journée est active, voir les matchs Canal ?')) {
				creerMatch_consulterCanal(numeroJournee);	
			}
		} else {
			creerMatch_consulterCanal(numeroJournee);
		}
	}
</script>
