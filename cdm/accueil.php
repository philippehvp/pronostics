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

<body>
	<?php
		$nomPage = 'accueil.php';
		include('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		/*if($administrateur != 1) {
			include('site_maintenance.php');
			return;
		}*/


		// echo '<div id="divAccueil">';
		
		// 	// Affichage des matches en direct
		// 	echo '<div id="divDirect">';
		// 		include('module_direct_poule.php');
		// 		include('module_direct_phase_finale.php');
		// 	echo '</div>';
		
		// 	// Affichage du classement général
		// 	echo '<div id="divClassementGeneral" class="colle-gauche gauche">';
		// 		include('module_classement_general.php');
		// 	echo '</div>';
		
		// 	// Pronostics de poule
		// 	echo '<div id="divAccueilPronosticsPoule" class="gauche">';
		// 		include('module_pronostics_poule.php');
		// 	echo '</div>';
			
		// 	// Pronostics de phase finale
		// 	echo '<div id="divAccueilPronosticsPhaseFinale" class="gauche">';
		// 		include('module_pronostics_phase_finale.php');
		// 	echo '</div>';

		// echo '</div>';	// divAccueil

		echo '<div id="divInfo"></div>';
		echo '<div id="divLegende" class="colle-gauche gauche"></div>';
		echo '<input type="hidden" id="txtDirect" value="" />';
	?>

	<script>
		$(function() {
			<?php
				if($_SESSION["pronostiqueur"] != 1) {
			?>		
					var intervalRafraichissement = setInterval(module_direct_rafraichirZone, 20000);
					$('#txtDirect').val(intervalRafraichissement);
			<?php
				}
			?>
				retournerHautPage();
		});
	</script>
	
</body>
</html>