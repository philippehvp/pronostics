<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	echo '<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>';
?>
</head>

<body>
	<?php
		$nomPage = 'envoyer_courrier.php';
		include_once('bandeau.php');
		
		// Lecture de toutes les journées de la table des courriers
		$ordreSQL =		'	SELECT		DISTINCT Courriers_JourneeEnCours, Courriers_DateLocale' .
						'	FROM		cdm_courriers' .
						'	ORDER BY	Courriers_DateLocale';
		$req = $bdd->query($ordreSQL);
		$journeesEnCours = $req->fetchAll();
		
		$ordreSQL =		'	SELECT		IFNULL(Courriers_Message, \'\') AS Courriers_Message' .
						'	FROM		cdm_courriers' .
						'	WHERE		Courriers_JourneeEnCours = 1';
		$req = $bdd->query($ordreSQL);
		$message = $req->fetch();
		$req->closeCursor();
						
		echo '<div id="divEnvoyerCourrier">';
			if(sizeof($journeesEnCours)) {
				echo '<select id="selectJourneesEnCours" onchange="envoyerCourrier_changerJournee()">';
					setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
					foreach($journeesEnCours as $journeeEnCours)
						echo '<option value="' . $journeeEnCours["Courriers_JourneeEnCours"] . '">Journée ' . $journeeEnCours["Courriers_JourneeEnCours"] . ' du ' . strftime('%#d %B', strtotime($journeeEnCours["Courriers_DateLocale"])) . '</option>';
				echo '</select>';
				echo '<div id="divMessage">' . $message["Courriers_Message"] . '</div>';
				echo '<label style="display: none;" id="labelSauvegarderMessage">Sauvegarder le message</label>';
				echo '<label style="display: none;" id="labelEnvoyerCourrier">Envoyer le courrier</label>';
			}
			else {
				echo 'Erreur inattendue rencontrée : aucune journée en cours trouvée dans la table des courriers';
			}
		echo '</div>';
		
		echo '<div id="divInfo"></div>';
		
	?>

	<script>

		$(function() {
			afficherTitrePage('divEnvoyerCourrier', 'Envoi de courrier');
			
			CKEDITOR.replace('divMessage');
			$('#labelSauvegarderMessage').css({'display': 'block'});
			$('#labelEnvoyerCourrier').css({'display': 'block'});
			
			$('#labelSauvegarderMessage').button().click	(	function(event) {
																var message = CKEDITOR.instances.divMessage.getData();
																envoyer_courrier_sauvegarderJournee(message);
															}
														);
													
			$('#labelEnvoyerCourrier').button().click	(	function(event) {
																envoyer_courrier_envoyerCourrier();
															}
														);
									
		});
	</script>
	
</body>
</html>