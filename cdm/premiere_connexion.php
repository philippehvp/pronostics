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

<body style="background: url('images/fond/fond_principal_qatar.png') repeat 0% 0%;">
	<?php
		$nomPage = 'premiere_connexion.php';
		
		$erreurModification = isset($_SESSION["cdm_erreur_modification"]) ? $_SESSION["cdm_erreur_modification"] : 0;
		
		// Modification du mot de passe
		echo '<div id="divConnexion">';
			echo '<div class="titre">';
				if($erreurModification == 0)	echo '<label>Première connexion</label>';
				else							echo '<label>Erreur de modification !</label>';

				echo '<form name="formModificationMotDePasse" id="formModificationMotDePasse" method="post" action="premiere_connexion_validation.php" style="margin-top: 1em;">';
          echo '<label>Nouveau</label>';
          echo '<br />';
					echo '<input class="gauche" type="password" name="motDePasse" id="motDePasse" placeholder="Mot de passe" />';
					
					echo '<br /><br />';
					
          echo '<label>Confirmation</label>';
          echo '<br />';
					echo '<input class="gauche" type="password" name="motDePasseConfirmation" id="motDePasseConfirmation" placeholder="Confirmation" />';
					echo '<div class="validation"><label>&raquo;</label></div>';
				echo '</form>';
			echo '</div>';
    echo '</div>';
		
	?>
	
	<script>
		$(function() {
			$('.validation').click(function(event) {	premiereConnexion_validerMotDePasse();	});
		});
	</script>
</body>
</html>