<?php
	include_once('commun.php');

	echo '<div>';
		echo '<label class="titre">Tchat public</label>';
		echo '<label class="lien" onclick="modules_afficherModule(50, \'divModule50\', 1);" title="Tchat public accessible à tous les joueurs">Tchatter</label><br />';

		// Lecture de tous les pronostiqueurs
		// En priorité, on affiche les pronostiqueurs ayant laissé un ou plusieurs messages non lus
		// Puis les pronostiqueurs connectés (s'ils ne sont pas déjà ci-dessus)
		// Enfin, les autres
		
		$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, MessagesLus_NombreMessages' .
						'				,CASE' .
						'					WHEN	messages_non_lus.Pronostiqueurs_Pronostiqueur IS NOT NULL AND pronostiqueurs_connectes.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
						'					THEN	1' .
						'					WHEN	messages_non_lus.Pronostiqueurs_Pronostiqueur IS NOT NULL AND pronostiqueurs_connectes.Pronostiqueurs_Pronostiqueur IS NULL' .
						'					THEN	2' .
						'					WHEN	pronostiqueurs_connectes.Pronostiqueurs_Pronostiqueur IS NOT NULL' .
						'					THEN	3' .
						'					ELSE	4' .
						'				END AS Pronostiqueurs_Categorie' .
						'				,CASE WHEN pronostiqueurs_connectes.Pronostiqueurs_Pronostiqueur IS NOT NULL THEN 1 ELSE 0 END AS Pronostiqueurs_EstConnecte' .
						'	FROM		pronostiqueurs' .
						'	LEFT JOIN	(' .
						'					SELECT		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur, messages_lus.MessagesLus_NombreMessages' .
						'					FROM		tchat_groupes' .
						'					JOIN		tchat_groupes_membres' .
						'								ON		tchat_groupes.TchatGroupe = tchat_groupes_membres.TchatGroupes_TchatGroupe' .
						'					JOIN		messages_lus' .
						'								ON		tchat_groupes_membres.TchatGroupes_TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'					WHERE		tchat_groupes.TchatGroupes_TypeTchat = 0' .
						'								AND		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
						'								AND		messages_lus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'								AND		messages_lus.MessagesLus_NombreMessages > 0' .
						'				) messages_non_lus' .
						'				ON		pronostiqueurs.Pronostiqueur = messages_non_lus.Pronostiqueurs_Pronostiqueur' .
						'	LEFT JOIN	(' .
						'					SELECT		Pronostiqueurs_Pronostiqueur' .
						'					FROM		pronostiqueurs_activite' .
						'					WHERE		TIMESTAMPDIFF(SECOND, PronostiqueursActivite_Date, NOW()) < 30' .
						'				) pronostiqueurs_connectes' .
						'				ON		pronostiqueurs.Pronostiqueur = pronostiqueurs_connectes.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		pronostiqueurs.Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
						'	ORDER BY	Pronostiqueurs_Categorie, Pronostiqueurs_NomUtilisateur';

		$req = $bdd->query($ordreSQL);
		$pronostiqueurs = $req->fetchAll();

		$nombrePronostiqueurs = sizeof($pronostiqueurs);
		$NOMBRE_COLONNES = 3;
		$nombrePronostiqueursParColonne = ceil($nombrePronostiqueurs / $NOMBRE_COLONNES);
		
		// Parcours des pronostiqueurs
		if($nombrePronostiqueurs) {
			echo '<label class="titre">Tchat privé</label>';
			for($i = 0; $i < $NOMBRE_COLONNES; $i++) {
				if($i == $NOMBRE_COLONNES - 1)		echo '<div class="gauche">';
				else								echo '<div class="gauche" style="margin-right: 1.5em;">';
					for($j = 0; $j < $nombrePronostiqueursParColonne && $i * $nombrePronostiqueursParColonne + $j < $nombrePronostiqueurs; $j++) {
						$indice = $i * $nombrePronostiqueursParColonne + $j;
						$pronostiqueur = $pronostiqueurs[$indice]["Pronostiqueur"];
						$pronostiqueursNomUtilisateur = $pronostiqueurs[$indice]["Pronostiqueurs_NomUtilisateur"];
						$pronostiqueursEstConnecte = $pronostiqueurs[$indice]["Pronostiqueurs_EstConnecte"];
						$nombreMessagesNonLus = $pronostiqueurs[$indice]["MessagesLus_NombreMessages"];
						$classe = 'lien nom-pronostiqueur ';
						if($pronostiqueursEstConnecte == 1)
							$classe .= 'lien--actif texte-gras ';
						else
							$classe .= 'texte-italique ';
						
						
						if($nombreMessagesNonLus != null && $nombreMessagesNonLus > 0)
							echo '<label class="' . $classe . 'texte-gras" onclick="moduleTchat_creerConversation(\'' . $pronostiqueurs[$indice]["Pronostiqueurs_NomUtilisateur"] . '\');">' . $pronostiqueursNomUtilisateur . ' (' . $nombreMessagesNonLus . ')</label>';
						else
							echo '<label class="' . $classe . '" onclick="moduleTchat_creerConversation(\'' . $pronostiqueurs[$indice]["Pronostiqueurs_NomUtilisateur"] . '\');">' . $pronostiqueursNomUtilisateur . '</label>';
					}
				echo '</div>';
			}
		}
		
	echo '</div>';
	echo '<div class="colle-gauche"></div>';


?>