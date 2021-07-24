<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>

<body style="background: url('images/camp_nou_flou.jpg') repeat 0% 0%;">
	<?php
		$nomPage = 'reponse_sondage.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Réponse au sondage
		echo '<div class="conteneur">';
			echo '<div id="divEnteteReponseSondage" class="contenu-page"></div>';

			echo '<div id="divReponseSondage">';
				echo '<h3>Sondage sur le maintien du mail de compte-rendu hebdomadaire</h3>';
				echo '<br/>';
				echo '<p>Texte d\'explication à définir...</p>';
				echo '<form id="formSondage">';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse1" name="sondage" value="1">';
						echo '<label for="reponse1">' . 'Choix 1 (sans commentaire)' . '</label>';
					echo '</span>';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse2" name="sondage" value="2">';
						echo '<label for="reponse2">' . 'Choix 2 (avec commentaire éventuel)' . '</label>';
					echo '</span>';
					echo '<span class="choix">';
						echo '<label for="commentaire">Commentaire réponse 2 :</label>';
						echo '<input type="text" id="commentaire" name="commentaire" minlength="3" maxlength="255" size="255">';
					echo '</span>';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse3" name="sondage" value="3">';
						echo '<label for="reponse3">' . 'Choix 3 (sans commentaire)' . '</label>';
					echo '</span>';
				echo '</form>';
				echo '<label class="validation">Répondre</label>';
			echo '</div>';	
		echo '</div>';
	?>

	<script>
		$(function() {
			afficherTitrePage('divEnteteReponseSondage', 'Sondage');
			$('#commentaire').attr('disabled', 'disabled');
			$('.validation').attr('disabled', 'disabled');

			// Si réponse 2 choisie, déblocage du commentaire lié à la réponse 2
			$('#formSondage input').on('change', function() {
				const valeur = $('input[name=sondage]:checked', '#formSondage').val();
				if (valeur == '2') {
					$('#commentaire').removeAttr('disabled');
				} else {
					$('#commentaire').attr('disabled', 'disabled');
					$('#commentaire').val('');
				}
				$('.validation').removeAttr('disabled');
			});


			$('.validation').button().click(function(event) {	reponseSondage_validerReponse();	});
		});

	</script>
</body>
</html>