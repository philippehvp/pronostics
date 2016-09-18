<?php
	// Module
	
	// La page des modules est affichée depuis la page d'accueil, ce qui signifie que tous les objets (base de données, etc.) sont déjà inclus et existants
	// La page est appelée de deux manières :
	// - à l'affichage de la page d'accueil
	// - à la demande de l'utilisateur (via un appel Ajax)
	// Le mode d'appel est déterminé par la présence d'un paramètre POST appelAjax
	
	// Le paramètre éventuellement passé au module peut être utilisé pour personnaliser l'appel à module
	// Par exemple, le module de tchat de groupe prend en paramètre un numéro de groupe de tchat
	// Dans le cas où un paramètre est passé dans l'appel au module, c'est lui qui devient prioritaire par rapport à celui qui a pu être saisi en base
	
	$appelAjax = isset($_POST["appelAjax"]) ? $_POST["appelAjax"] : 0;
	$module = isset($_POST["module"]) ? $_POST["module"] : 0;
	$parametreAppel = isset($_POST["parametre"]) ? $_POST["parametre"] : '';

	// Dans le cas de l'ouverture d'un module (non ouvert avant), il est nécessaire de ne modifier que ce module (les autres n'ont pas à être modifiés)
	$nomConteneurNouvellementCree = '';
	$nomConteneurFictif = '';
	
	$nomConteneurComplet = '';
	$nomConteneurSimple = '';
	
	if($appelAjax) {
		include('commun.php');
		
		$nomPage = isset($_POST["nomPage"]) ? $_POST["nomPage"] : '';
	}

	// Le nom de page indique quelle page est actuellement affichée
	// Pour déterminer si un module peut s'afficher sur chaque page, en table, la colonne Modules_PagesAutorisees contient les pages autorisées pour le module
	// Cette colonne peut contenir soit le mot toutes, soit le nom d'une page, soit le nom de plusieurs pages (séparés par un point-virgule)
	// A l'ouverture de la page, la variable $nomPage est connue
	// Une fois la page chargée, cette variable n'est plus accessible
	// Donc, la demande d'ouverture d'un module ne peut plus se baser sur cette variable
	// Toutefois, l'affichage d'un module passe par la fonction Javascript modules_afficherModule
	// Il suffit donc de sauvegarder dans une variable le nom de la page à chaque chargement et de demander à la fonction modules_afficherModule de passer cette variable
	// au module pour qu'il puisse savoir où l'on se trouve
	
	
	// Initialisation du module (opération effectuée de toute manière, quel que soit le type d'appel)
	function initialiserModule	(	$bdd
									,$module, $nom
									,&$nomConteneurComplet
									,&$nomConteneurSimple
									,&$nomConteneurNouvellementCree
									,$conteneurNouvellementCree
									,$zoneOptions
									,$parametre
									,$pageVerification
									,$page
									,$pageOptions
									,$x, $y
									,$largeur, $hauteur
									,$largeurMin, $hauteurMin
									,$contientModeRival
									,$modeRival
									,$contientModeConcurrentDirect
									,$modeConcurrentDirect
									,$rafraichissementAutomatique
									,$intervalleRafraichissement
									,$critereRafraichissement
									,$modeIncruste
									,$classeModule
								) {
		// Création d'une fenêtre pour chaque module
		$nomConteneurComplet = 'divModule' . $module . $parametre;
		$nomConteneurSimple = 'divModule' . $module;
		if($conteneurNouvellementCree == 1)
			$nomConteneurNouvellementCree = $nomConteneurComplet;

		// Lecture des paramètres visuels (position, taille, couleur, etc.) pour fabrication du style à appliquer pour le DIV
		// Il est possible d'avoir un module fenêtré ou en mode inscrusté dans la page (sans possibilité de déplacer le module)
		$style =	'position: absolute; left: ' . $x . 'px; top: ' . $y . 'px; width: ' . $largeur . 'px; height: ' . $hauteur . 'px; ';
			
		echo '<div id="' . $nomConteneurComplet . '" class="module ' . $classeModule . '" style="' . $style . '">';
			// Ici, les caractéristiques visuelles du module sont sauvegardées dans des zones cachées afin de les mettre en place via le Javascript après le chargement du module
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_NomConteneurSimple" value="divModule' . $module . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_Module" value="' . $module . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_PageVerification" value="' . $pageVerification . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_Page" value="' . $page . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_Parametre" value="' . $parametre . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_ModeRival" value="' . $modeRival . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_ModeConcurrentDirect" value="' . $modeConcurrentDirect . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_LargeurMin" value="' . $largeurMin . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_HauteurMin" value="' . $hauteurMin . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_ChangementTaille" value="0" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_IntervalleRafraichissement" value="0" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_CritereRafraichissement" value="' . $critereRafraichissement . '" />';
			echo '<input type="hidden" id="divModule' . $module . $parametre . '_ModeIncruste" value="' . $modeIncruste . '" />';
			
			// Affichage du titre du module
			echo '<div class="module--entete entete">';
				echo '<div class="module--titre">';
					// Ajout manuel de quelques espaces pour éviter d'utiliser le padding gauche
					echo '<div class="gauche"><label>&nbsp;&nbsp;' . $nom . '</label></div>';
				echo '</div>';

				echo '<div class="bouton-fermeture" onclick="masquerModule(' . $module . ', \'divModule' . $module . '\', ' . $parametre . ');"></div>';
				
				if($modeIncruste == 1)
					echo '<div class="bouton-epingle mode-incruste" onclick="epinglerModule(' . $module . ', \'divModule' . $module . '\', ' . $parametre . ');"></div>';
				else
					echo '<div class="bouton-epingle" onclick="epinglerModule(' . $module . ', \'divModule' . $module . '\', ' . $parametre . ');"></div>';
			echo '</div>';
			
			// Le module comporte-t-il une zone d'options ?
			if($zoneOptions == 1) {
			
				// La difficulté est de savoir, lorsque l'on veut ajouter une option, si d'autres options ont été ajoutées avant ou non
				// Si c'est le cas, il faut alors ajouter de l'espace à gauche
				$nombreOptions = 0;
			
				echo '<div class="module--options">';
					// Le module comporte-t-il le mode rival ?
					if($contientModeRival == 1) {
						$nombreOptions++;
						echo '<span>';
							echo '<input id="modeRival' . $module . $parametre . '" class="moderival" type="checkbox" name="modeRival' . $module . $parametre . '" value="' . $module . '"' . ($modeRival == 1 ? 'checked="checked"' : '') . ' />';
							echo '<label class="texte-petit" for="modeRival' . $module . $parametre . '">Rival</label>';
						echo '</span>';
					}
					

					// Le module comporte-t-il le mode concurrent direct ?
					if($contientModeConcurrentDirect == 1) {
						$nombreOptions++;
						if($nombreOptions > 0)				echo '<span style="margin-left: 15px;">';
						else								echo '<span>';
							echo '<input id="modeConcurrentDirect' . $module . $parametre . '" class="modeconcurrentdirect" type="checkbox" name="modeConcurrentDirect' . $module . $parametre . '" value="' . $module . '"' . ($modeConcurrentDirect == 1 ? 'checked="checked"' : '') . ' />';
							echo '<label class="texte-petit" for="modeConcurrentDirect' . $module . $parametre . '">Concurrents directs</label>';
						echo '</span>';
					}
					
					// Le module doit-il se rafraîchir automatiquement ?
					if($rafraichissementAutomatique == 1) {
						if($nombreOptions > 0)				echo '<span style="margin-left: 15px;">';
						else								echo '<span>';
							//echo '<label class="texte-petit">MAJ auto.</label>';
							echo '<select class="rafraichissement">';
								echo '<option value="0" ' . ($intervalleRafraichissement == 0 ? 'selected' : '') . '>-</option>';
								echo '<option value="3000" ' . ($intervalleRafraichissement == 3000 ? 'selected' : '') . '>3 sec</option>';
								echo '<option value="5000" ' . ($intervalleRafraichissement == 5000 ? 'selected' : '') . '>5 sec</option>';
								echo '<option value="10000" ' . ($intervalleRafraichissement == 10000 ? 'selected' : '') . '>10 sec</option>';
								echo '<option value="30000" ' . ($intervalleRafraichissement == 30000 ? 'selected' : '') . '>30 sec</option>';
								echo '<option value="60000" ' . ($intervalleRafraichissement == 60000 ? 'selected' : '') . '>1 min</option>';
								echo '<option value="180000" ' . ($intervalleRafraichissement == 180000 ? 'selected' : '') . '>3 min</option>';
								echo '<option value="300000" ' . ($intervalleRafraichissement == 300000 ? 'selected' : '') . '>5 min</option>';
							echo '</select>';
						echo '</span>';
					}
					
					// Le module contient-il une zone de texte ? (spécifique au tchat en direct)
					if($pageOptions != '') {
						// On insère un saut de ligne ssi la zone d'options comporte déjà des éléments
						if($contientModeRival == 1 || $rafraichissementAutomatique == 1 || $contientModeConcurrentDirect == 1)
							echo '<br />';
						
						// Ajout de la page d'options
						include($pageOptions);
					}
				echo '</div>';
			}

			echo '<div class="module--contenu">';
				include($page);
			echo '</div>';
			
		echo '</div>';
	}
	
	if($appelAjax == 0) {
		// Appel de la page depuis une inclusion classique
		// Lecture des informations de tous les modules actifs
		$ordreSQL =		'	SELECT		Module' .
						'				,Modules_Nom' .
						'				,Modules_ZoneOptions' .
						'				,Modules_X' .
						'				,Modules_Y' .
						'				,Modules_Largeur' .
						'				,Modules_Hauteur' .
						'				,Modules_LargeurMin' .
						'				,Modules_HauteurMin' .
						'				,Modules_PageVerification' .
						'				,Modules_Page' .
						'				,Modules_PageOptions' .
						'				,ModulesPronostiqueurs_Actif' .
						'				,Modules_Parametre' .
						'				,Modules_ModeRival' .
						'				,ModulesPronostiqueurs_ModeRival' .
						'				,Modules_ModeConcurrentDirect' .
						'				,ModulesPronostiqueurs_ModeConcurrentDirect' .
						'				,Modules_RafraichissementAutomatique' .
						'				,ModulesPronostiqueurs_IntervalleRafraichissement' .
						'				,Modules_CritereRafraichissement' .
						'				,ModulesPronostiqueurs_ModeIncruste' .
						'				,Modules_Classe' .
						'	FROM		(' .
						'					SELECT		Module' .
						'								,Modules_Nom' .
						'								,IFNULL(Modules_ZoneOptions, 0) AS Modules_ZoneOptions' .
						'								,IFNULL(ModulesPronostiqueurs_X, Modules_X) AS Modules_X' .
						'								,IFNULL(ModulesPronostiqueurs_Y, Modules_Y) AS Modules_Y' .
						'								,IFNULL(ModulesPronostiqueurs_Largeur, Modules_Largeur) AS Modules_Largeur' .
						'								,IFNULL(ModulesPronostiqueurs_Hauteur, Modules_Hauteur) AS Modules_Hauteur' .
						'								,Modules_LargeurMin' .
						'								,Modules_HauteurMin' .
						'								,Modules_PageVerification' .
						'								,Modules_Page' .
						'								,IFNULL(Modules_PageOptions, \'\') AS Modules_PageOptions' .
						'								,ModulesPronostiqueurs_Actif' .
						'								,ModulesPronostiqueurs_Parametre AS Modules_Parametre' .
						'								,IFNULL(Modules_ModeRival, 0) AS Modules_ModeRival' .
						'								,IFNULL(ModulesPronostiqueurs_ModeRival, 0) AS ModulesPronostiqueurs_ModeRival' .
						'								,IFNULL(Modules_ModeConcurrentDirect, 0) AS Modules_ModeConcurrentDirect' .
						'								,IFNULL(ModulesPronostiqueurs_ModeConcurrentDirect, 0) AS ModulesPronostiqueurs_ModeConcurrentDirect' .
						'								,IFNULL(Modules_RafraichissementAutomatique, 0) AS Modules_RafraichissementAutomatique' .
						'								,IFNULL(ModulesPronostiqueurs_IntervalleRafraichissement, 0) AS ModulesPronostiqueurs_IntervalleRafraichissement' .
						'								,IFNULL(Modules_CritereRafraichissement, \'\') AS Modules_CritereRafraichissement' .
						'								,IFNULL(ModulesPronostiqueurs_ModeIncruste, 0) AS ModulesPronostiqueurs_ModeIncruste' .
						'								,IFNULL(Modules_Classe, \'\') AS Modules_Classe' .
						'								,Modules_PagesAutorisees' .
						'					FROM		modules' .
						'					LEFT JOIN	modules_pronostiqueurs' .
						'								ON		Module = Modules_Module' .
						'					LEFT JOIN	modules_groupes' .
						'								ON		modules_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'										AND		modules.Modules_Parametre = modules_groupes.ModulesGroupes_Parametre' .
						'					WHERE		modules_pronostiqueurs.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'								AND		(	(	Modules_Type >= 50' .
						'												AND		ModulesPronostiqueurs_Actif = 1' .
						'											)' .
						'											OR' .
						'											(	Modules_Type < 50' .
						'												AND		ModulesGroupes_Actif = 1' .
						'												AND		ModulesPronostiqueurs_Actif = 1' .
						'											)' .
						'										)';
						
		if($parametreAppel != '')
			$ordreSQL .=	'							AND		ModulesPronostiqueurs_Parametre = ' . $parametreAppel;
			
		
		$ordreSQL .=	'					UNION' .
						'					SELECT		Module' .
						'								,Modules_Nom' .
						'								,IFNULL(Modules_ZoneOptions, 0) AS Modules_ZoneOptions' .
						'								,IFNULL(ModulesPronostiqueurs_X, Modules_X) AS Modules_X' .
						'								,IFNULL(ModulesPronostiqueurs_Y, Modules_Y) AS Modules_Y' .
						'								,IFNULL(ModulesPronostiqueurs_Largeur, Modules_Largeur) AS Modules_Largeur' .
						'								,IFNULL(ModulesPronostiqueurs_Hauteur, Modules_Hauteur) AS Modules_Hauteur' .
						'								,Modules_LargeurMin' .
						'								,Modules_HauteurMin' .
						'								,Modules_PageVerification' .
						'								,Modules_Page' .
						'								,IFNULL(Modules_PageOptions, \'\') AS Modules_PageOptions' .
						'								,ModulesPronostiqueurs_Actif' .
						'								,ModulesPronostiqueurs_Parametre AS Modules_Parametre' .
						'								,IFNULL(Modules_ModeRival, 0) AS Modules_ModeRival' .
						'								,IFNULL(ModulesPronostiqueurs_ModeRival, 0) AS ModulesPronostiqueurs_ModeRival' .
						'								,IFNULL(Modules_ModeConcurrentDirect, 0) AS Modules_ModeConcurrentDirect' .
						'								,IFNULL(ModulesPronostiqueurs_ModeConcurrentDirect, 0) AS ModulesPronostiqueurs_ModeConcurrentDirect' .
						'								,IFNULL(Modules_RafraichissementAutomatique, 0) AS Modules_RafraichissementAutomatique' .
						'								,IFNULL(ModulesPronostiqueurs_IntervalleRafraichissement, 0) AS ModulesPronostiqueurs_IntervalleRafraichissement' .
						'								,IFNULL(Modules_CritereRafraichissement, \'\') AS Modules_CritereRafraichissement' .
						'								,IFNULL(ModulesPronostiqueurs_ModeIncruste, 0) AS ModulesPronostiqueurs_ModeIncruste' .
						'								,IFNULL(Modules_Classe, \'\') AS Modules_Classe' .
						'								,Modules_PagesAutorisees' .
						'					FROM		modules' .
						'					RIGHT JOIN	modules_pronostiqueurs' .
						'								ON		Module = Modules_Module' .
						'					LEFT JOIN	modules_groupes' .
						'								ON		modules_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'										AND		modules.Modules_Parametre = modules_groupes.ModulesGroupes_Parametre' .
						'					WHERE		modules_pronostiqueurs.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'								AND		(	(	Modules_Type >= 50' .
						'												AND		ModulesPronostiqueurs_Actif = 1' .
						'											)' .
						'											OR' .
						'											(	Modules_Type < 50' .
						'												AND		ModulesGroupes_Actif = 1' .
						'												AND		ModulesPronostiqueurs_Actif = 1' .
						'											)' .
						'										)';
						
		if($parametreAppel != '')
			$ordreSQL .=	'							AND		ModulesPronostiqueurs_Parametre = ' . $parametreAppel;
			
		$ordreSQL .=	'				) modules' .
						'	WHERE		Modules_PagesAutorisees = \'toutes\'' .
						'				OR		INSTR(Modules_PagesAutorisees, \'' . $nomPage . '\') > 0';

		$req = $bdd->query($ordreSQL);
		$modules = $req->fetchAll();
		
		foreach($modules as $unModule) {
			if($parametreAppel != '')
				$parametreAUtiliser = $parametreAppel;
			else
				$parametreAUtiliser = $unModule["Modules_Parametre"]; 
			initialiserModule	(	$bdd
									,$unModule["Module"], $unModule["Modules_Nom"]
									,$nomConteneurComplet
									,$nomConteneurSimple
									,$nomConteneurNouvellementCree
									,0
									,$unModule["Modules_ZoneOptions"]
									,$parametreAUtiliser
									,$unModule["Modules_PageVerification"]
									,$unModule["Modules_Page"]
									,$unModule["Modules_PageOptions"]
									,$unModule["Modules_X"], $unModule["Modules_Y"]
									,$unModule["Modules_Largeur"], $unModule["Modules_Hauteur"]
									,$unModule["Modules_LargeurMin"], $unModule["Modules_HauteurMin"]
									,$unModule["Modules_ModeRival"]
									,$unModule["ModulesPronostiqueurs_ModeRival"]
									,$unModule["Modules_ModeConcurrentDirect"]
									,$unModule["ModulesPronostiqueurs_ModeConcurrentDirect"]
									,$unModule["Modules_RafraichissementAutomatique"]
									,$unModule["ModulesPronostiqueurs_IntervalleRafraichissement"]
									,$unModule["Modules_CritereRafraichissement"]
									,$unModule["ModulesPronostiqueurs_ModeIncruste"]
									,$unModule["Modules_Classe"]
								);
		}
	}
	else {
		// Appel à la demande de l'utilisateur d'un module spécifique
		// Dans le cas où le module n'est pas déclaré chez le pronostiqueur (cas typique des nouveaux tchats de groupe), il est nécessaire d'ajouter une ligne dans la table modules_pronostiqueurs
		$ordreSQL =		'	INSERT INTO		modules_pronostiqueurs(Modules_Module, Pronostiqueurs_Pronostiqueur, ModulesPronostiqueurs_Parametre)' .
						'	SELECT			Modules_Module, Pronostiqueurs_Pronostiqueur, ModulesPronostiqueurs_Parametre' .
						'	FROM			(' .
						'						SELECT		' . $module . ' AS Modules_Module' .
						'									,' . $_SESSION["pronostiqueur"] . ' AS Pronostiqueurs_Pronostiqueur' .
						'									,' . $parametreAppel . ' AS ModulesPronostiqueurs_Parametre' .
						'					) AS tmp' .
						'	WHERE			NOT EXISTS' .
						'					(' .
						'						SELECT		*' .
						'						FROM		modules_pronostiqueurs' .
						'						WHERE		Modules_Module = ' . $module .
						'									AND		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'									AND		ModulesPronostiqueurs_Parametre = ' . $parametreAppel .
						'					)';


		$req = $bdd->exec($ordreSQL);
		
		// Lecture des informations de ce module
		$ordreSQL =		'	SELECT		Module' .
						'				,Modules_Nom' .
						'				,IFNULL(Modules_ZoneOptions, 0) AS Modules_ZoneOptions' .
						'				,IFNULL(ModulesPronostiqueurs_X, Modules_X) AS Modules_X' .
						'				,IFNULL(ModulesPronostiqueurs_Y, Modules_Y) AS Modules_Y' .
						'				,IFNULL(ModulesPronostiqueurs_Largeur, Modules_Largeur) AS Modules_Largeur' .
						'				,IFNULL(ModulesPronostiqueurs_Hauteur, Modules_Hauteur) AS Modules_Hauteur' .
						'				,Modules_LargeurMin' .
						'				,Modules_HauteurMin' .
						'				,Modules_PageVerification' .
						'				,Modules_Page' .
						'				,IFNULL(Modules_PageOptions, \'\') AS Modules_PageOptions' .
						'				,ModulesPronostiqueurs_Parametre' .
						'				,IFNULL(Modules_ModeRival, 0) AS Modules_ModeRival' .
						'				,IFNULL(ModulesPronostiqueurs_ModeRival, 0) AS ModulesPronostiqueurs_ModeRival' .
						'				,IFNULL(Modules_ModeConcurrentDirect, 0) AS Modules_ModeConcurrentDirect' .
						'				,IFNULL(ModulesPronostiqueurs_ModeConcurrentDirect, 0) AS ModulesPronostiqueurs_ModeConcurrentDirect' .
						'				,IFNULL(Modules_RafraichissementAutomatique, 0) AS Modules_RafraichissementAutomatique' .
						'				,IFNULL(ModulesPronostiqueurs_IntervalleRafraichissement, 0) AS ModulesPronostiqueurs_IntervalleRafraichissement' .
						'				,IFNULL(Modules_CritereRafraichissement, \'\') AS Modules_CritereRafraichissement' .
						'				,IFNULL(ModulesPronostiqueurs_ModeIncruste, 0) AS ModulesPronostiqueurs_ModeIncruste' .
						'				,IFNULL(Modules_Classe, \'\') AS Modules_Classe' .
						'	FROM		(' .
						'					SELECT		*' .
						'					FROM		modules' .
						'					LEFT JOIN	modules_pronostiqueurs' .
						'								ON		Module = Modules_Module' .
						'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'								AND		Module = ' . $module .
						'								AND		ModulesPronostiqueurs_Parametre = \'' . $parametreAppel . '\'' .
						'					UNION' .
						'					SELECT		*' .
						'					FROM		modules' .
						'					RIGHT JOIN	modules_pronostiqueurs' .
						'								ON		Module = Modules_Module' .
						'					WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'								AND		Module = ' . $module .
						'								AND		ModulesPronostiqueurs_Parametre = \'' . $parametreAppel . '\'' .
						'				) modules' .
						'	WHERE		Modules_PagesAutorisees = \'toutes\'' .
						'				OR		INSTR(Modules_PagesAutorisees, \'' . $nomPage . '\') > 0';

		$req = $bdd->query($ordreSQL);
		$modules = $req->fetchAll();

		foreach($modules as $unModule) {
			if($parametreAppel != '')
				$parametreAUtiliser = $parametreAppel;
			else
				$parametreAUtiliser = $unModule["Modules_Parametre"];
			
			initialiserModule	(	$bdd
									,$module, $unModule["Modules_Nom"]
									,$nomConteneurComplet
									,$nomConteneurSimple
									,$nomConteneurNouvellementCree
									,1
									,$unModule["Modules_ZoneOptions"]
									,$parametreAUtiliser
									,$unModule["Modules_PageVerification"]
									,$unModule["Modules_Page"]
									,$unModule["Modules_PageOptions"]
									,$unModule["Modules_X"], $unModule["Modules_Y"]
									,$unModule["Modules_Largeur"], $unModule["Modules_Hauteur"]
									,$unModule["Modules_LargeurMin"], $unModule["Modules_HauteurMin"]
									,$unModule["Modules_ModeRival"]
									,$unModule["ModulesPronostiqueurs_ModeRival"]
									,$unModule["Modules_ModeConcurrentDirect"]
									,$unModule["ModulesPronostiqueurs_ModeConcurrentDirect"]
									,$unModule["Modules_RafraichissementAutomatique"]
									,$unModule["ModulesPronostiqueurs_IntervalleRafraichissement"]
									,$unModule["Modules_CritereRafraichissement"]
									,$unModule["ModulesPronostiqueurs_ModeIncruste"]
									,$unModule["Modules_Classe"]
								);
		}

	}

?>


<script>
	// A l'ouverture d'un module, on suppose que c'est le module le plus récent
	// Il a donc le z-index max (1) tandis que les autres ont le 0
	// On gère au niveau de la fenêtre entière les z-index des modules
	// Pour cela, on sauvegarde juste le nom du module qui possède le focus
	// Lorsque l'utilisateur clique sur un autre module, l'ancien perd le focus, remplacé par le nouveau
	var nomConteneurFocus = '';


	$(function() {
		$('.module').each(function() {
			var nomConteneurComplet = '#' + $(this).attr('id');
			var nomConteneurNouvellementCree = '#' + '<?php echo $nomConteneurNouvellementCree; ?>';
			
			// Dans le cas de l'ajout d'un nouveau module, on ne s'intéresse qu'à lui car les autres sont déjà en place
			if(nomConteneurNouvellementCree != '#' && nomConteneurComplet != nomConteneurNouvellementCree) {
				return true;
			}
			
			cliquerModule(nomConteneurComplet);

			var module = $(nomConteneurComplet + '_Module').val();
			var pageVerification = $(nomConteneurComplet + '_PageVerification').val();
			var page = $(nomConteneurComplet + '_Page').val();
			var parametre = $(nomConteneurComplet + '_Parametre').val();
			var nomConteneurSimple = $(nomConteneurComplet + '_NomConteneurSimple').val();
			
			// On coche ou non la case Mode rival
			// Attention, dans certains modules, le mode rival n'existe pas
			if($(nomConteneurComplet).find('input[type="checkbox"][name="modeRival' + module + parametre + '"]').length != 0) {
				if($(nomConteneurComplet + '_ModeRival').val() == 1)
					$(nomConteneurComplet).find('input[type="checkbox"][name="modeRival' + module + parametre + '"]').attr('checked', true);
				else
					$(nomConteneurComplet).find('input[type="checkbox"][name="modeRival' + module + parametre + '"]').attr('checked', false);
			}

			// On coche ou non la case Mode concurrent direct
			// Attention, dans certains modules, le mode concurrent direct n'existe pas
			if($(nomConteneurComplet).find('input[type="checkbox"][name="modeConcurrentDirect' + module + parametre + '"]').length != 0) {
				if($(nomConteneurComplet + '_ModeConcurrentDirect').val() == 1)
					$(nomConteneurComplet).find('input[type="checkbox"][name="modeConcurrentDirect' + module + parametre + '"]').attr('checked', true);
				else
					$(nomConteneurComplet).find('input[type="checkbox"][name="modeConcurrentDirect' + module + parametre + '"]').attr('checked', false);
			}
				
			var modeIncruste = $(nomConteneurComplet + '_ModeIncruste').val();
			if(modeIncruste == 0) {
				$(nomConteneurComplet).draggable(	{
					start:	function() {
						cliquerModule(nomConteneurComplet);
					},
					stop:	function() {
								// Si le module est sous le bandeau, il est nécessaire de le faire redescendre
								var hauteurBandeau = $('.bandeau').height();
								if(hauteurBandeau > $(this).position().top)
									$(this).css('top', hauteurBandeau + 3);
								modules_sauvegarderPositionModule(module, nomConteneurSimple, parametre);
								
								$(this).find('.module--contenu').getNiceScroll().resize();
					},
					handle: '.module--titre'
				});
			}
			
			if(modeIncruste == 0) {
				$(nomConteneurComplet).resizable(	{
					minWidth: $(nomConteneurComplet + '_LargeurMin').val(),
					minHeight: $(nomConteneurComplet + '_HauteurMin').val(),
					stop:	function()	{
								modules_sauvegarderTailleModule(module, nomConteneurSimple, parametre);
					},
					handles: 'e,se,s,sw,w'
				});
			}


			// Il est nécessaire de modifier manuellement la taille de la zone de contenu
			// Il faut soustraire (et non ajouter, ce qui est obscur) à la hauteur de cette zone celle de l'en-tête et de la zone d'options si elle existe
			// Les options supplémentaires se trouvent dans un div dont la classe est options
			if($(nomConteneurComplet + '_ChangementTaille').val() == 0) {
				var nouvelleHauteur = $(nomConteneurComplet).find('.module--contenu').height();
				var hauteurEntete = $(nomConteneurComplet).find('.module--entete').height();
				var hauteurOptions = 0;
				if($(nomConteneurComplet).find('.module--options').length)
					hauteurOptions = $(nomConteneurComplet).find('.module--options').height();

				hauteurEntete = parseInt(hauteurEntete);
				nouvelleHauteur = parseInt(nouvelleHauteur) - hauteurEntete - hauteurOptions;
			
				$(nomConteneurComplet).find('.module--contenu').height(nouvelleHauteur);
				
				// On indique que la zone vient d'être redimensionnée
				// Ainsi, si un autre module est rafraîchi, cela ne vient pas perturber celui-ci
				$(nomConteneurComplet + '_ChangementTaille').val(1);
				
			}			
			// Si la coordonnée en X est supérieure à la taille de la fenêtre du navigateur, alors, il faut déplacer vers la gauche le module
			var deplacementForce = 0;
			var coordonnees = $(nomConteneurComplet).position();
			
			/*if(coordonnees.left >= $(window).width()) {
				$(nomConteneurComplet).css('left', ($(window).width() - $(nomConteneurComplet).width()) + 'px');
				deplacementForce = 1;
			}
			
			if(coordonnees.top >= $(window).height()) {
				$(nomConteneurComplet).css('top', '120px');
				deplacementForce = 1;
			}*/
			
			// Si le coin supérieur gauche du module est dans une zone que l'on ne peut atteindre, on doit le replacer
			// La zone que l'on ne peut atteindre prend en compte également le bandeau
			var hauteurBandeau = $('.bandeau').height();
			
			if(coordonnees.left < 0) {
				$(nomConteneurComplet).css('left', '10px');
				deplacementForce = 1;
			}
			
			if(coordonnees.top < hauteurBandeau) {
				$(nomConteneurComplet).css('top', (hauteurBandeau + 3) + 'px');
				deplacementForce = 1;
			}
			
			if(deplacementForce) {
				modules_sauvegarderPositionModule(module, nomConteneurSimple, parametre);
			}

			// Mise en place de l'intervalle de rafraîchissement pour les modules qui ont cette fonctionnalité
			// La création ne se fait que si le module n'a pas déjà un intervalle positionné (ce qui arrive lorsqu'une nouvelle fenêtre est créée)
			if($(nomConteneurComplet).find('select.rafraichissement').length) {
				if($(nomConteneurComplet + '_IntervalleRafraichissement').val() == 0) {
					// Intervalle souhaité
					var intervalleRafraichissement = $(nomConteneurComplet).find('select.rafraichissement').val();
					
					// Mise en place de l'intervalle de rafraîchissement
					if(intervalleRafraichissement != 0) {
						var intervalle = setInterval(function() {
							rafraichirModule(pageVerification, page, nomConteneurComplet);
						}, intervalleRafraichissement);
						
						$(nomConteneurComplet + '_IntervalleRafraichissement').val(intervalle);
					}
				}
			}
			
			// Gestion du clic sur une zone du module
			$(nomConteneurComplet).click(
				function() {
					cliquerModule(nomConteneurComplet);
			});

			
			// Clic sur la case à cocher mode rival
			$('.module input.moderival').change(
				function() {
					// Rafraîchissement du module
					// Le numéro de module a été placé plus haut dans l'attribut valeur de la case à cocher
					var module = $(this).val();
					var nomConteneurComplet = '#' + $(this).parents('.module').attr('id');
					var nomConteneurSimple = $(nomConteneurComplet + '_NomConteneurSimple').val();
					var parametre = $(nomConteneurComplet + '_Parametre').val();
					
					modules_changerModeRival(module, $(this).prop('checked'), parametre);
					modules_relancerModule(module, nomConteneurSimple, parametre);
				}
			);

			// Clic sur la case à cocher mode concurrent direct
			$('.module input.modeconcurrentdirect').change(
				function() {
					// Rafraîchissement du module
					// Le numéro de module a été placé plus haut dans l'attribut valeur de la case à cocher
					var module = $(this).val();
					var nomConteneurComplet = '#' + $(this).parents('.module').attr('id');
					var nomConteneurSimple = $(nomConteneurComplet + '_NomConteneurSimple').val();
					var parametre = $(nomConteneurComplet + '_Parametre').val();
					
					modules_changerModeConcurrentDirect(module, $(this).prop('checked'), parametre);
					modules_relancerModule(module, nomConteneurSimple, parametre);
				}
			);

			// Modification de la fréquence de rafraîchissement
			$('.module select.rafraichissement').change(
				function(e) {
					var nomConteneurComplet = '#' + $(this).parents('.module').attr('id');
					var module = $(nomConteneurComplet + '_Module').val();
					var intervalleRafraichissement = $(this).val();
					var intervalle = $(nomConteneurComplet + '_IntervalleRafraichissement').val();
					var parametre = $(nomConteneurComplet + '_Parametre').val();
					var pageVerification = $(nomConteneurComplet + '_PageVerification').val();
					var page = $(nomConteneurComplet + '_Page').val();
					modules_changerIntervalleRafraichissement(module, parametre, intervalleRafraichissement)
					
					// Mise en place ou modification de l'intervalle de rafraîchissement
					clearInterval(intervalle);
					if(intervalleRafraichissement != 0) {
						intervalle = setInterval(function() {
							rafraichirModule(pageVerification, page, nomConteneurComplet);
						}, intervalleRafraichissement);
						
						$(nomConteneurComplet + '_IntervalleRafraichissement').val(intervalle);
					}
					else {
						$(nomConteneurComplet + '_IntervalleRafraichissement').val(0);
					}
				}
			);
			
			$(this).find('.module--contenu').niceScroll({cursorcolor: "#333", cursorborder: "#333"});
		});
		
		
	});
	
	// Modification de la taille de la fenêtre faite par l'utilisateur
	$('div.module').resize( function(e) {
		var nomConteneurComplet = '#' + e.target.id;
		e.target.evtChangementTaille;
		$('#' + e.target.id).resize(function() {
			clearTimeout(e.target.evtChangementTaille);
			e.target.evtChangementTaille = setTimeout(function() {
				var nouvelleHauteur = $(nomConteneurComplet).css('height');
				var hauteurEntete = $(nomConteneurComplet).find('.module--entete').css('height');
				var hauteurOptions = 0;
				if($(nomConteneurComplet).find('.module--options').length)
					hauteurOptions = parseInt($(nomConteneurComplet).find('.module--options').css('height'));

				hauteurEntete = parseInt(hauteurEntete);
				nouvelleHauteur = parseInt(nouvelleHauteur) - hauteurEntete - hauteurOptions;
			
				$(nomConteneurComplet).find('.module--contenu').css('height', nouvelleHauteur + 'px');
			}, 750);
			
			$(this).find('.module--contenu').getNiceScroll().resize();
		});
	});
	
	// Fermeture du module - Effacement du timer s'il existe
	function masquerModule(module, nomConteneurSimple, parametre) {
		var intervalle = $('#' + nomConteneurSimple + parametre + '_IntervalleRafraichissement').val();
		if (intervalle != 0) {
			clearInterval(intervalle);
		}
		
		modules_masquerModule(module, nomConteneurSimple, parametre);
	}
	
	// Activation / désactivation du mode incrusté
	function epinglerModule(module, nomConteneurSimple, parametre) {
		var modeIncrustation = $('#' + nomConteneurSimple + parametre + '_ModeIncruste').val();
		
		modules_changerModeIncrustation(module, modeIncrustation, parametre);
		modules_relancerModule(module, nomConteneurSimple, parametre);
	}
	
	// Rafraîchissement du module
	function rafraichirModule(pageVerification, page, nomConteneurComplet) {
		// Deux modes existent :
		// - soit une page de vérification existe, auquel cas il est nécessaire de l'appeler pour vérifier s'il est nécessaire que le module soit rafraîchi ou non
		// - soit il n'existe pas de page de vérification, auquel cas on suppose que le rafraîchissement est obligatoire
		if(pageVerification == null || pageVerification == '')
			rafraichirModuleSansVerification(page, nomConteneurComplet);
		else
			rafraichirModuleAvecVerification(pageVerification, page, nomConteneurComplet);
	}

	
	// Rafraîchissement du module sans vérification
	function rafraichirModuleSansVerification(page, nomConteneurComplet) {
		// Lecture des informations propres au module en cours
		var module = $(nomConteneurComplet + '_Module').val();
		var parametre = $(nomConteneurComplet + '_Parametre').val();
		var modeRival = $(nomConteneurComplet + '_ModeRival').val();
		var modeConcurrentDirect = $(nomConteneurComplet + '_ModeConcurrentDirect').val();
		var nomConteneurSimple = $(nomConteneurComplet + '_NomConteneurSimple').val();
		
		$.ajax(	{
			url: page,
			type: 'POST',
			data:	{
						rafraichissementModule: 1,
						module: module,
						nomConteneurComplet: nomConteneurComplet,
						nomConteneurSimple: nomConteneurSimple,
						parametre: parametre,
						modeRival: modeRival,
						modeConcurrentDirect: modeConcurrentDirect
					}
				}
		).done(function(html) {
			$(nomConteneurComplet).find('.module--contenu').empty().append(html);
		}).fail(function(html) { console.log('Fonction rafraichirModuleSansVerification : fail de l\'appel Ajax'); });
	}

	
	// Rafraîchissement du module avec vérification
	function rafraichirModuleAvecVerification(pageVerification, page, nomConteneurComplet) {
		// Lecture des informations propres au module en cours
		var module = $(nomConteneurComplet + '_Module').val();
		var parametre = $(nomConteneurComplet + '_Parametre').val();
		var modeRival = $(nomConteneurComplet + '_ModeRival').val();
		var modeConcurrentDirect = $(nomConteneurComplet + '_ModeConcurrentDirect').val();
		var nomConteneurSimple = $(nomConteneurComplet + '_NomConteneurSimple').val();
		var critereRafraichissement = $(nomConteneurComplet + '_CritereRafraichissement').val();
		
		// Le rafraîchissement se fait en deux temps :
		// - d'abord on regarde s'il est nécessaire de le faire (en appelant la page dont le nom est contenu dans le paramètre pageVerification)
		// - ensuite, si c'est nécessaire, on fait appel à la deuxième page qui va retourner le contenu à proprement parler
		$.ajax(	{
					url: pageVerification,
					type: 'POST',
					data:	{
								critereRafraichissement: critereRafraichissement,
								parametre: parametre
							},
					dataType: 'json'
				}
		).done(function(html) {
			if(html.rafraichir == '1') {
				$(nomConteneurComplet + '_CritereRafraichissement').val(html.critereRafraichissement);
				$.ajax(	{
					url: page,
					type: 'POST',
					data:	{
								rafraichissementModule: 1,
								module: module,
								nomConteneurComplet: nomConteneurComplet,
								nomConteneurSimple: nomConteneurSimple,
								parametre: parametre,
								modeRival: modeRival,
								modeConcurrentDirect: modeConcurrentDirect
							}
						}
				).done(function(html) {
					$(nomConteneurComplet).find('.module--contenu').empty().append(html);
				}).fail(function(html) { console.log('Fonction rafraichirModuleAvecVerification : fail du deuxième appel Ajax'); });
			}
		}).fail(function(html) { console.log('Fonction rafraichirModuleAvecVerification : fail du premier appel Ajax'); });
	}
	
	// Gestion du clic sur le module (pour le passer en avant-plan)
	function cliquerModule(nomConteneurComplet) {
		if(nomConteneurComplet != nomConteneurFocus) {
			// Passer le z-index de l'ancien module à 0
			$(nomConteneurFocus).zIndex(200);
			
			// Passer celui-ci devant
			$(nomConteneurComplet).zIndex(300);
			
			// Sauvegarder le nom du module comme étant le module ayant le focus
			nomConteneurFocus = nomConteneurComplet;
		}
	}
	
</script>