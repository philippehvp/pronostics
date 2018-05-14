<?php
	session_start();
	
	// Vérification de l'état de la connexion de l'utilisateur
	$pronostiqueur = isset($_SESSION["cdm_pronostiqueur"]) ? $_SESSION["cdm_pronostiqueur"] : 0;
	
	if($pronostiqueur <> 0)
		header('Location: accueil.php');
	else {
		$_SESSION["nomPronostiqueur"] = NULL;
		$_SESSION["administrateur"] = 0;
	}
	
	$erreurLogin = isset($_SESSION["erreurLogin"]) ? $_SESSION["erreurLogin"] : 0;
	$login = isset($_COOKIE["cdm_login"]) ? $_COOKIE["cdm_login"] : '';
	$mdp = isset($_COOKIE["cdm_mdp"]) ? $_COOKIE["cdm_mdp"] : '';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
	<style>
	</style>

</head>


<body>
	<?php
		echo '<div id="divConnexion">';
			echo '<div class="titre">';
				if($erreurLogin == 0)	echo '<label>Connexion</label>';
				else					echo '<label>Erreur de connexion !</label>';
			echo '</div>';
		
			echo '<form name="formConnexion" method="post" action="connexion.php">';
				echo '<label>Nom</label>';
				echo '<br />';
				echo '<input type="text" name="login" id="login" value="' . $login . '">';
				echo '<br />';echo '<br />';
				
				echo '<label>Mot de passe</label>';
				echo '<br />';
				echo '<input class="gauche" type="password" name="mdp" id="mdp" value="' . $mdp . '">';
				echo '<div class="validation"><label>&raquo;</div>';
			
			echo '</form>';
		echo '</div>';
	?>

	<script>
		$(function() {
			$('.validation').on('click', function(event) {	connexion_connecter();	});
			
			$('body').keypress(function(event) {
				if(event.which == 13) {
					$('[name="formConnexion"]').submit();
				}
			});

		});

	</script>

	
	
	
</body>
</html>