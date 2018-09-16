<?php
	include('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
	include_once('classements_pronostiqueurs_fonctions.php');
?>

</head>

<body>
<?php
	// Module d'affichage des classements
	$nomPage = 'classements_pronostiqueurs.php';
	enregistrerConsultationPage($bdd, $nomPage);
	echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
	
	// Le paramètre "affichageNeutre" passé dans l'URL permet d'empêcher le tableau de faire apparaître le pronostiqueur connecté en surbrillance
	$affichageNeutre = isset($_GET["neutre"]) ? $_GET["neutre"] : 0;
	
	// Parcours des championnats avec affichage :
	// - du classement général
	// - du classement de la dernière journée
	// - du classement général buteur

	$ordreSQL =		'	SELECT		championnats.Championnat, championnats.Sans_Buteur, championnats.Championnats_Nom' .
					'	FROM		(' .
					'					SELECT		Championnat, Championnat * 10 AS Indice_Tri, 0 AS Sans_Buteur, Championnats_Nom' .
					'					FROM		championnats' .
					'					WHERE		championnats.Championnat NOT IN (5)' .
					'					UNION ALL' .
					'					SELECT		1 AS Championnat, 15 AS Indice_Tri, 1 AS Sans_Buteur, \'Ligue 1 SB\' AS Championnats_Nom' .
					'				) championnats' .
					'	ORDER BY	championnats.Indice_Tri';

	$req = $bdd->query($ordreSQL);
	$championnats = $req->fetchAll();

	// Dans le cas où aucun championnat n'a encore débuté, on aimerait afficher un message
	// Pour cela, on compte le nombre de journées détectées pour tous les championnats
	// Si ce nombre de journées vaut 0, alors aucune journée n'a été trouvée
	$nombreTotalJournees = 0;
	
	if(count($championnats)) {
		// Les championnats sont mis dans des onglets différents pour une meilleure lisibilité
		echo '<div class="conteneur">';
			include('bandeau.php');
		
			echo '<div id="divClassements" class="contenu-page">';
				echo '<ul></ul>';
		
				// Parcours de chaque championnat lu
				$indice = 0;
				foreach($championnats as $unChampionnat) {
					$indice++;
					$championnat = $unChampionnat["Championnat"];
					$sansButeur = $unChampionnat["Sans_Buteur"];
					
					$ordreSQL = lireDerniereJournee($bdd, $championnat);
					$req = $bdd->query($ordreSQL);
					$donnees = $req->fetchAll();
					if(count($donnees) > 0) {
						$nombreTotalJournees += count($donnees);
						$journee = $donnees[0]["Journee"];
						$journeeNom = $donnees[0]["Journees_Nom"];
						$dateReference = $donnees[0]["Classements_DateReference"];
						$dateMAJJournee = $donnees[0]["Journees_DateMAJ"];
						$dtDateMAJ = new DateTime($dateMAJJournee);

						echo '<div id="divClassements-' . $indice . '" class="championnat" title="' . $unChampionnat["Championnats_Nom"] . '">';
							// Liste des journées du championnat
							// Attention, on doit faire la différence entre la ligue 1 normale et la version sans buteur
							$ordreSQL =		'	SELECT		DISTINCT journees.Journee, classements.Classements_DateReference, IFNULL(journees.Journees_NomCourt, journees.Journees_Nom) AS Journees_Nom' .
											'	FROM		journees' .
											($sansButeur == 0 ? '	LEFT JOIN	classements' : '	LEFT JOIN	classements_sb classements') .
											'				ON		journees.Journee = classements.Journees_Journee' .
											'	WHERE		journees.Championnats_Championnat = ' . $championnat .
											'				AND		journees.Journee <= ' . $journee .
											'	ORDER BY	classements.Classements_DateReference DESC';
							$req = $bdd->query($ordreSQL);
							if(empty($req))
								break;
							$journees = $req->fetchAll();
							$nombreJournees = count($journees);

							if($nombreJournees) {
								echo '<div>';
									echo '<label class="texteJournee">Journées :&nbsp;</label>';
									$indiceJournee = 0;
									foreach($journees as $uneJournee) {
										echo '<label class="lienJournee" onclick="classementsPronostiqueurs_afficherJournee(' . $championnat . ', ' . $uneJournee["Journee"] . ', \'' . $uneJournee["Classements_DateReference"] . '\', \'divClassementsTableaux-' . $indice . '\', ' . $affichageNeutre . ', ' . $sansButeur . ');">' . $uneJournee["Journees_Nom"] . '</label>';
										if($indiceJournee++ < $nombreJournees - 1)
											echo '<label class="texteJournee"> - </label>';
									}
								echo '</div>';
								
								echo '<br />';
								
								if($journee != null) {
									echo '<div id="divClassementsTableaux-' . $indice . '">';
										$afficherClassementButeur = 1;
										$affichageJourneeSuivante = 0;			// On ne doit jamais afficher la journée suivante
										afficherClassements($bdd, $championnat, $journee, $dateReference, $dtDateMAJ, $journeeNom, $afficherClassementButeur, $affichageJourneeSuivante, $sansButeur);
									echo '</div>';
								}
							}
						echo '</div>';
					}
					else {
						echo '<div id="divClassements-' . $indice . '" class="championnat" title="' . $unChampionnat["Championnats_Nom"] . '">';
							echo '<label>Aucune donnée à afficher</label>';
						echo '</div>';
					}
				}
				if($nombreTotalJournees == 0)
					echo '<label>Aucune donnée à afficher</label>';

			echo '</div>';
			//include('pied.php');
			
		echo '</div>';
	}
	
?>

	<script>
		$(function() {
			afficherTitrePage('.contenu-page', 'Classements');
		
			$('#divClassements .championnat').each	(	function() {
					var nombreTab = $("div#divClassements ul li").length + 1;
					$('div#divClassements ul').append('<li><a href="#divClassements-' + nombreTab + '">' + $(this).attr('title') + '</a></li>');
				}
			);
			$('#divClassements').tabs();
			
			// Dans le cas d'un affichage neutre, ne pas mettre en surbrillance les lignes du pronostiqueur connecté
			var affichageNeutre = '<?php echo $affichageNeutre; ?>';
			if(affichageNeutre == '1') {
				var fondNormal = $('.tableau--classement--corps td').css('background-color');
				var couleurNormale = $('.tableau--classement--corps td').css('color');
				$('.tableau--classement tbody td.surbrillance').css('background-color', fondNormal + ' !important').css('color', couleurNormale + ' !important');
			}
		});
	</script>


</body>
</html>