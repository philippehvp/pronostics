<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';
?>
</head>

<body>
	<?php
		$nomPage = 'accueil.php';
		enregistrerConsultationPage($bdd, $nomPage);
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

	?>
	<script>
		$(function() {
			/*retournerHautPage();
			activitePronostiqueur();
			verificationMessage();*/			
		});
	
	</script>
	
</body>
</html>