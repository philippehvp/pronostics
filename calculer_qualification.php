<?php
	include_once('commun_administrateur.php');
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
		$nomPage = 'calculer_qualification.php';
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de lancement des calculs des points de qualification (sorties de poules LDC et EL)
		$ordreSQL =		'	CALL sp_calculpointsqualifications()';
		$bdd->exec($ordreSQL);
		
		
		echo '<div>';
			echo 'Les points de qualifications ont été calculés avec succès';
		echo '</div>';
		
	?>

	<script>
		$('#selectChampionnat').change(	function() {	calculerResultats_changerChampionnat();	});
		$('button').button().click	(	function( event ) {
											event.preventDefault();
										}
									);
	</script>
</body>
</html>