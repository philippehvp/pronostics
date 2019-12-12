<?php
	include_once('../commun.php');

	// Affichage des pronostiqueurs du concours
	// Lecture des paramètres passés à la page
	$largeur = isset($_POST["largeur"]) ? $_POST["largeur"] : 0;

	// L'indication du sous-onglet permet de différencier deux cas :
	// - ouverture habituelle par le CC (sous-onglet actif = 1)
	// - ouverture par le tableau des classements (sous-onglet actif = 2)
	$sousOnglet = isset($_POST["sousOnglet"]) ? $_POST["sousOnglet"] : 1;

	// La remarque est la même pour le pronostiqueur sélectionné par défaut :
	// - ouverture habituelle par le CC (pronostiqueur connecté)
	// - ouverture par le tableau des classements (pronostiqueur passé en paramètre)
	$pronostiqueurConsulte = isset($_POST["pronostiqueurConsulte"]) ? $_POST["pronostiqueurConsulte"] : $_SESSION["pronostiqueur"];

	// Le calcul de la largeur de chaque image dépend du nombre de pronostiqueurs et de la largeur de la zone de contenu

	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
					'	FROM		pronostiqueurs' .
					'	ORDER BY	Pronostiqueurs_NomUtilisateur';
	$req = $bdd->query($ordreSQL);
	$pronostiqueurs = $req->fetchAll();
	$nombrePronostiqueurs = sizeof($pronostiqueurs);

	$NOMBRE_PRONOSTIQUEURS_PAR_LIGNE = 10;

	$largeurCellule = floor($largeur / $NOMBRE_PRONOSTIQUEURS_PAR_LIGNE) - 6;		// Le -6 correspond au margin (3 à gauche et 3 à droite)
	$largeurPhoto = floor($largeurCellule / 1.8);

	echo '<div onclick="concoursCentre_monterListeVignettes(\'cc--pronostiqueurs\');" class="aligne-centre" style="width: 100%; margin-bottom: 5px;"><img src="images/concours_centre_fleche_haut.png" alt="" /></div>';

	echo '<div class="cc--pronostiqueurs">';

		$indice = 0;
		foreach($pronostiqueurs as $unPronostiqueur) {

			// Retour à la ligne tous les n pronostiqueurs
			if($indice != 0 && $indice % $NOMBRE_PRONOSTIQUEURS_PAR_LIGNE == 0) {
				echo '<br />';
			}

			if($unPronostiqueur["Pronostiqueur"] == $pronostiqueurConsulte)
				echo '<div class="cc--vignette cc--selectionnee gauche" style="width: ' . $largeurCellule . 'px" onclick="concoursCentre_afficherPronostiqueurEntete(' . $unPronostiqueur["Pronostiqueur"] . ', \'cc--pronostiqueurs-entete\', 0); concoursCentre_afficherPronostiqueurDetail(' . $unPronostiqueur["Pronostiqueur"] . ', \'cc--pronostiqueurs-detail\', 0);">';
			else
				echo '<div class="cc--vignette gauche" style="width: ' . $largeurCellule . 'px" onclick="concoursCentre_afficherPronostiqueurEntete(' . $unPronostiqueur["Pronostiqueur"] . ', \'cc--pronostiqueurs-entete\', 0); concoursCentre_afficherPronostiqueurDetail(' . $unPronostiqueur["Pronostiqueur"] . ', \'cc--pronostiqueurs-detail\', 0);">';
				echo '<label class="cc--vignette--nom">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</label><br />';
				echo '<img class="bordure-grise" src="images/pronostiqueurs/' . $unPronostiqueur["Pronostiqueurs_Photo"] . '" alt="" width="' . $largeurPhoto . 'px" />';
			echo '</div>';

			$indice++;
		}
	echo '</div>';

	echo '<div onclick="concoursCentre_descendreListeVignettes(\'cc--pronostiqueurs\');" class="aligne-centre" style="width: 100%;"><img src="images/concours_centre_fleche_bas.png" alt="" /></div>';

	echo '<hr style="margin: 10px 0;" />';

	echo '<div class="colle-gauche gauche cc--sous-onglets">';
		if($sousOnglet == 1)
			echo '<label class="cc--nom-sous-onglet cc--selectionne" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 1);">FICHE D\'IDENTITE</label>';
		else
			echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 1);">FICHE D\'IDENTITE</label>';

		if($sousOnglet == 2)
			echo '<label class="cc--nom-sous-onglet cc--selectionne" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 2);">PALMARES</label>';
		else
			echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 2);">PALMARES</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 3);">STATISTIQUES</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 4);">CLASSEMENT GENERAL</label>';
		echo '<label class="cc--nom-sous-onglet" onclick="concoursCentre_afficherPronostiqueurDetail(0, \'cc--pronostiqueurs-detail\', 5);">CLASSEMENT JOURNEE</label>';
	echo '</div>';

	echo '<div class="cc--pronostiqueurs-entete"></div>';
	echo '<div class="cc--pronostiqueurs-detail"></div>';

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