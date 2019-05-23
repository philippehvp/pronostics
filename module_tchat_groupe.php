<?php
	// Module de gestion des groupes de tchat
	
	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)
	
	// Le module affiche les données suivantes :
	// - conversations de l'utilisateur (qu'il soit à l'origine ou non de la discussion)
	// - tchats de groupe créés par l'utilisateur
	// - tchats de groupe auxquels il participe
	
	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include_once('commun.php');
		
		$module = isset($_POST["module"]) ? $_POST["module"] : 0;
		$nomConteneurSimple = isset($_POST["nomConteneurSimple"]) ? $_POST["nomConteneurSimple"] : 0;
		$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	}
	
	// Tchats de groupe créés par l'utilisateur
	function lireTchatGroupeProprietaire($bdd, $module, $nomConteneurSimple, $parametre) {
		$ordreSQL =		'	SELECT		TchatGroupe, TchatGroupes_Nom, MessagesLus_NombreMessages' .
						'	FROM		tchat_groupes' .
						'	JOIN		messages_lus' .
						'				ON		tchat_groupes.TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'						AND		tchat_groupes.Pronostiqueurs_Pronostiqueur = messages_lus.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		tchat_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes.TchatGroupes_TypeTchat = 1';

		$req = $bdd->query($ordreSQL);
		$tchatGroupes = $req->fetchAll();
		$nombreTchatGroupes = sizeof($tchatGroupes);
		
		$nombreMessages = 0;
		if($nombreTchatGroupes != 0) {
			echo '<table class="classementModule">';
				echo '<thead>';
					echo '<tr><th colspan="3">Groupes de tchat créés par vous</tr>';
					echo '<tr class="bordure-basse">';
						echo '<th>Supprimer</th>';
						echo '<th>Groupes</th>';
						echo '<th>Membres</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					
					foreach($tchatGroupes as $unTchatGroupe) {
						// Lecture des membres du groupe
						$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Pronostiqueurs_NomUtilisateur' .
										'	FROM		tchat_groupes_membres' .
										'	JOIN		pronostiqueurs' .
										'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
										'	WHERE		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
										'				AND		TchatGroupes_TchatGroupe = ' . $unTchatGroupe["TchatGroupe"];
										
						$req = $bdd->query($ordreSQL);
						$membres = $req->fetchAll();
						$nombreMessagesNonLus = $unTchatGroupe["MessagesLus_NombreMessages"];
						$nombreMessages += $nombreMessagesNonLus;
						echo '<tr>';
							echo '<td class="lienActif" onclick="moduleTchatGroupe_supprimerTchatGroupe(' . $unTchatGroupe["TchatGroupe"] . ', ' . $module . ', \'' . $nomConteneurSimple . '\', ' . $parametre . ');">Supprimer</td>';
							if($nombreMessagesNonLus == 0)
								echo '<td class="lienActif" onclick="modules_afficherModule(50, \'divModule50\', ' . $unTchatGroupe["TchatGroupe"] . ')"><strong>' . $unTchatGroupe["TchatGroupes_Nom"] . '</strong></td>';
							else
								echo '<td class="lienActif rouge" onclick="modules_afficherModule(50, \'divModule50\', ' . $unTchatGroupe["TchatGroupe"] . ')"><strong>' . $unTchatGroupe["TchatGroupes_Nom"] . ' (' . $nombreMessagesNonLus . ')</strong></td>';
							echo '<td style="overflow: hidden;" title="' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '">' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '</td>';
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
	function lireTchatGroupeParticipant($bdd, $module, $nomConteneurSimple, $parametre) {
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
			echo '<table class="classementModule">';
				echo '<thead>';
					echo '<tr><th colspan="3">Groupes de tchat auxquels vous participez</tr>';
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
								echo '<td class="lienActif" onclick="modules_afficherModule(50, \'divModule50\', ' . $uneDiscussion["TchatGroupe"] . ')"><strong>' . $uneDiscussion["TchatGroupes_Nom"] . '</strong></td>';
							else
								echo '<td class="lienActif rouge" onclick="modules_afficherModule(50, \'divModule50\', ' . $uneDiscussion["TchatGroupe"] . ')"><strong>' . $uneDiscussion["TchatGroupes_Nom"] . ' (' . $nombreMessagesNonLus . ')</strong></td>';
							echo '<td style="overflow: hidden;" title="' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '">' . $membres[0]["Pronostiqueurs_NomUtilisateur"] . '</td>';
						echo '</tr>';
					}
				echo '</tbody>';
					
			echo '</table>';
		}
		
		return $nombreMessages;
	}
	
	
	$nombreMessagesNonLus = 0;
	echo '<div class="retractable">';
		$nombreMessagesNonLus = lireTchatGroupeProprietaire($bdd, $module, $nomConteneurSimple, $parametre);
	echo '</div>';
	echo '<div class="retractable">';
		$nombreMessagesNonLus += lireTchatGroupeParticipant($bdd, $module, $nomConteneurSimple, $parametre);
	echo '</div>';

?>

