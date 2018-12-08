<?php
	include_once('commun.php');
	
	$nomMenu = isset($_POST["nomMenu"]) ? $_POST["nomMenu"] : '';

	// Tchats de groupe créés par l'utilisateur
	function lireTchatGroupeProprietaire($bdd, $nomMenu) {
		$ordreSQL =		'	SELECT		TchatGroupe, TchatGroupes_Nom, MessagesLus_NombreMessages' .
						'	FROM		tchat_groupes' .
						'	LEFT JOIN	messages_lus' .
						'				ON		tchat_groupes.TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'						AND		tchat_groupes.Pronostiqueurs_Pronostiqueur = messages_lus.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		tchat_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes.TchatGroupes_TypeTchat = 1';

		$req = $bdd->query($ordreSQL);
		$tchatGroupes = $req->fetchAll();
		$nombreTchatGroupes = sizeof($tchatGroupes);

		$nombreMessages = 0;
		if($nombreTchatGroupes != 0) {
			echo '<hr />';
			echo '<label class="titre">Groupes que vous avez créés</label>';
			echo '<table class="tchatGroupe">';
				echo '<thead>';
					echo '<tr class="bordure-basse">';
						echo '<th>Supprimer</th>';
						echo '<th>Groupes</th>';
						echo '<th>Membres</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';

					$i = 0;
					foreach($tchatGroupes as $unTchatGroupe) {
						// Lecture des membres du groupe
						$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Pronostiqueurs_NomUtilisateur' .
										'	FROM		tchat_groupes_membres' .
										'	LEFT JOIN	pronostiqueurs' .
										'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
										'	WHERE		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
										'				AND		TchatGroupes_TchatGroupe = ' . $unTchatGroupe["TchatGroupe"];

						$req = $bdd->query($ordreSQL);
						$membres = $req->fetchAll();
						$nombreMessagesNonLus = $unTchatGroupe["MessagesLus_NombreMessages"];
						$nombreMessages += $nombreMessagesNonLus;
						echo '<tr id="tchatGroupe' . $i++ . '">';
							echo '<td onclick="moduleTchatGroupe_supprimerTchatGroupe(' . $unTchatGroupe["TchatGroupe"] . ', \'tchatGroupe' . $i++ . '\');">Supprimer</td>';
							if($nombreMessagesNonLus == 0)
								echo '<td onclick="modules_afficherModule(50, \'divModule50\', ' . $unTchatGroupe["TchatGroupe"] . ')"><strong>' . $unTchatGroupe["TchatGroupes_Nom"] . '</strong></td>';
							else
								echo '<td class="rouge" onclick="modules_afficherModule(50, \'divModule50\', ' . $unTchatGroupe["TchatGroupe"] . ')"><strong>' . $unTchatGroupe["TchatGroupes_Nom"] . ' (' . $nombreMessagesNonLus . ')</strong></td>';
							echo '<td style="overflow: hidden; width: 300px;" title="' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '">' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '</td>';
						echo '</tr>';
					}
				echo '</tbody>';

			echo '</table>';
		}
		return $nombreMessages;
	}


	// Affichage des tchats de groupe auxquels participe le pronostiqueur
	// On exclut le tchat public
	// On en profite pour regarder dans la table messages_lus le nombre de messages qui auraient été postés depuis la dernière fois où il n'a pas ouvert ce tchat de groupe en particulier
	function lireTchatGroupeParticipant($bdd) {
		$ordreSQL =		'	SELECT		Pronostiqueurs_NomUtilisateur, TchatGroupe, TchatGroupes_Nom, MessagesLus_NombreMessages' .
						'	FROM		tchat_groupes_membres' .
						'	JOIN		tchat_groupes' .
						'				ON		tchat_groupes_membres.TchatGroupes_TchatGroupe = tchat_groupes.TchatGroupe' .
						'	JOIN		pronostiqueurs' .
						'				ON		tchat_groupes.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		messages_lus' .
						'				ON		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = messages_lus.Pronostiqueurs_Pronostiqueur' .
						'						AND		tchat_groupes_membres.TchatGroupes_TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'	WHERE		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes.Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes_membres.TchatGroupes_TchatGroupe <> 1' .
						'				AND		tchat_groupes.TchatGroupes_TypeTchat = 1';

		$req = $bdd->query($ordreSQL);
		$discussions = $req->fetchAll();
		$nombreDiscussions = sizeof($discussions);
		
		$nombreMessages = 0;
		if($nombreDiscussions != 0) {
			echo '<hr />';
			echo '<label class="titre">Groupes auxquels vous participez</label>';
			echo '<table class="tchatGroupe">';
				echo '<thead>';
					echo '<tr class="bordure-basse">';
						echo '<th>Créateur</th>';
						echo '<th>Groupes</th>';
						echo '<th>Membres</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					foreach($discussions as $uneDiscussion) {
						// Lecture des membres du groupe
						$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Pronostiqueurs_NomUtilisateur' .
										'	FROM		tchat_groupes_membres' .
										'	JOIN		pronostiqueurs' .
										'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
										'	WHERE		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
										'				AND		TchatGroupes_TchatGroupe = ' . $uneDiscussion["TchatGroupe"];
										
						$req = $bdd->query($ordreSQL);
						$membres = $req->fetchAll();
						$nombreMessagesNonLus = $uneDiscussion["MessagesLus_NombreMessages"];
						$nombreMessages += $nombreMessagesNonLus;
						echo '<tr>';
							echo '<td>' . $uneDiscussion["Pronostiqueurs_NomUtilisateur"] . '</td>';
							if($nombreMessagesNonLus == 0)
								echo '<td onclick="modules_afficherModule(50, \'divModule50\', ' . $uneDiscussion["TchatGroupe"] . ')"><strong>' . $uneDiscussion["TchatGroupes_Nom"] . '</strong></td>';
							else
								echo '<td class="rouge" onclick="modules_afficherModule(50, \'divModule50\', ' . $uneDiscussion["TchatGroupe"] . ')"><strong>' . $uneDiscussion["TchatGroupes_Nom"] . ' (' . $nombreMessagesNonLus . ')</strong></td>';
							echo '<td style="overflow: hidden; width: 300px;" title="' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '">' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '</td>';
						echo '</tr>';
					}
				echo '</tbody>';
					
			echo '</table>';
		}
		
		return $nombreMessages;
	}	
	
	echo '<div>';
		echo '<img class="curseur-main" onclick="moduleTchatGroupe_creerTchatGroupe(\'txtNomTchatGroupe\', \'taPronostiqueurs\');" src="images/tchat_groupe.png" alt="" title="Nouveau tchat de groupe" />';

		echo '<div>';
			lireTchatGroupeProprietaire($bdd, $nomMenu);
		echo '</div>';

		echo '<div>';
			lireTchatGroupeParticipant($bdd);
		echo '</div>';
	echo '</div>';
?>
