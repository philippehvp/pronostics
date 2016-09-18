<?php
	// Module de tchat - Options
	
	// La liste des destinataires
	// Pour le tchat public, on se contente d'afficher une indication simple
	// Pour le tchat de groupe, on affiche le nom du groupe ainsi que la liste des membres du groupe
	if($parametre == 1)
		echo '<h2>Tchat public</h2>';
	else {
		// Lecture du nom des pronostiqueurs du groupe
		$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Pronostiqueurs_NomUtilisateur' .
						'	FROM		tchat_groupes_membres' .
						'	JOIN		pronostiqueurs' .
						'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
						'	WHERE		tchat_groupes_membres.TchatGroupes_TchatGroupe = ' . $parametre .
						'				AND		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"];
		$req = $bdd->query($ordreSQL);
		$membres = $req->fetchAll();
		
		echo '<div style="overflow: hidden;"><label title="' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '">Discussion avec ' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '</label></div><br />';
	}
	
	// Zone de saisie du texte à envoyer
	echo '<textarea class="module--tchat-texte module--tchat-texte-vide" style="border: 1px solid #fff;" maxlength="255">Saisissez votre texte ici. Touche "Entrée" pour l\'envoyer</textarea>';
?>

<script>
	$(function() {
		// Lorsque la zone de texte récupère le focus, on efface le contenu s'il s'agit du contenu standard
		$('.module--tchat-texte').focus(function(event) {
			if($(this).val() == 'Saisissez votre texte ici. Touche "Entrée" pour l\'envoyer') {
				$(this).val('');
				$(this).removeClass('module--tchat-texte-vide');
			}
		});

		// Lorsque la zone de texte perd le focus, on remet le contenu standard si la zone est vide
		$('.module--tchat-texte').blur(function(event) {
			if($(this).val() == '') {
				$(this).val('Saisissez votre texte ici. Touche "Entrée" pour l\'envoyer');
				$(this).addClass('module--tchat-texte-vide');
			}
		});		
	
		$('#' + '<?php echo $nomConteneurComplet;?>' + ' .module--tchat-texte').keydown(function(event) {
			if(event.which == 13) {
				// On vérifie que du texte a été saisi
				if($(this).val().trim() != '')
					moduleTchat_envoyerMessage('<?php echo $nomConteneurComplet;?>', '<?php echo $parametre;?>');

				event.preventDefault();
			}
		});

	});
</script>