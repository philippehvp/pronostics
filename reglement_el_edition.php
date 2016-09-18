<?php
	include('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
	<script src="ckeditor/ckeditor.js"></script>
</head>


<body>
	<?php
		$nomPage = 'reglement_el_edition.php';
		include('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Page de règlement EL
		
		$ordreSQL =		'	SELECT		IFNULL(Reglements_Texte, \'Vide\') AS Reglements_Texte' .
						'	FROM		reglements' .
						'	WHERE		Championnats_Championnat = 3';
		$req = $bdd->query($ordreSQL);
		$reglements = $req->fetchAll();
		$reglement = $reglements[0]["Reglements_Texte"];
		
		echo '<div id="divReglementEL" class="contenu-page">';
			echo '<textarea name="txtReglement" id="txtReglement">' . $reglement . '</textarea>';
			echo '<input type="button" value="Enregistrer" class="bouton" onclick="enregistrerReglement(3);" />';
		echo '</div>';
		
	?>

	<script>
		$(function() {
			afficherTitrePage('divReglementEL', 'Règlement EL');
			retournerHautPage();
			
			CKEDITOR.replace('txtReglement');
			
		});
	</script>
	
</body>
</html>