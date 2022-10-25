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
		$nomPage = 'accueil.php';

		// Dans un premier temps on détermine dans quelle phase on se trouve :
		// - avant le premier match de poule : phase de pronostics
		// - après le premier match de poule et une heure avant le premier 1/8 de finale : phase de poule
		// - après la phase de poule on se trouve en phase finale
		if(time() < 1668960000) {
			$phase = 0;
		} else if(time() >= 1668960000 && time() < 1670079600) {
			$phase = 1;
		} else {
			$phase = 2;
		}

		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		/*if($_SESSION["cdm_pronostiqueur"] != 1) {
			include_once('site_maintenance.php');
			return;
		}*/


		echo '<div id="divAccueil">';
			// Affichage des matches en direct
			if($phase != 0) {
				echo '<div id="divDirect">';
					if($phase == 1) {
						include_once('module_direct_poule.php');
					} else if($phase == 2) {
						include_once('module_direct_phase_finale.php');
					}
				echo '</div>';
			}
		
			// Affichage du classement général
			if($phase != 0) {
				echo '<div id="divClassementGeneral" class="colle-gauche gauche">';
					include_once('module_classement_general.php');
				echo '</div>';
			}
			
			// Pronostics de poule
			if($phase == 1) {
				echo '<div id="divAccueilPronosticsPoule" class="gauche">';
					include_once('module_pronostics_poule.php');
				echo '</div>';
			} else if($phase == 2) {
				// Pronostics de phase finale
				echo '<div id="divAccueilPronosticsPhaseFinale" class="gauche">';
					include_once('module_pronostics_phase_finale.php');
				echo '</div>';
			}

		echo '</div>';	// divAccueil

		echo '<div id="divInfo"></div>';
		echo '<div id="divLegende" class="colle-gauche gauche"></div>';
		echo '<input type="hidden" id="txtDirect" value="" />';
	?>

	<script>
		$(function() {
			<?php
				if($_SESSION["cdm_pronostiqueur"] != 1) {
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