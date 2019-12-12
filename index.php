<?php
	session_start();

	// Vérification de l'état de la connexion_connecter de l'utilisateur
	$pronostiqueur = isset($_SESSION["pronostiqueur"]) ? $_SESSION["pronostiqueur"] : 0;

	if($pronostiqueur <> 0)
		header('Location: accueil.php');
	else {
		$_SESSION["nom_pronostiqueur"] = NULL;
		$_SESSION["administrateur"] = 0;
	}

	$erreurLogin = isset($_SESSION["erreurLogin"]) ? $_SESSION["erreurLogin"] : 0;
	$login = isset($_COOKIE["lepoulpeg_login"]) ? $_COOKIE["lepoulpeg_login"] : '';
	$mdp = isset($_COOKIE["lepoulpeg_mdp"]) ? $_COOKIE["lepoulpeg_mdp"] : '';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>

</head>


<body style="background: url('css/pronostics/themes/defaut/fond.jpg') no-repeat 50% 0;">
	<?php
		echo '<div class="connexion--cartouche">';
			echo '<div class="connexion--entete entete">';
				echo '<label>Bienvenue sur le Poulpe d\'Or</label>';
			echo '</div>';
			if($erreurLogin != 0)
				echo '<label class="connexion--echec aligne-centre texte-rouge">Les données de connexion saisies sont erronées</label>';

			echo '<form name="formConnexion" method="post" action="connexion.php" style="margin-top: 3em;">';
				echo '<label class="connexion--libelle">Utilisateur</label>';
				echo '<input class="connexion--champ" type="text" name="login" id="login" value="' . $login . '">';
				echo '<br />';

				echo '<label class="connexion--libelle">Mot de passe</label>';
				echo '<input class="connexion--champ" type="password" name="mdp" id="mdp" value="' . $mdp . '">';

				echo '<br />';
				echo '<label class="connexion--libelle">&nbsp;</label>';
				echo '<label class="bouton-connexion" onclick="connexion_connecter();">Se connecter</label>';
			echo '</form>';
		echo '</div>';
	?>

	<script>
		$(function() {
			//$('.validation').button().click(function(event) {	connexion_connecter();	});

			$('body').keyup(function(event) {
				if(event.which == 13) {
					$('[name="formConnexion"]').submit();
				}
			});

			centrerObjet('.connexion--cartouche', 0, 1);

		});

	</script>




</body>
</html>