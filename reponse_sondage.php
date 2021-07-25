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
				echo '<br/>';
				
				include_once('sondage.php');

				echo '<br/><br/>';
				echo '<form id="formSondage">';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse1" name="sondage" value="1">';
						echo '<label for="reponse1">' . 'OUI Je la lis à chaque fois c\'est parfait comme ça' . '</label>';
					echo '</span>';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse2" name="sondage" value="2">';
						echo '<label for="reponse2">' . 'OUI mais en modifiant son contenu : quelle partie modifier/supprimer ou quelle rubrique rajouter ?' . '</label>';
					echo '</span>';
					echo '<span class="choix">';
						echo '<label for="commentaire">Suggestion :</label><br />';
						echo '<textarea id="commentaire" name="commentaire" minlength="3" maxlength="500" rows="5" cols="90"></textarea>';
					echo '</span>';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse3" name="sondage" value="3">';
						echo '<label for="reponse3">' . 'NON je ne la lis que quand je gagne donc rarement' . '</label>';
					echo '</span>';
					echo '<span class="choix">';
						echo '<input type="radio" id="reponse4" name="sondage" value="4">';
						echo '<label for="reponse4">' . 'NON je trouve toutes les infos sur le site donc son contenu ne m\'intéresse pas' . '</label>';
					echo '</span>';
				echo '</form>';
				echo '<br /><label class="validation">Répondre</label>';
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