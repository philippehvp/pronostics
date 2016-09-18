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
		$nomPage = 'reglement.php';
		enregistrerConsultationPage($bdd, $nomPage);
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		echo '<div class="conteneur">';
			include('bandeau.php');
			echo '<div id="divEnteteReglement" class="contenu-page"></div>';

			echo '<div id="divReglement">';
				echo '<ul>';
					echo '<li class="onglet"><a href="#divReglement-1">Règlement général</a></li>';
					echo '<li class="onglet"><a href="#divReglement-2">Règlement Ligue des Champions</a></li>';
					echo '<li class="onglet"><a href="#divReglement-3">Règlement Europa League</a></li>';
					echo '<li class="onglet"><a href="#divReglement-4">Règlement Coupe de France</a></li>';
				echo '</ul>';
				echo '<div id="divReglement-1">';
					include('reglement_general.php');
				echo '</div>';
				
				echo '<div id="divReglement-2">';
					include('reglement_ldc.php');
				echo '</div>';
				
				echo '<div id="divReglement-3">';
					include('reglement_el.php');
				echo '</div>';
				
				echo '<div id="divReglement-4">';
					include('reglement_cdf.php');
				echo '</div>';
			echo '</div>';
			//include('pied.php');
		echo '</div>';
?>
	
	<script>
		$(function() {
			afficherTitrePage('divEnteteReglement', 'Règlement du concours');
			
			$('#divReglement').tabs();
			$('.ui-tabs-anchor').prepend('<em class="icones icones-grandes">&#10150;</em>');
			
		});
		
	</script>
	
</body>
</html>