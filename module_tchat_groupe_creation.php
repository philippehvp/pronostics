<?php
	include_once('commun.php');

	// Page de création d'un tchat de groupe
	
	echo '<label class="libelle">Nom du groupe </label>';
	echo '<br />';
	echo '<input type="text" value="" id="txtNomTchatGroupe" />';
	echo '<br /><br />';
	echo '<label class="lienActif" onclick="moduleTchatGroupe_selectionnerPronostiqueurs(\'taPronostiqueurs\', 1);">Gestion des membres de ce groupe</label>';
	echo '<br />';
	echo '<textarea id="taPronostiqueurs" class="module--tchat-groupe-texte" readonly></textarea>';
?>

