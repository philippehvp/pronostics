<?php

	// Page d'affichage de la liste des équipes selon un critère de recherche

	// La page peut être appelée de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	$rafraichissement = isset($_POST["rafraichissement"]) ? $_POST["rafraichissement"] : 0;
	if($rafraichissement == 1) {
		// Rafraîchissement de la page
		include_once('commun_administrateur.php');

		// Lecture des paramètres passés à la page
		$nomEquipe = isset($_POST["nomEquipe"]) ? $_POST["nomEquipe"] : '';
	}
	else {
		$nomEquipe = '';
	}

	$ordreSQL =		'	SELECT		DISTINCT Equipe, Equipes_Nom, IFNULL(Equipes_NomCourt, \'-\') AS Equipes_NomCourt, Equipes_Fanion' .
					'				,CASE WHEN l1.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS Equipes_L1' .
					'				,IFNULL(Equipes_L1Europe, 0) AS Equipes_L1Europe' .
					'				,CASE WHEN ldc.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS Equipes_LDC' .
					'				,CASE WHEN el.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS Equipes_EL' .
					'				,CASE WHEN barrages.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS Equipes_Barrages' .
					'				,CASE WHEN cdf.Equipes_Equipe IS NOT NULL THEN 1 ELSE 0 END AS Equipes_CDF' .
					'	FROM		equipes' .
					'	LEFT JOIN	engagements l1' .
					'				ON		equipes.Equipe = l1.Equipes_Equipe' .
					'						AND		l1.Championnats_Championnat = 1' .
					'	LEFT JOIN	engagements ldc' .
					'				ON		equipes.Equipe = ldc.Equipes_Equipe' .
					'						AND		ldc.Championnats_Championnat = 2' .
					'	LEFT JOIN	engagements el' .
					'				ON		equipes.Equipe = el.Equipes_Equipe' .
					'						AND		el.Championnats_Championnat = 3' .
					'	LEFT JOIN	engagements barrages' .
					'				ON		equipes.Equipe = barrages.Equipes_Equipe' .
					'						AND		barrages.Championnats_Championnat = 4' .
					'	LEFT JOIN	engagements cdf' .
					'				ON		equipes.Equipe = cdf.Equipes_Equipe' .
					'						AND		cdf.Championnats_Championnat = 5' .
					'	WHERE		Equipes_Nom LIKE \'%' . $nomEquipe . '%\'' .
					'				OR		Equipes_NomCourt LIKE \'%' . $nomEquipe . '%\'' .
					'	ORDER BY	Equipes_Nom';

	$req = $bdd->query($ordreSQL);
	$equipes = $req->fetchAll();

	if(count($equipes) == 0) {
		echo '<label>Aucune donnée à afficher</label>';
	}
	else {
		echo '<table class="tableau--liste">';
			echo '<thead>';
				echo '<th>Identifiant</th>';
				echo '<th>Nom</th>';
				echo '<th>Nom court</th>';
				echo '<th>Fanion</th>';
				echo '<th>L1</th>';
				echo '<th>L1 Europe</th>';
				echo '<th>LDC</th>';
				echo '<th>EL</th>';
				echo '<th>Barrages</th>';
				echo '<th>CDF</th>';
			echo '</thead>';

			echo '<tbody>';
				foreach($equipes as $uneEquipe) {
					echo '<tr>';
						$l1 = $uneEquipe["Equipes_L1"] == 1 ? ' checked' : '';
						$l1Europe = $uneEquipe["Equipes_L1Europe"] == 1 ? ' checked' : '';
						$ldc = $uneEquipe["Equipes_LDC"] == 1 ? ' checked' : '';
						$el = $uneEquipe["Equipes_EL"] == 1 ? ' checked' : '';
						$barrages = $uneEquipe["Equipes_Barrages"] == 1 ? ' checked' : '';
						$cdf = $uneEquipe["Equipes_CDF"] == 1 ? ' checked' : '';

						echo '<td class="aligne-centre">' . $uneEquipe["Equipe"] . '</td>';
						echo '<td><input type="text" id="txtEquipeNom_' . $uneEquipe["Equipe"] . '" value="' . $uneEquipe["Equipes_Nom"] . '" onchange="gererEquipe_modifierEquipe($(this), ' . $uneEquipe["Equipe"] . ', 0);" /></td>';
						echo '<td><input type="text" id="txtEquipeNomCourt_' . $uneEquipe["Equipe"] . '" value="' . $uneEquipe["Equipes_NomCourt"] . '" onchange="gererEquipe_modifierEquipe($(this), ' . $uneEquipe["Equipe"] . ', 1);" /></td>';
						echo '<td>';
                            echo '<input type="text" id="txtEquipeFanion_' . $uneEquipe["Equipe"] . '" value="' . $uneEquipe["Equipes_Fanion"] . '" onchange="gererEquipe_modifierEquipe($(this), ' . $uneEquipe["Equipe"] . ', 2);" />';
                            echo '&nbsp;<label for="ficFanion_' . $uneEquipe["Equipe"] . '" class="bouton">Fanion</label>';
                            echo '<input id="ficFanion_' . $uneEquipe["Equipe"] . '" name="ficFanion_' . $uneEquipe["Equipe"] . '" type="file" style="display: none;" />';
                        echo '</td>';
						echo '<td class="aligne-centre"><input type="checkbox" id="cbL1_' . $uneEquipe["Equipe"] . '"' . $l1 . ' onclick="gererEquipe_modifierEngagement($(this), ' . $uneEquipe["Equipe"] . ', 1, 0);" /></td>';
						echo '<td class="aligne-centre"><input type="checkbox" id="cbL1Europe_' . $uneEquipe["Equipe"] . '"' . $l1Europe . ' onclick="gererEquipe_modifierEngagement($(this), ' . $uneEquipe["Equipe"] . ', 1, 1);" /></td>';
						echo '<td class="aligne-centre"><input type="checkbox" id="cbLDC_' . $uneEquipe["Equipe"] . '"' . $ldc . ' onclick="gererEquipe_modifierEngagement($(this), ' . $uneEquipe["Equipe"] . ', 2, 0);" /></td>';
						echo '<td class="aligne-centre"><input type="checkbox" id="cbEL_' . $uneEquipe["Equipe"] . '"' . $el . ' onclick="gererEquipe_modifierEngagement($(this), ' . $uneEquipe["Equipe"] . ', 3, 0);" /></td>';
						echo '<td class="aligne-centre"><input type="checkbox" id="cbBarrages_' . $uneEquipe["Equipe"] . '"' . $barrages . ' onclick="gererEquipe_modifierEngagement($(this), ' . $uneEquipe["Equipe"] . ', 4, 0);" /></td>';
						echo '<td class="aligne-centre"><input type="checkbox" id="cbCDF_' . $uneEquipe["Equipe"] . '"' . $cdf . ' onclick="gererEquipe_modifierEngagement($(this), ' . $uneEquipe["Equipe"] . ', 5, 0);" /></td>';

					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}

?>


<script>
    $(function() {
        $('input[type=file]').change(function() {
            var fichier = $(this)[0].files;
            var equipe = $(this).attr('id').replace(/\D+/g, '');
            gererEquipe_chargerFanion(fichier, equipe, 'txtEquipeFanion_' + equipe);
        });
    });
</script>