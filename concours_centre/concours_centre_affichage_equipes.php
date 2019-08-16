<?php
	include_once('../commun.php');
	
	// Affichage des équipes d'une catégorie :
	// - 1 : équipes de L1 pure
	// - 2 : équipes de L1 du match 11
	// - 3 : équipes de LDC
	// - 4 : équipes d'EL
	
	// Certaines équipes peuvent bien entendues être dans plusieurs catégories
	
	// Affichage des pronostiqueurs du concours
	// Lecture des paramètres passés à la page
	$largeur = isset($_POST["largeur"]) ? $_POST["largeur"] : 0;
	$typeEquipe = isset($_POST["typeEquipe"]) ? $_POST["typeEquipe"] : 0;

	
	// Equipes de L1 pure
	function afficherEquipesL1() {
		return	'	SELECT		Equipe, Equipes_Nom, Equipes_NomCourt, Equipes_Fanion' .
				'	FROM		equipes' .
				'	JOIN		engagements' .
				'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
				'	WHERE		engagements.Championnats_Championnat = 1' .
				'				AND		equipes.Equipes_L1Europe IS NULL' .
				'	ORDER BY	equipes.Equipes_NomCourt';
	}
	
	// Equipes de L1 étendue
	function afficherEquipesL1Etendue() {
		return	'	SELECT		Equipe, Equipes_Nom, Equipes_NomCourt, Equipes_Fanion' .
				'	FROM		equipes' .
				'	JOIN		engagements' .
				'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
				'	WHERE		engagements.Championnats_Championnat = 1' .
				'				AND		equipes.Equipes_L1Europe = 1' .
				'	ORDER BY	equipes.Equipes_NomCourt';
	}
	
	// Equipes de LDC
	function afficherEquipesLDC() {
		return	'	SELECT		Equipe, Equipes_Nom, Equipes_NomCourt, Equipes_Fanion' .
				'	FROM		equipes' .
				'	JOIN		engagements' .
				'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
				'	WHERE		engagements.Championnats_Championnat = 2' .
				'	ORDER BY	equipes.Equipes_NomCourt';
	}
	
	// Equipes d'EL
	function afficherEquipesEL() {
		return	'	SELECT		Equipe, Equipes_Nom, Equipes_NomCourt, Equipes_Fanion' .
				'	FROM		equipes' .
				'	JOIN		engagements' .
				'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
				'	WHERE		engagements.Championnats_Championnat = 3' .
				'	ORDER BY	equipes.Equipes_NomCourt';
	}	
	
	switch($typeEquipe) {
		case 1:			$ordreSQL = afficherEquipesL1();			break;
		case 2:			$ordreSQL = afficherEquipesL1Etendue();		break;
		case 3:			$ordreSQL = afficherEquipesLDC();			break;
		case 4:			$ordreSQL = afficherEquipesEL();			break;
	}

	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();
	$nombreEquipes = sizeof($equipes);
	
	$NOMBRE_EQUIPES_PAR_LIGNE = 10;
	
	$largeurCellule = floor($largeur / $NOMBRE_EQUIPES_PAR_LIGNE) - 6;		// Le -4 correspond au margin (2 à gauche et 2 à droite)
	$largeurPhoto = floor($largeurCellule / 1.8);
	
	echo '<div onclick="concoursCentre_monterListeVignettes(\'cc--equipes\');" class="colle-gauche gauche aligne-centre" style="width: 100%; margin: 5px 0;"><img src="images/concours_centre_fleche_haut.png" alt="" /></div>';

	echo '<div class="cc--equipes colle-gauche">';
	
		$indice = 0;
		foreach($equipes as $uneEquipe) {
		
			// Retour à la ligne toutes les n équipes
			if($indice != 0 && $indice % $NOMBRE_EQUIPES_PAR_LIGNE == 0) {
				echo '<br />';
			}
			echo '<div class="cc--vignette gauche" style="width: ' . $largeurCellule . 'px" onclick="concoursCentre_afficherEquipeDetail(' . $uneEquipe["Equipe"] . ', ' . $typeEquipe . ', \'cc--equipes-detail\');">';
				echo '<label class="cc--vignette--nom">' . $uneEquipe["Equipes_NomCourt"] . '</label><br />';
				echo '<img src="images/equipes/' . $uneEquipe["Equipes_Fanion"] . '" alt="" width="' . $largeurPhoto . 'px" />';
			echo '</div>';
			
			$indice++;
		}
	echo '</div>';
	
	echo '<div onclick="concoursCentre_descendreListeVignettes(\'cc--equipes\');" class="aligne-centre" style="width: 100%;"><img src="images/concours_centre_fleche_bas.png" alt="" /></div>';
	
	echo '<hr style="margin: 10px 0;" />';
	
	echo '<div class="cc--equipes-detail"></div>';

?>

<script>
	$(function() {
		// Gestion du clic sur une vignette pour que celle-ci apparaisse avec un style de surbrillance / sélection
		$('.cc--vignette').click(function (e) {
			// Si cet onglet n'était pas sélectionné, alors effectuer deux tâches :
			// - enlever le style sélectionné à l'ancien onglet s'il existe
			// - mettre le style à l'onglet cliqué
			if(!$(this).hasClass('cc--selectionnee')) {
				$('.cc--vignette').removeClass('cc--selectionnee');
				$(this).addClass('cc--selectionnee');
			}
		});
	});
</script>

