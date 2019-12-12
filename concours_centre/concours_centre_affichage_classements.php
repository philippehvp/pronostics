<?php
	include_once('../commun.php');

	// Affichage des classements comparés pour un championnat

	// Lecture des paramètres passés à la page
	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;
	$zoneDessinLargeur = isset($_POST["zoneDessinLargeur"]) ? $_POST["zoneDessinLargeur"] : 0;
	$zoneDessinHauteur = isset($_POST["zoneDessinHauteur"]) ? $_POST["zoneDessinHauteur"] : 0;
	$generalJournee = isset($_POST["generalJournee"]) ? $_POST["generalJournee"] : 1;

	// Liste des pronostiqueurs pour le championnat en question
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
					'				,CASE' .
					'					WHEN		pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur IS NOT NULL' .
					'					THEN		1' .
					'					ELSE		0' .
					'				END AS Pronostiqueurs_Rival' .
					'	FROM		pronostiqueurs' .
					'	JOIN		inscriptions' .
					'				ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs_rivaux' .
					'				ON		pronostiqueurs_rivaux.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'						AND		pronostiqueurs.Pronostiqueur = pronostiqueurs_rivaux.PronostiqueursRivaux_Pronostiqueur' .
					'	WHERE		inscriptions.Championnats_Championnat = ' . $championnat .
					'				AND		inscriptions.Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
					'	ORDER BY	Pronostiqueurs_NomUtilisateur';
	$req = $bdd->query($ordreSQL);
	$pronostiqueurs = $req->fetchAll();

	// Affichage des pronostiqueurs
	$NOMBRE_COLONNES = 10;

	$nombrePronostiqueurs = sizeof($pronostiqueurs);
	$nombrePronostiqueursParColonne = ceil($nombrePronostiqueurs / $NOMBRE_COLONNES);

	echo '<div>';
		if($nombrePronostiqueurs) {
			echo '<div>';
				// L'affichage des pronostiqueurs se fait sur plusieurs colonnes
				for($i = 0; $i < $NOMBRE_COLONNES; $i++) {
					echo '<div class="gauche">';
						for($j = 0; $j < $nombrePronostiqueursParColonne && $i * $nombrePronostiqueursParColonne + $j < $nombrePronostiqueurs; $j++) {
							if($pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueurs_Rival"] == 1)
								echo '<label class="cc--pronostiqueur cc--nom-pronostiqueur texte-gras curseur-main" onclick="concoursCentre_comparerClassementsPronostiqueur(' . $championnat . ', \'cc--classements-graphique\', \'cc--classements-graphique-secondaire\', ' . $pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueur"] . ', ' . ($nombrePronostiqueurs + 1) . ', ' . $generalJournee . ');">' . $pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueurs_NomUtilisateur"] . '</label><br />';
							else
								echo '<label class="cc--pronostiqueur cc--nom-pronostiqueur curseur-main" onclick="concoursCentre_comparerClassementsPronostiqueur(' . $championnat . ', \'cc--classements-graphique\', \'cc--classements-graphique-secondaire\', ' . $pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueur"] . ', ' . ($nombrePronostiqueurs + 1) . ', ' . $generalJournee . ');">' . $pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueurs_NomUtilisateur"] . '</label><br />';
						}
					echo '</div>';
				}
				echo '<div class="colle-gauche"></div>';
			echo '</div>';
		}
	echo '</div>';

	// Création du graphique du pronostiqueur connecté
	echo '<div class="cc--classements-graphique">';
		// Meilleur et plus mauvais classements
		if($generalJournee == 1)
			$ordreSQL =		'	SELECT		MAX(Classements_ClassementGeneralMatch) AS Classement_Max' .
							'				,MIN(Classements_ClassementGeneralMatch) AS Classement_Min' .
							'	FROM		classements' .
							'	JOIN		journees' .
							'				ON		classements.Journees_Journee = journees.Journee' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'				AND		journees.Championnats_Championnat = ' . $championnat .
							'				AND		Classements_ClassementGeneralMatch IS NOT NULL';
		else
			$ordreSQL =		'	SELECT		MAX(Classements_ClassementJourneeMatch) AS Classement_Max' .
							'				,MIN(Classements_ClassementJourneeMatch) AS Classement_Min' .
							'	FROM		classements' .
							'	JOIN		journees' .
							'				ON		classements.Journees_Journee = journees.Journee' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'				AND		journees.Championnats_Championnat = ' . $championnat .
							'				AND		Classements_ClassementJourneeMatch IS NOT NULL';

		$req = $bdd->query($ordreSQL);
		$classementsMinEtMax = $req->fetchAll();
		$classementMin = $classementsMinEtMax[0]["Classement_Min"];
		$classementMax = $classementsMinEtMax[0]["Classement_Max"];

		// Classements occupés
		if($generalJournee == 1)
			$ordreSQL =		'	SELECT		Classements_ClassementGeneralMatch AS Valeur' .
							'	FROM		(' .
							'					SELECT		classements.Journees_Journee, classements.Classements_ClassementGeneralMatch, classements.Pronostiqueurs_Pronostiqueur' .
							'					FROM		classements' .
							'					JOIN		(' .
							'									SELECT		Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
							'									FROM		classements' .
							'									GROUP BY	Journees_Journee' .
							'								) classements_max' .
							'								ON		classements.Journees_Journee = classements_max.Journees_Journee' .
							'										AND		classements.Classements_DateReference = classements_max.Classements_DateReference' .
							'				) classements' .
							'	JOIN		journees' .
							'				ON		classements.Journees_Journee = journees.Journee' .
							'	WHERE		journees.Championnats_Championnat = ' . $championnat .
							'				AND		classements.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'	ORDER BY	classements.Journees_Journee';
		else
			$ordreSQL =		'	SELECT		Classements_ClassementJourneeMatch AS Valeur' .
							'	FROM		(' .
							'					SELECT		classements.Journees_Journee, classements.Classements_ClassementJourneeMatch, classements.Pronostiqueurs_Pronostiqueur' .
							'					FROM		classements' .
							'					JOIN		(' .
							'									SELECT		Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
							'									FROM		classements' .
							'									GROUP BY	Journees_Journee' .
							'								) classements_max' .
							'								ON		classements.Journees_Journee = classements_max.Journees_Journee' .
							'										AND		classements.Classements_DateReference = classements_max.Classements_DateReference' .
							'				) classements' .
							'	JOIN		journees' .
							'				ON		classements.Journees_Journee = journees.Journee' .
							'	WHERE		journees.Championnats_Championnat = ' . $championnat .
							'				AND		classements.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'	ORDER BY	classements.Journees_Journee';


		$req = $bdd->query($ordreSQL);
		$classements = $req->fetchAll();
		$nombrePoints = sizeof($classements);

		$nomFichier = '';
		if($generalJournee == 1) {
			$dossierImages = '../images/classements/general/';
			$dossierImagesHTML = 'images/classements/general/';
		} else {
			$dossierImages = '../images/classements/journee/';
			$dossierImagesHTML = 'images/classements/journee/';
		}

		// Effacement d'images qui pourraient exister dans ce dossier pour ce pronostiqueur
		foreach(glob($dossierImagesHTML . $championnat . '/_' . $_SESSION["pronostiqueur"] . '_*.png') as $f) {
			unlink($f);
		}

		include('concours_centre_affichage_classements_creation_graphique_principal.php');
		echo '<img src="' . $nomFichierHTML . '" alt="" />';
	echo '</div>';
	echo '<div class="cc--classements-graphique-secondaire" style="position: absolute;"></div>';

?>

<script>
	$(function() {
		// Gestion du clic sur un sous-onglet pour que celui-ci apparaisse avec un style de surbrillance / sélection
		$('.cc--nom-sous-onglet').click(function (e) {
			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('cc--selectionne')) {
				$('.cc--nom-sous-onglet').removeClass('cc--selectionne');
				$(this).addClass('cc--selectionne');
			}
		});

		// Gestion du clic sur une pronostiqueur pour que celle-ci apparaisse avec un style de surbrillance / sélection
		$('.cc--pronostiqueur').click(function (e) {
			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('cc--selectionnee')) {
				$('.cc--pronostiqueur').removeClass('cc--selectionnee');
				$(this).addClass('cc--selectionnee');
			}
		});

	});

</script>