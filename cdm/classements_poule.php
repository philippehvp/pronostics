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
		$nomPage = 'classements_poule.php';
		include('bandeau.php');
		
		echo '<div id="divVisuClassements"></div>';
		echo '<div id="divVisuClassementsPoule">';
			include('module_classements_poule.php');
		echo '</div>';
	?>
	
	<script>
		$(function() {
			afficherTitrePage('divVisuClassements', 'Classements de poule');
		});

	</script>
	
</body>
</html>