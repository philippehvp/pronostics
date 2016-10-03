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
		$nomPage = 'creer_compte_rendu.php';
		include('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Page de modèle du compte-rendu

		$ordreSQL =		'	SELECT		IFNULL(CompteRenduModeles_Modele, \'Vide\') AS CompteRenduModeles_Modele' .
						'	FROM		compte_rendu_modeles' .
						'	LIMIT		1';
		$req = $bdd->query($ordreSQL);
		$modele = $req->fetchAll()[0]["CompteRenduModeles_Modele"];

		echo '<div id="divCompteRendu" class="contenu-page">';
			echo '<textarea name="txtCompteRendu" id="txtCompteRendu">' . $modele . '</textarea>';
			echo '<input type="button" value="Enregistrer" class="bouton" onclick="enregistrerCompteRendu();" />';
		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divCompteRendu', 'Compte-rendu');
			retournerHautPage();

			CKEDITOR.replace('txtCompteRendu');

		});
	</script>

</body>
</html>
