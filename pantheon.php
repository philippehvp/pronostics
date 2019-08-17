<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';
?>

</head>

<body class="pnthn">
<?php
	// Panthéon du Poulpe d'Or
	$nomPage = 'pantheon.php';
	echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

	echo '<div class="conteneur">';
		include_once('bandeau.php');
		
		// Lecture des données de la table des points du panthéon des pronostiqueurs du top 3 (actuels et anciens)
		// Attention, compte tenu des égalités, le fait de ramener les joueurs ayant un classement inférieur ou égal à 3 peut ramener plus de 3 joueurs
		$ordreSQL =		'	SELECT		classements_pantheon.Pronostiqueurs_Pronostiqueur' .
						'				,CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur IS NOT NULL' .
						'					THEN	IFNULL(pronostiqueurs.Pronostiqueurs_Photo, \'_inconnu.png\')' .
						'					ELSE	\'_inconnu.png\'' .
						'				END AS Pronostiqueurs_Photo' .
						'				,CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur IS NOT NULL' .
						'					THEN	pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
						'					ELSE	pronostiqueurs_anciens.Pronostiqueurs_NomUtilisateur' .
						'				END AS Pronostiqueurs_NomUtilisateur' .
						'				,CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur IS NOT NULL' .
						'					THEN	1' .
						'					ELSE	0' .
						'				END AS Pronostiqueurs_Actuel' .
						'				,Classements_Classement' .
						'				,Classements_Points' .
						'	FROM		classements_pantheon' .
						'	LEFT JOIN	pronostiqueurs' .
						'				ON		classements_pantheon.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	LEFT JOIN	pronostiqueurs_anciens' .
						'				ON		classements_pantheon.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
						'	WHERE		Classements_Classement <= 3' .
						'	ORDER BY	Classements_Classement, classements_pantheon.Pronostiqueurs_Pronostiqueur';
		$req = $bdd->query($ordreSQL);
		$pronostiqueursTop3 = $req->fetchAll();
		
		// Lecture des données de la table des points du panthéon des pronostiqueurs hors top 3 (actuels et anciens)
		$ordreSQL =		'	SELECT		classements_pantheon.Pronostiqueurs_Pronostiqueur' .
						'				,CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur IS NOT NULL' .
						'					THEN	IFNULL(pronostiqueurs.Pronostiqueurs_Photo, \'_inconnu.png\')' .
						'					ELSE	\'_inconnu.png\'' .
						'				END AS Pronostiqueurs_Photo' .
						'				,CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur IS NOT NULL' .
						'					THEN	pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
						'					ELSE	pronostiqueurs_anciens.Pronostiqueurs_NomUtilisateur' .
						'				END AS Pronostiqueurs_NomUtilisateur' .
						'				,CASE' .
						'					WHEN	pronostiqueurs.Pronostiqueur IS NOT NULL' .
						'					THEN	1' .
						'					ELSE	0' .
						'				END AS Pronostiqueurs_Actuel' .
						'				,Classements_Classement' .
						'				,Classements_Points' .
						'	FROM		classements_pantheon' .
						'	LEFT JOIN	pronostiqueurs' .
						'				ON		classements_pantheon.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	LEFT JOIN	pronostiqueurs_anciens' .
						'				ON		classements_pantheon.Pronostiqueurs_Pronostiqueur = pronostiqueurs_anciens.Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueurs_Pronostiqueur' .
						'					FROM		classements_pantheon' .
						'					WHERE		Classements_Classement <= 3' .
						'				) top_3' .
						'				ON		classements_pantheon.Pronostiqueurs_Pronostiqueur = top_3.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		top_3.Pronostiqueurs_Pronostiqueur IS NULL' .
						'	ORDER BY	Classements_Classement, classements_pantheon.Pronostiqueurs_Pronostiqueur';

		$req = $bdd->query($ordreSQL);
		$pronostiqueurs = $req->fetchAll();
		$nombrePronostiqueurs = count($pronostiqueurs);
		$NOMBRE_PRONOSTIQUEURS_PAR_COLONNE = 17;

		$classementAffiche = $classementPrecedent = '';
		echo '<div class="pantheon">';
			// Affichage du top 3
			echo '<div class="pantheon--top3">';
				foreach($pronostiqueursTop3 as $unPronostiqueur) {
					echo '<div class="pantheon--top3--pronostiqueur pantheon--top3--pronostiqueur' . $unPronostiqueur["Classements_Classement"] . '" onclick="pantheon_afficherPronostiqueur(' . $unPronostiqueur["Pronostiqueurs_Pronostiqueur"] . ', \'pantheon--detail\');">';
						echo '<div class="pantheon--top3--pronostiqueur--entete pantheon--top3--pronostiqueur--entete' . $unPronostiqueur["Classements_Classement"] . '">';
							echo '<label>' .  $unPronostiqueur["Classements_Classement"] . '<sup>e</sup></label>';
						echo '</div>';
						echo '<img src="images/pronostiqueurs/' . $unPronostiqueur["Pronostiqueurs_Photo"] . '" alt="" title="" />';
						echo '<span style="display: inline-block; vertical-align: top; margin-top: 10px;">';
							echo '<label class="pantheon--top3--pronostiqueur--nom">' . $unPronostiqueur["Pronostiqueurs_NomUtilisateur"] . '</label><br />';
							echo '<label class="pantheon--top3--pronostiqueur--score">' . $unPronostiqueur["Classements_Points"] . ' points</label>';
						echo '</span>';
					echo '</div>';
				}
			echo '</div>';
			
			echo '<div class="colle-gauche"></div>';
		
			// Calcul du nombre de colonnes qui seront occupées pour afficher l'ensemble des pronostiqueurs actuels et anciens)
			// On n'exclut de cette liste le top 3
			$nombreColonnes = ($nombrePronostiqueurs % $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE > 0) ? (floor($nombrePronostiqueurs / $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE + 1)) : (floor($nombrePronostiqueurs / $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE));
			for($i = 0; $i < $nombreColonnes; $i++) {
				
				if($i == 0)					echo '<div class="colle-gauche gauche pantheon--liste">';
				else						echo '<div class="gauche pantheon--liste">';

					for($j = 0; $i * $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE + $j < $nombrePronostiqueurs && $j < $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE; $j++) {
						// Pour la dernière ligne de la colonne, ne pas mettre de bordure inférieure
						// Idem pour le tout dernier pronostiqueur
						$indice = $i * $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE + $j;



						// On affiche une en-tête tout en haut de chaque colonne
						if($j == 0) {
							if($i == 0)					echo '<div class="pantheon--liste--titre">';
							else						echo '<div class="pantheon--liste--titre">';
								echo '<span class="pantheon--liste--photo"></span>';

								echo '<span class="pantheon--liste--titre--classement">';
									echo 'Rang';
								echo '</span>';
								echo '<span class="pantheon--liste--nom"></span>';
								echo '<span class="pantheon--liste--titre--score">';
									echo 'Score';
								echo '</span>';
							echo '</div>';
						}

						if($j == $NOMBRE_PRONOSTIQUEURS_PAR_COLONNE - 1 || $indice == $nombrePronostiqueurs - 1)		$classe = '';
						else																							$classe = 'pantheon--liste--bordure-basse';

						if($i == 0)					echo '<div id="pronostiqueur_' . $pronostiqueurs[$indice]["Pronostiqueurs_Pronostiqueur"] . '" class="pantheon--liste--donnees" onclick="selectionnerPronostiqueur(this); pantheon_afficherPronostiqueur(' . $pronostiqueurs[$indice]["Pronostiqueurs_Pronostiqueur"] . ', \'pantheon--detail\');">';
						else						echo '<div id="pronostiqueur_' . $pronostiqueurs[$indice]["Pronostiqueurs_Pronostiqueur"] . '" class="pantheon--liste--donnees pantheon--liste--bordure-gauche" onclick="selectionnerPronostiqueur(this); pantheon_afficherPronostiqueur(' . $pronostiqueurs[$indice]["Pronostiqueurs_Pronostiqueur"] . ', \'pantheon--detail\');">';

							echo '<span class="pantheon--liste--photo ' . $classe . '">';
								echo '<img src="images/pronostiqueurs/' . $pronostiqueurs[$indice]["Pronostiqueurs_Photo"] . '" alt="" title="" />';
							echo '</span>';

							echo '<span class="pantheon--liste--donnees--classement ' . $classe . '">';
								if($classementPrecedent != $pronostiqueurs[$indice]["Classements_Classement"]) {
									$classementPrecedent = $classementAffiche = $pronostiqueurs[$indice]["Classements_Classement"];
								}
								else																					$classementAffiche = '-';
								echo $classementAffiche;
							echo '</span>';
							echo '<span class="pantheon--liste--nom survol ' . $classe . '">';
								echo $pronostiqueurs[$indice]["Pronostiqueurs_NomUtilisateur"];
							echo '</span>';
							echo '<span class="pantheon--liste--donnees--score survol ' . $classe . '">';
								echo $pronostiqueurs[$indice]["Classements_Points"];
							echo '</span>';
						echo '</div>';
					}
				echo '</div>';
			}
			echo '<div class="colle-gauche"></div>';
			echo '<div class="pantheon--detail"></div>';
		echo '</div>';
		//include_once('pied.php');
	echo '</div>';

