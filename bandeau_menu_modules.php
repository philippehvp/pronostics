<?php
	// Module d'affichage des modules liés au concours (classement général, journée, pronostics et résultats)

	include_once('commun.php');

	// if($_SESSION["administrateur"] == 1)
	// 	// Nombre de championnats différents
	// 	$ordreSQL =		'	SELECT		DISTINCT Championnat, Championnats_NomCourt, ModulesGroupes_Actif' .
	// 					'	FROM		championnats' .
	// 					'	LEFT JOIN	modules_groupes' .
	// 					'				ON		championnats.Championnat = modules_groupes.ModulesGroupes_Parametre' .
	// 					'						AND		modules_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];

	// else
	// 	// Nombre de championnats différents auxquels le pronostiqueur est inscrit
	// 	$ordreSQL =		'	SELECT			DISTINCT championnats.Championnat, championnats.Championnats_NomCourt, modules_groupes.ModulesGroupes_Actif' .
	// 								'	FROM				championnats' .
	// 								'	LEFT JOIN		inscriptions' .
	// 								'							ON		inscriptions.Championnats_Championnat = championnats.Championnat' .
	// 								'										AND		inscriptions.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
	// 								'	LEFT JOIN		modules_groupes' .
	// 								'							ON		championnats.Championnat = modules_groupes.ModulesGroupes_Parametre' .
	// 								'										AND		modules_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
	// 								'	WHERE				championnats.Championnat <> 4';

	$ordreSQL =		'	SELECT		DISTINCT Championnat, Championnats_NomCourt, ModulesGroupes_Actif' .
								'	FROM		championnats' .
								'	LEFT JOIN	modules_groupes' .
								'				ON		championnats.Championnat = modules_groupes.ModulesGroupes_Parametre' .
								'						AND		modules_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"];

	$req = $bdd->query($ordreSQL);
	$championnats = $req->fetchAll();
	$nombreChampionnats = sizeof($championnats);

	if($nombreChampionnats) {
		echo '<div>';
			$nombreChampionnatsAffiches = 0;

			foreach($championnats as $unChampionnat) {
				if($nombreChampionnatsAffiches == 0)
					echo '<div class="groupe-menu gauche">';

				$ordreSQL =		'	SELECT		Module, Modules_NomCourt, Modules_Parametre, ModulesPronostiqueurs_Actif' .
								'	FROM		modules' .
								'	JOIN		championnats' .
								'				ON		modules.Modules_Parametre = championnats.Championnat' .
								'	LEFT JOIN	modules_pronostiqueurs' .
								'				ON		modules.Module = modules_pronostiqueurs.Modules_Module' .
								'						AND		modules_pronostiqueurs.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
								'	WHERE		modules.Modules_Type >= 1' .
								'				AND		modules.Modules_Type <= 7' .
								'				AND		championnats.Championnat = ' . $unChampionnat["Championnat"] .
								'	ORDER BY	Modules_Type';

				$req = $bdd->query($ordreSQL);
				$modules = $req->fetchAll();
				$nombreModules = sizeof($modules);

				$checked = ($unChampionnat["ModulesGroupes_Actif"] == 1) ? 'checked' : '';
				echo '<label class="titre"><input style="margin-right: 10px;" type="checkbox" id="cbChampionnat_' . $unChampionnat["Championnat"] . '" onclick="basculerEtatGroupe(' . $unChampionnat["Championnat"] . ', \'cbChampionnat_' . $unChampionnat["Championnat"] . '\');" ' . $checked . ' />' . $unChampionnat["Championnats_NomCourt"] . '</label>';
				for($i = 0; $i < $nombreModules; $i++) {
					if($modules[$i]["ModulesPronostiqueurs_Actif"] == 1)
						$classe = 'lien--actif';
					else
						$classe = 'lien--inactif';

					//echo '<label class="lien ' . $classe . ' divModule' . $modules[$i]["Module"] . $modules[$i]["Modules_Parametre"] . '" onclick="basculerEtat(\'divModule' . $modules[$i]["Module"] . $modules[$i]["Modules_Parametre"] . '\'); afficherMasquerModule(' . $modules[$i]["Module"] . ', \'divModule' . $modules[$i]["Module"] . '\', \'' . $modules[$i]["Modules_Parametre"] . '\');">' . $modules[$i]["Modules_NomCourt"] . '</label>';
					echo '<label class="lien ' . $classe . ' divModule' . $modules[$i]["Module"] . $modules[$i]["Modules_Parametre"] . '" onclick="basculerEtat(\'cbChampionnat_' . $unChampionnat["Championnat"] . '\', \'divModule' . $modules[$i]["Module"] . $modules[$i]["Modules_Parametre"] . '\', ' . $modules[$i]["Module"] . ', \'divModule' . $modules[$i]["Module"] . '\', \'' . $modules[$i]["Modules_Parametre"] . '\');"><span>' . $modules[$i]["Modules_NomCourt"] . '</span></label>';
				}

				$nombreChampionnatsAffiches++;
				if($nombreChampionnatsAffiches == 1) {
					$nombreChampionnatsAffiches = 0;
					echo '</div>';
				}
			}
		echo '</div>';
	}

?>

<script>
	// Lors du basculement d'état entre affiché et masqué pour un module du concours, il faut faire refléter l'état dans le nom du module
	function basculerEtat(identifiantGroupe, identifiantLigne, module, nomConteneur, parametre) {
		var groupeActif = 0;
		if($('#' + identifiantGroupe).prop('checked') == true)
			groupeActif = 1;

		var moduleActif = 0;
		if($('.' + identifiantLigne).hasClass('lien--actif')) {
			$('.' + identifiantLigne).removeClass('lien--actif');
			$('.' + identifiantLigne).addClass('lien--inactif');
		}
		else {
			moduleActif = 1;
			$('.' + identifiantLigne).removeClass('lien--inactif');
			$('.' + identifiantLigne).addClass('lien--actif');
		}

		basculerEtatModule(groupeActif, moduleActif, module, nomConteneur, parametre);
	}

	// Basculement d'un groupe de modules d'un état à l'autre
	function basculerEtatGroupe(groupe, identifiantGroupe) {
		var groupeActif = 0;
		if($('#' + identifiantGroupe).prop('checked') == true)
			groupeActif = 1;

		basculerEtatGroupeModule(groupeActif, groupe);
	}

</script>