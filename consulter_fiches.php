<?php
	include_once('commun.php');
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	include_once('consulter_fiches_fonctions.php');
?>
</head>

<body>
	<?php
		$nomPage = 'consulter_fiches.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		$NOMBRE_COLONNES = 10;

		// Consultation de la fiche d'identité des pronostiqueurs
		
		// Lecture des pronostiqueurs
		$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur' .
						'	FROM		pronostiqueurs' .
						'	ORDER BY	Pronostiqueurs_NomUtilisateur';

		$req = $bdd->query($ordreSQL);
		$pronostiqueurs = $req->fetchAll();
		$nombrePronostiqueurs = sizeof($pronostiqueurs);
		$nombrePronostiqueursParColonne = ceil($nombrePronostiqueurs / $NOMBRE_COLONNES);
		
		echo '<div class="conteneur">';
			include_once('bandeau.php');
			echo '<div id="divConsultationFicheIdentite" class="contenu-page">';
				if($nombrePronostiqueurs) {
					echo '<div>';
						// L'affichage des pronostiqueurs se fait sur plusieurs colonnes
						for($i = 0; $i < $NOMBRE_COLONNES; $i++) {
							echo '<div class="gauche">';
								for($j = 0; $j < $nombrePronostiqueursParColonne && $i * $nombrePronostiqueursParColonne + $j < $nombrePronostiqueurs; $j++) {
									echo '<label class="fiche--nom" onclick="consulterFiches_consulterFiche(' . $pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueur"] . ');">' . $pronostiqueurs[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueurs_NomUtilisateur"] . '</label><br />';
								}
							echo '</div>';
						}
						echo '<div class="colle-gauche"></div>';
					echo '</div>';
				}
				else {
					echo '<label>Aucun pronostiqueur n\'a été trouvé</label>';
				}

				echo '<div style="margin-top: 20px;" class="colle-gauche fiche">';
					consulterFiche($bdd, $_SESSION["pronostiqueur"], 0);
				echo '</div>';
			echo '</div>';
			//include_once('pied.php');
		echo '</div>';
	?>
	
	<script>
		$(function() {
			afficherTitrePage('divConsultationFicheIdentite', 'Consultation des fiches d\'identité');
		});
	</script>
</body>
</html>