?>

<script>
	function selectionnerPronostiqueur(elt) {
		var classe = $(elt).attr('class');
		$('.pantheon--liste--donnees.selectionne').removeClass('selectionne');
		$(elt).addClass('selectionne');
	}
	
	$(function() {
		// La largeur des cellules du top 3 dépend du nombre de pronostiqueurs classés dans le top 3
		// En effet, avec les égalités, on peut avoir plus de 3 pronostiqueurs dans cette liste
		// La largeur de la cellule d'un pronostiqueur est donc calculée à la volée
		var largeur_conteneur = $('.pantheon--top3').width();
		var marges_cellule = parseInt($('.pantheon--top3--pronostiqueur').css('margin-left')) + parseInt($('.pantheon--top3--pronostiqueur').css('margin-right'));
		marges_cellule += parseInt($('.pantheon--top3--pronostiqueur').css('padding-left')) + parseInt($('.pantheon--top3--pronostiqueur').css('padding-right'));
		var bordures_cellule = parseInt($('.pantheon--top3--pronostiqueur').css('border-left-width')) + parseInt($('.pantheon--top3--pronostiqueur').css('border-right-width'));
		var nombre_cellules = $('.pantheon--top3--pronostiqueur').length;
		var largeur_cellule = Math.floor((largeur_conteneur / nombre_cellules) - (marges_cellule + bordures_cellule));
		$('.pantheon--top3--pronostiqueur').width(largeur_cellule);
		
		// Même chose pour la liste des pronostiqueurs classés à partir de la 4ème place
		var largeur_liste = $('.pantheon--liste').width();
		var bordures_liste = parseInt($('.pantheon--liste').css('border-left-width')) + parseInt($('.pantheon--liste').css('border-right-width'));
		var nombre_listes = $('.pantheon--liste').length;
		var espacement_liste = Math.floor(((largeur_conteneur / nombre_listes) - (largeur_liste + bordures_liste)) / 1);
		//$('.pantheon--liste').css('margin-left', espacement_liste + 'px');
		$('.pantheon--liste').css('margin-right', espacement_liste + 'px');
		
		
	});
	
	
</script>

</body>
</html>