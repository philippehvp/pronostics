<?php
	include_once('commun.php');

	// Affichage des données Match centre d'un championnat

	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;

	// La page est divisée en plusieurs sections
	// Chacune d'elles affiche des informations différentes

	// Lecture de la journée en cours
	$ordreSQL =		'	SELECT		fn_recherchejourneeencours(' . $championnat . ') AS Journee_En_Cours';
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$journee = $journees[0]["Journee_En_Cours"];

	// Il est nécessaire de lire le nombre de pronostiqueurs pour l'échelle horizontale des graphiques
	$ordreSQL =		'	SELECT		COUNT(*) AS Nombre_Pronostiqueurs' .
					'	FROM		inscriptions' .
					'	WHERE		Championnats_Championnat = ' . $championnat;
	$req = $bdd->query($ordreSQL);
	$pronostiqueurs = $req->fetchAll();
	$nombrePronostiqueurs = $pronostiqueurs[0]["Nombre_Pronostiqueurs"];

	// Dans le cas où le championnat affiché ne concerne pas le pronostiqueur connecté, il est nécessaire :
	// - de lire la liste des joueurs du championnat
	// - de mettre le premier de la liste en tant que pronostiqueur consulté
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	inscriptions.Pronostiqueurs_Pronostiqueur IS NULL' .
					'					THEN	CASE' .
					'								WHEN	pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NULL' .
					'								THEN	(' .
					'											SELECT		Pronostiqueurs_Pronostiqueur' .
					'											FROM		inscriptions' .
					'											JOIN		pronostiqueurs' .
					'														ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
					'											WHERE		Championnats_Championnat = ' . $championnat .
					'											ORDER BY	Pronostiqueurs_NomUtilisateur' .
					'											LIMIT 1' .
					'										)' .
					'								ELSE	pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'							END' .
					'					ELSE	' . $_SESSION["pronostiqueur"] .
					'				END AS Pronostiqueur_Consulte' .
					'	FROM		pronostiqueurs' .
					'	LEFT JOIN	(' .
					'					SELECT		Pronostiqueurs_Pronostiqueur' .
					'					FROM		inscriptions' .
					'					WHERE		Championnats_Championnat = ' . $championnat .
					'								AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				) inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		' .$_SESSION["pronostiqueur"] . ' AS Pronostiqueur, PronostiqueursRivaux_Pronostiqueur' .
					'					FROM		pronostiqueurs' .
					'					JOIN		pronostiqueurs_rivaux' .
					'								ON		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'					JOIN		inscriptions' .
					'								ON		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'					WHERE		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'								AND		Championnats_Championnat = ' . $championnat .
					'					ORDER BY	Pronostiqueurs_NomUtilisateur' .
					'					LIMIT		1' .
					'				) pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.Pronostiqueur' .
					'	WHERE		pronostiqueurs.Pronostiqueur = ' . $_SESSION["pronostiqueur"];
	$req = $bdd->query($ordreSQL);
	$pronostiqueursConsultes = $req->fetchAll();
	$pronostiqueurConsulte = $pronostiqueursConsultes[0]["Pronostiqueur_Consulte"];

	echo '<input type="hidden" name="nombrePronostiqueurs" value="' . $nombrePronostiqueurs . '" />';
	echo '<input type="hidden" name="championnat" value="' . $championnat . '" />';
	echo '<input type="hidden" name="journee" value="' . $journee . '" />';
	echo '<input type="hidden" name="pronostiqueurConsulte" value="' . $pronostiqueurConsulte . '" />';


	// Liste des pronostiqueurs pour le championnat
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
					'				,CASE' .
					'					WHEN	pronostiqueurs.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	0' .
					'					WHEN	pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,CASE' .
					'					WHEN	pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN	1' .
					'					ELSE	0' .
					'				END AS Pronostiqueur_Rival' .
					'	FROM		pronostiqueurs' .
					'	LEFT JOIN	(' .
					'					SELECT		PronostiqueursRivaux_Pronostiqueur' .
					'					FROM		pronostiqueurs_rivaux' .
					'					WHERE		Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				) pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'	JOIN		inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		journees' .
					'				ON		inscriptions.Championnats_Championnat = journees.Championnats_Championnat' .
					'						AND		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	WHERE		journees.Journee = ' . $journee .
					'	ORDER BY	Ordre, Pronostiqueurs_NomUtilisateur';
	$req = $bdd->query($ordreSQL);
	$pronostiqueurs = $req->fetchAll();

	// Affichage de la liste des pronostiqueurs du championnat
	// On identifie trois groupes :
	// - le pronostiqueur
	// - les rivaux
	// - les autres pronostiqueurs
	// La difficulté réside dans le fait de détecter le changement de groupe
	// Voici comment cela fonctionne ici :
	// - pour le premier groupe (pronostiqueur connecté), on crée le groupe d'options autour et on initialise une variable avec la valeur vide
	// - à chaque changement de valeur, on ferme et on ouvre un nouveau groupe d'options
	// - on ferme le dernier groupe d'options après la lecture de la dernière ligne de résultat
	echo '<div class="mc--liste-pronostiqueurs">';
		echo '<label>Consulter les résultats de </label>';
		echo '<select id="selectPronostiqueurs" onchange="afficherPronostiqueur(' . $journee . ', \'mc--resultats\', \'mc--classement-general\', \'mc--graphiques\');">';
			$ordrePrecedent = '';
			foreach($pronostiqueurs as $unPronostiqueur) {
				if($unPronostiqueur["Pronostiqueur"] == $_SESSION["pronostiqueur"]) {
					echo '<optgroup label="Moi">';
						echo '<option value="' . $unPronostiqueur["Pronostiqueur"] . '">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</option>';
					echo '</optgroup>';
				}
				else if($unPronostiqueur["Pronostiqueur_Rival"] == 1) {
					if($ordrePrecedent == '')
						echo '<optgroup label="Rivaux">';
					echo '<option value="' . $unPronostiqueur["Pronostiqueur"] . '">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</option>';

					$ordrePrecedent = '1';
				}
				else {
					if($ordrePrecedent == '')
						echo '<optgroup label="Autres joueurs">';
					else if($ordrePrecedent == '1') {
						echo '</optgroup>';
						echo '<optgroup label="Autres joueurs">';
					}
					echo '<option value="' . $unPronostiqueur["Pronostiqueur"] . '">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</option>';
					$ordrePrecedent = '2';
				}
			}
			if($ordrePrecedent != '')
				echo '</optgroup>';
		echo '</select>';
	echo '</div>';


	// La zone d'affichage des données est divisée en 2 parties, l'une au-dessus de l'autre
	echo '<div class="colle-gauche">';
		echo '<div class="mc--resultats scroll-pane gauche">';
			// Section résultats et pronostics
			include_once('match_centre_affichage_journee.php');
		echo '</div>';

		echo '<div class="mc--detail-match scroll-pane droite"></div>';
	echo '</div>';

	// La partie du dessous est divisée en deux parties : à gauche les classements, à droite les graphiques
	echo '<div class="colle-gauche gauche mc--conteneur-section" style="margin-top: 10px;">';
		echo '<div class="colle-gauche gauche">';
			// Classement général
			echo '<label class="colle-gauche mc--titre-section">Classement général</label>';
			echo '<div class="mc--classement-general scroll-pane">';
				include_once('match_centre_affichage_classement_general.php');
			echo '</div>';


			// Lecture du nom de la journée
			$ordreSQL =		'	SELECT		Journees_Nom, Journees_DateMAJ, Journees_DateEvenement FROM journees WHERE Journee = ' . $journee;
			$req = $bdd->query($ordreSQL);
			$journees = $req->fetchAll();
			$nomJournee = $journees[0]["Journees_Nom"];
			$journeeDateMAJ = $journees[0]["Journees_DateMAJ"];
			$journeeEvenement = $journees[0]["Journees_DateEvenement"];

			echo '<label class="colle-gauche mc--titre-section">' . $nomJournee . '</label>';
			echo '<div class="mc--classement-journee scroll-pane">';
				include_once('match_centre_affichage_classement_journee.php');
			echo '</div>';
		echo '</div>';

	echo '<div class="gauche" style="margin-left: 20px;">';
		// Section graphique général et journée
		echo '<svg class="mc--graphiques"></svg>';

		// Affichage des statistiques de la journée
		echo '<div class="mc--statistiques-journee">';
			include_once('match_centre_affichage_statistiques_journee.php');
		echo '</div>';
	echo '</div>';

?>

<script>
	$(function() {
		//$('.scroll-pane').getNiceScroll().resize();
		$('.scroll-pane').niceScroll({cursorcolor: "#0e2c3d", cursorborder: "#0e2c3d"});
	});

	function afficherPronostiqueur(journee, classeResultats, classeClassementGeneral, classeGraphiques) {
		var pronostiqueurConsulte = $('#selectPronostiqueurs').val();
		matchCentre_afficherPronostiqueur(journee, pronostiqueurConsulte, classeResultats, classeClassementGeneral, classeGraphiques);
	}

</script>