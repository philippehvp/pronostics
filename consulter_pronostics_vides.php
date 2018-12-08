<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>

<body>
	<?php
		$nomPage = 'envoyer_courrier.php';
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		$ordreSQL =		'	SELECT		CONCAT(equipesDomicile.Equipes_Nom, \' - \', equipesVisiteur.Equipes_Nom) AS Equipes' .
						'				,GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \'; \') AS Pronostiqueurs_NomUtilisateur' .
						'	FROM		matches' .
						'	JOIN		journees' .
						'				ON		matches.Journees_Journee = journees.Journee' .
						'	JOIN		pronostics' .
						'				ON		matches.Match = pronostics.Matches_Match' .
						'	JOIN		pronostiqueurs' .
						'				ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		equipes equipesDomicile' .
						'				ON		matches.Equipes_EquipeDomicile = equipesDomicile.Equipe' .
						'	JOIN		equipes equipesVisiteur' .
						'				ON		matches.Equipes_EquipeVisiteur = equipesVisiteur.Equipe' .
						'	LEFT JOIN	(' .
						'					SELECT		Matches_Match, Pronostiqueurs_Pronostiqueur, PronosticsCarreFinal_Coefficient' .
						'					FROM		pronostics_carrefinal' .
						'				) pronostics_carrefinal' .
						'				ON		matches.Match = pronostics_carrefinal.Matches_Match' .
						'						AND		pronostics.Pronostiqueurs_Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		DATE_FORMAT(matches.Matches_Date, \'%Y%m%d\') = DATE_FORMAT(NOW(), \'%Y%m%d\')' .
						'				AND		(' .
						'							pronostics_carrefinal.PronosticsCarreFinal_Coefficient IS NULL' .
						'							OR		pronostics_carrefinal.PronosticsCarreFinal_Coefficient <> 0' .
						'						)' .
						'				AND		(' .
						'							pronostics.Pronostics_ScoreEquipeDomicile IS NULL' .
						'							OR		pronostics.Pronostics_ScoreEquipeVisiteur IS NULL' .
						'						)' .
						'				AND		(' .
						'							pronostics_carrefinal.PronosticsCarreFinal_Coefficient IS NULL' .
						'							OR		pronostics_carrefinal.PronosticsCarreFinal_Coefficient IS NULL <> 0' .
						'						)' .
						'	GROUP BY	matches.Match';
					
		$req = $bdd->query($ordreSQL);
		$pronosticsVides = $req->fetchAll();
		$nombrePronosticsVides = sizeof($pronosticsVides);
		
		echo '<div class="contenu-page">';
		
			if($nombrePronosticsVides > 0) {
				// Adresses électroniques des pronostiqueurs
				$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_MEL SEPARATOR \'; \') AS Pronostiqueurs_MEL' .
								'	FROM		(' .
								'					SELECT		DISTINCT Pronostiqueurs_MEL' .
								'					FROM		matches' .
								'					JOIN		journees' .
								'								ON		matches.Journees_Journee = journees.Journee' .
								'					JOIN		pronostics' .
								'								ON		matches.Match = pronostics.Matches_Match' .
								'					JOIN		pronostiqueurs' .
								'								ON		pronostics.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
								'					WHERE		DATE_FORMAT(matches.Matches_Date, \'%Y%m%d\') = DATE_FORMAT(NOW(), \'%Y%m%d\')' .
								'								AND		(' .
								'											pronostics.Pronostics_ScoreEquipeDomicile IS NULL' .
								'											OR		pronostics.Pronostics_ScoreEquipeVisiteur IS NULL' .
								'										)' .
								'				) adresses';
				$req = $bdd->query($ordreSQL);
				$adresses = $req->fetchAll();
				$nombreAdresses = sizeof($adresses);
				
				// Titre
				$ordreSQL =		'	SELECT		DISTINCT CONCAT(\'Le Poulpe d\\\'Or - \', Journees_Nom, \' : Pronostics non effectués\') AS Objet' .
								'	FROM		journees' .
								'	JOIN		matches' .
								'				ON		journees.Journee = matches.Journees_Journee' .
								'	JOIN		pronostics' .
								'				ON		matches.Match = pronostics.Matches_Match' .
								'	WHERE		DATE_FORMAT(matches.Matches_Date, \'%Y%m%d\') = DATE_FORMAT(NOW(), \'%Y%m%d\')' .
								'				AND		(' .
								'							pronostics.Pronostics_ScoreEquipeDomicile IS NULL' .
								'							OR		pronostics.Pronostics_ScoreEquipeVisiteur IS NULL' .
								'						)';
				$req = $bdd->query($ordreSQL);
				$journees = $req->fetchAll();
				$nombreJournees = sizeof($journees);
			
				echo '<label>Destinataires :</label><br /><input style="padding: 0 1em; width: 100%; height: 2em;" type="text" id="txtAdresses" value="' . $adresses[0]["Pronostiqueurs_MEL"] . '" /><br /><br />';
				echo '<label>Objet :</label><br /><input style="padding: 0 1em; width: 100%; height: 2em;" type="text" id="txtObjet" value="' . $journees[0]["Objet"] . '" /><br /><br />';
				echo '<label>Message :</label><br />';
				echo '<textarea style="padding: 0 1em; width: 100%; height: 50em;">';
					foreach($pronosticsVides as $unPronosticVide) {
						echo 'Match ' . $unPronosticVide["Equipes"] . '&#13;&#10;' . $unPronosticVide["Pronostiqueurs_NomUtilisateur"] . '&#13;&#10;&#13;&#10;';
					}
				echo '</textarea>';
				echo '<label style="display: none;" id="labelEnvoyerCourrier">Envoyer le courrier</label>';
			}
			else
				echo '<label>Aucun pronostic non saisi n\'a été trouvé</label>';
		echo '</div>';
		
	?>

	<script>

		$(function() {
			afficherTitrePage('.contenu-page', 'Pronostics non saisis');
			
			$('#labelEnvoyerCourrier').button().click	(	function(event) {
																envoyer_courrier_envoyerCourrier();
															}
														);
									
		});
	</script>
	
</body>
</html>