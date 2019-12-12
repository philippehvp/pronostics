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

<body>
	<?php
		$nomPage = 'modifier_mot_de_passe.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';


		$erreurModification = isset($_SESSION["erreurModification"]) ? $_SESSION["erreurModification"] : 0;

		// Modification du mot de passe
		echo '<div id="divModificationMotDePasse" class="connexion contenu-page">';
			if($erreurModification) {
				echo '<div class="titre">';
					echo '<label>Erreur de modification de mot de passe !</label>';
				echo '</div>';
			}

			$_SESSION["erreurModification"] = 0;


			echo '<form name="formModificationMotDePasse" id="formModificationMotDePasse" method="post" action="modifier_mot_de_passe_validation.php">';
				echo '<label>Mot de passe actuel</label>';
				echo '<br />';
				echo '<input type="password" name="motDePasseActuel" id="motDePasseActuel" placeholder="Mot de passe actuel" /><br />';

				echo '<label>Nouveau mot de passe</label>';
				echo '<br />';
				echo '<input type="password" name="motDePasse" id="motDePasse" placeholder="Nouveau mot de passe" /><br />';

				echo '<label>Confirmation</label>';
				echo '<br />';
				echo '<input class="gauche" type="password" name="motDePasseConfirmation" id="motDePasseConfirmation" placeholder="Confirmation" /><br />';

				echo '<div class="validation">Modifier le mot de passe</div>';
			echo '</form>';
		echo '</div>';
	?>

	<script>
		$(function() {
			afficherTitrePage('divModificationMotDePasse', 'Modification du mot de passe');

			<?php
				//	A l'ouverture de la page, si le paramètre motdepasse est égal à 1, cela signifie qu'une modification de mot de passe vient d'avoir lieu
				$modification = isset($_GET["motdepasse"]) ? $_GET["motdepasse"] : 0;
				if($modification == 1) {
			?>
					afficherMessageInformationBandeau('Mot de passe a été modifié avec succès', 2000, '');
					// Changement de l'adresse URL de la page pour qu'elle n'affiche plus le paramètre passé dans l'URL
					var etatObjet = { foo: 'bar' };
					history.pushState(etatObjet, "Le Poulpe d'Or", "modifier_mot_de_passe.php");
			<?php
				}
			?>


			$('.validation').button().click(function(event) {	modifierMotDePasse_validerMotDePasse();	});
		});
	</script>
</body>
</html>