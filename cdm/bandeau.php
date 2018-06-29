<?php

	$nomPage = isset($nomPage) ? $nomPage : '';
	
	echo '<div id="divBandeau">';
		echo '<div class="icones">';
			echo '<ul>';
				echo '<li onclick="window.open(\'accueil.php\', \'_self\');"><img src="images/accueil.png" alt="" /><br /><span>Accueil</span></li>';

				if($_SESSION["cdm_pronostiqueur"] == 1 || $phase == 0) {
					echo '<li onclick="window.open(\'creer_prono.php\', \'_self\');"><img src="images/pronostics.png" alt="" /><br />Pronostics</li>';
				} else {
					echo '<li onclick="window.open(\'consulter_prono.php?pronostiqueurConsulte=' . $_SESSION["cdm_pronostiqueur"] . '\', \'_self\');"><img src="images/pronostics.png" alt="" /><br />Pronostics</li>';
				}

				if($phase != 0) {
					echo '<li><img src="images/concours.png" alt="" /><br />Le concours';
						echo '<ul>';
							echo '<li onclick="window.open(\'pronostics_phase_finale.php\', \'_self\');">Pronostics de phase finale</li>';
							echo '<li onclick="window.open(\'pronostics_poule.php\', \'_self\');">Pronostics de poule</li>';
							echo '<li onclick="window.open(\'classements_poule.php\', \'_self\');">Classements de poule</li>';
							echo '<li onclick="window.open(\'meilleur_buteur.php\', \'_self\');">Meilleurs buteurs</li>';
							echo '<li onclick="window.open(\'podium.php\', \'_self\');">Podium</li>';
							echo '<li onclick="window.open(\'voir_statistiques.php\', \'_self\');">Statistiques</li>';
							echo '<li onclick="window.open(\'recapituler_prono.php\', \'_self\');">Résumé de mes pronostics</li>';
							echo '<li>Pronostics de...';
								include('consulter_prono_selection_pronostiqueur.php');
							echo '</li>';
							
						echo '</ul>';
					echo '</li>';
				}
				
				if($_SESSION["cdm_pronostiqueur"] == 1) {
					echo '<li><img src="images/live.png" alt="" /><br /><span>En direct</span>';
						include('matches_direct.php');
					echo '</li>';
				}

				echo '<li onclick="window.open(\'reglement.php\', \'_self\');"><img src="images/reglement.png" alt="" /><br /><span>Règlement</span></li>';
				echo '<li onclick="window.open(\'deconnexion.php\', \'_self\');"><img src="images/quitter.png" /><span>Déconnecter</span></li>';
				
			echo '</ul>';
		echo '</div>';
	echo '</div>';
	echo '<div id="divMenu"></div>';
	echo '<div style="clear: both; height: 20px;"></div>';
?>



