<?php
	include('commun.php');

	// Chargement d'un module
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	
	
	$ordreSQL =		'	SELECT		Module, Modules_Nom, Modules_Conteneur, Modules_Page, Modules_Javascript' .
					'				,IFNULL(PronostiqueursModules_X, Modules_X) AS PronostiqueursModules_X' .
					'				,IFNULL(PronostiqueursModules_Y, Modules_Y) AS PronostiqueursModules_Y' .
					//'				,PronostiqueursModules_Largeur, PronostiqueursModules_Hauteur' .
					'	FROM		cdm_pronostiqueurs_modules' .
					'	JOIN		cdm_modules' .
					'				ON		Modules_Module = Module' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'				AND		Module = ' . $module;
					
	$req = $bdd->query($ordreSQL);
	$modules = $req->fetchAll();

	foreach($modules as $unModule) {
		// Création du conteneur dans la page en cours pour chaque module activé
		echo '<div id="' . $unModule["Modules_Conteneur"] . '">';
			include($unModule["Modules_Page"]);
			
			// Appel d'une fonction Javascript si celle-ci a été spécifiée
			if($unModule["Modules_Javascript"]) {
				$javascript = $unModule["Modules_Javascript"];
				$module = $unModule["Module"];
				$nomConteneur = $unModule["Modules_Conteneur"];
				$x = $unModule["PronostiqueursModules_X"];
				$y = $unModule["PronostiqueursModules_Y"];
			
				echo '<script>';
					echo $javascript . '(' . $module . ', \'' . $nomConteneur . '\', ' . $x . ', ' . $y . ');';
				echo '</script>';
			}
		echo '</div>';
	}
?>