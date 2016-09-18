<?php
	include('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
</head>

<body style="background: url('images/camp_nou_flou.jpg') repeat 0% 0%;">
	<?php
		$nomPage = 'premiere_connexion.php';
		
		$erreurModification = isset($_SESSION["erreurModification"]) ? $_SESSION["erreurModification"] : 0;
		
		// Modification du mot de passe
		echo '<div class="connexion">';
			//echo '<img src="images/logo_poulpe.png" alt="" width="200px" height="40px"/>';
			echo '<div class="connexion--titre">';
				if($erreurModification == 0)	echo '<label class="connexion--libelle">&nbsp;</label><label>Première connexion</label>';
				else							echo '<label class="connexion--libelle">&nbsp;</label><label class="texte-rouge">Erreur de modification !</label>';

				echo '<form name="formModificationMotDePasse" id="formModificationMotDePasse" method="post" action="premiere_connexion_validation.php" style="margin-top: 1em;">';
					echo '<label class="connexion--libelle">Nouveau</label>';
					echo '<input class="connexion--champ" type="password" name="motDePasse" id="motDePasse" placeholder="Mot de passe" />';
					
					echo '<br />';
					
					echo '<label class="connexion--libelle">Confirmation</label>';
					echo '<input class="connexion--champ" type="password" name="motDePasseConfirmation" id="motDePasseConfirmation" placeholder="Confirmation" />';
					
					echo '<br />';
					echo '<label class="connexion--libelle">&nbsp;</label>';
					echo '<label class="validation">Modifier</label>';
				echo '</form>';
			echo '</div>';
		echo '</div>';
		
	?>
	
	<script>
		$(function() {
			$('.validation').button().click(function(event) {	premiereConnexion_validerMotDePasse();	});
		});
	</script>
</body>
</html>