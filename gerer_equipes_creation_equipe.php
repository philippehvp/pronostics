<?php
	include_once('commun_administrateur.php');

	// Page de création d'une équipe
	
	echo '<table class="tableau--liste">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td>Nom de l\'équipe</td>';
				echo '<td><input type="text" id="txtEquipeNom" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Nom court</td>';
				echo '<td><input type="text" id="txtEquipeNomCourt" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Fanion</td>';
				echo '<td><input type="text" id="txtFanion" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>L1</td>';
				echo '<td><input type="checkbox" id="cbL1" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>L1 Européen</td>';
				echo '<td><input type="checkbox" id="cbL1Europe" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>LDC</td>';
				echo '<td><input type="checkbox" id="cbLDC" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>EL</td>';
				echo '<td><input type="checkbox" id="cbEL" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Barrages</td>';
				echo '<td><input type="checkbox" id="cbBarrages" value="" /></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>CDF</td>';
				echo '<td><input type="checkbox" id="cbCDF" value="" /></td>';
			echo '</tr>';
		echo '</tbody>';
	echo '</table>';
		
?>
