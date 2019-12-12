<?php
	include_once('commun_administrateur.php');

	// Transfert d'un joueur d'une équipe
	// Soit le joueur va dans un autre championnat, soit il va dans une autre équipe
	// Dans les deux cas, la liste des équipes contient toutes les équipes ainsi qu'une équipe fictive qui correspond à un championnat hors scope

	// Si le paramètre joueur vaut 0, c'est qu'il s'agit d'une création de joueur

	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : null;

	// Affichage de la liste des équipes
	$ordreSQL =		'	SELECT		DISTINCT equipes.Equipe, equipes.Equipes_Nom' .
					'	FROM		(' .
					'					SELECT		1 AS Ordre, Equipe, IFNULL(Equipes_NomCourt, Equipes_Nom) AS Equipes_Nom' .
					'					FROM		equipes' .
					'					UNION' .
					'					SELECT		0 AS Ordre, 0 AS Equipe, \'Hors concours\' AS Equipes_Nom' .
					'				) equipes' .
					'	LEFT JOIN	(' .
					'					SELECT		DISTINCT joueurs_equipes.Equipes_Equipe' .
					'					FROM		joueurs_equipes' .
					'					WHERE		joueurs_equipes.Joueurs_Joueur = ' . $joueur .
					'								AND		joueurs_equipes.JoueursEquipes_Fin IS NULL' .
					'				) joueurs_equipes' .
					'				ON		equipes.Equipe = joueurs_equipes.Equipes_Equipe' .
					'	WHERE		joueurs_equipes.Equipes_Equipe IS NULL' .
					'	ORDER BY	equipes.Ordre, equipes.Equipes_Nom';

	$req = $bdd->query($ordreSQL);

	echo '<div id="divGererEffectifTransfert">';
		echo '<div id="divEquipesTransfert">';
			echo '<label class="gauche">Equipes</label><br />';
			echo '<select id="selectEquipesTransfert" size="35">';
				//echo '<option value="0" selected="selected">Hors concours</option>';
				while($donnees = $req->fetch())
					echo '<option value="' . $donnees["Equipe"] . '">' . $donnees["Equipes_Nom"] . '</option>';
				$req->closeCursor();
			echo '</select>';
		echo '</div>';

		// Affichage d'un composant date pour que l'utilisateur choisisse la date à laquelle le transfert est effectif
		echo '<div id="divDateTransfert">';
			echo '<br /><label>Date effective du transfert</label><br />';
			echo '<input class="date" id="dateDebutTransfert" type="text" value="' . date('d/m/Y') . '" onchange=""/>';
		echo '</div>';
	echo '</div>';

?>

<script>
	$(function() {
		$('.date').datepicker({dateFormat: 'dd/mm/yy'});
	});


</script>