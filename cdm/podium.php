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
		$nomPage = 'podium.php';
		include_once('bandeau.php');
		
		echo '<div id="divVisuPodium">';
			include_once('module_podium.php');
		echo '</div>';
	?>
	
	<script>
		$(function() {
			afficherTitrePage('divVisuPodium', 'Pronostics de podium');
		});

	</script>
	
</body>
</html>