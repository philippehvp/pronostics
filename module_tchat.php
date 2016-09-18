<?php
	// Module de tchat
	
	// Le module peut être appelé de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)
	
	$rafraichissementModule = isset($_POST["rafraichissementModule"]) ? $_POST["rafraichissementModule"] : 0;
	if($rafraichissementModule == 1) {
		// Rafraîchissement automatique du module
		include('commun.php');

		// Nom du div dans lequel se trouve le module
		$nomConteneur = isset($_POST["nomConteneur"]) ? $_POST["nomConteneur"] : '';
		
		// Lecture des paramètres passés à la page
		$tchatGroupe = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	}
	else
		$tchatGroupe = $parametre;		// Paramètre du module

?>	
	
<script type="text/javascript">
	<?php
		// Lecture de l'identifiant du dernier message
		$ordreSQL =	'	SELECT		Message' .
					'	FROM		messages' .
					'	WHERE		TchatGroupes_TchatGroupe = ' . $tchatGroupe .
					'	ORDER BY	Message DESC' .
					'	LIMIT 1';
				
		$req = $bdd->query($ordreSQL);
		$messages = $req->fetchAll();
		if(sizeof($messages) > 0)
			$dernierMessage = $messages[0]["Message"];
		else
			$dernierMessage = 0;

	?>
	var dernierMessage = <?php echo $dernierMessage;?>;
</script>

<?php
	echo '<div id="divTchat" class="module--tchat-messages">';
		$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, Messages_Date, Messages_Message, Pronostiqueurs_CodeCouleur' .
						'	FROM		messages' .
						'	JOIN		pronostiqueurs' .
						'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
						'	JOIN		tchat_groupes' .
						'				ON		messages.TchatGroupes_TchatGroupe = tchat_groupes.TchatGroupe' .
						'	JOIN		tchat_groupes_membres' .
						'				ON		tchat_groupes.TchatGroupe = tchat_groupes_membres.TchatGroupes_TchatGroupe' .
						'	WHERE		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes.TchatGroupe = ' . $tchatGroupe .
						'	ORDER BY	Messages_Date DESC' .
						'	LIMIT 300';

		$req = $bdd->query($ordreSQL);
		$messages = $req->fetchAll();
		$nombreMessages = sizeof($messages);
		
		if($nombreMessages) {
			foreach($messages as $unMessage) {
				// Lecture de la couleur
				$codeCouleur = isset($unMessage["Pronostiqueurs_CodeCouleur"]) ? $unMessage["Pronostiqueurs_CodeCouleur"] : '#ffffff';
				$messageAAfficher = $unMessage["Messages_Message"];
				
				echo '<p style="color: ' . $codeCouleur . ';">';
					// Si le message date d'aujourd'hui, on affiche uniquement l'heure
					// Sinon, on affiche le jour et l'heure
					if(date('Ymd') == date('Ymd', strtotime($unMessage["Messages_Date"])))
						$dateAffichee = date("H:i", strtotime($unMessage["Messages_Date"]));
					else
						$dateAffichee = date("d/m H:i", strtotime($unMessage["Messages_Date"]));
					
				
					if($unMessage["Pronostiqueur"] == $_SESSION["pronostiqueur"])
						echo '(' . $dateAffichee . ') <strong>Moi</strong> :&nbsp;';
					else
						echo '(' . $dateAffichee . ') <strong>' . $unMessage["Pronostiqueurs_NomUtilisateur"] . '</strong>  :&nbsp;';
					echo htmlentities($messageAAfficher);
				echo '</p>';
			}
			
			// On indique que l'on a lu tous les messages de ce tchat de groupe
			// On exclut le tchat public
			if($tchatGroupe != 1) {
				$ordreSQL =		'	UPDATE		messages_lus' .
								'	SET			MessagesLus_NombreMessages = 0' .
								'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'				AND		TchatGroupes_TchatGroupe = ' . $tchatGroupe;
				$req = $bdd->exec($ordreSQL);
			}
		}

	echo '</div>';
?>

<script>
	$(function() {
		$('.bouton').button();
		$('#divTchat').emoticonize({animate: false});
	});
</script>

