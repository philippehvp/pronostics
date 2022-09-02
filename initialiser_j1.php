<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
	echo '<script src="js/jquery/jquery.ui.touch-punch.min.js"></script>';
?>
</head>

<body class="cdf">
	<?php
		$nomPage = 'initialiser_cdf.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		echo '<div class="conteneur">';
			include_once('bandeau.php');
            $ordreSQL =		'	CALL sp_initialisationl1()';
		    $bdd->exec($ordreSQL);
			//include_once('pied.php');
		echo '</div>';

	?>
</body>
</html>