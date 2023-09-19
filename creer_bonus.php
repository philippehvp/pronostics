<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>

<body>
	<?php
		$nomPage = 'creer_bonus.php';
		include_once('bandeau.php');

		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Saisie des bonus pour un pronostiqueur

		// Liste des équipes
		$ordreSQL =		'	SELECT		equipes.Equipe, Equipes_Nom' .
						'	FROM		equipes' .
						'	JOIN		engagements' .
						'				ON		equipes.Equipe = engagements.Equipes_Equipe' .
						'	WHERE		equipes.Equipes_L1Europe IS NULL' .
						'				AND		engagements.Championnats_Championnat = 1' .
						'	ORDER BY	Equipes_Nom';
		$req = $bdd->query($ordreSQL);
		$equipes = $req->fetchAll();

		// Lecture des données déjà saisies par le pronostiqueur
		$ordreSQL =		'	SELECT		PronosticsBonus_EquipeChampionne' .
						'				,PronosticsBonus_EquipeLDC1, PronosticsBonus_EquipeLDC2, PronosticsBonus_EquipeLDC3, PronosticsBonus_EquipeLDC4' .
						'				,PronosticsBonus_EquipeReleguee1, PronosticsBonus_EquipeReleguee2, PronosticsBonus_EquipeReleguee3' .
						'				,PronosticsBonus_JoueurMeilleurButeur' .
						'				,PronosticsBonus_JoueurMeilleurPasseur' .
						'				,buteurs.Joueurs_NomFamille AS NomMeilleurButeur' .
						'				,passeurs.Joueurs_NomFamille AS NomMeilleurPasseur' .
						'	FROM		pronostics_bonus' .
						'	LEFT JOIN	joueurs buteurs' .
						'				ON		pronostics_bonus.PronosticsBonus_JoueurMeilleurButeur = buteurs.Joueur' .
						'	LEFT JOIN	joueurs passeurs' .
						'				ON		pronostics_bonus.PronosticsBonus_JoueurMeilleurPasseur = passeurs.Joueur' .
						'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur;

		$req = $bdd->query($ordreSQL);
		$pronostics_bonus = $req->fetchAll();

		if(!empty($pronostics_bonus)) {
			$pronosticsBonus_EquipeChampionne = $pronostics_bonus[0]["PronosticsBonus_EquipeChampionne"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeChampionne"] : 0;
			$pronosticsBonus_EquipeLDC1 = $pronostics_bonus[0]["PronosticsBonus_EquipeLDC1"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeLDC1"] : 0;
			$pronosticsBonus_EquipeLDC2 = $pronostics_bonus[0]["PronosticsBonus_EquipeLDC2"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeLDC2"] : 0;
			$pronosticsBonus_EquipeLDC3 = $pronostics_bonus[0]["PronosticsBonus_EquipeLDC3"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeLDC3"] : 0;
			$pronosticsBonus_EquipeLDC4 = $pronostics_bonus[0]["PronosticsBonus_EquipeLDC4"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeLDC4"] : 0;
			$pronosticsBonus_EquipeReleguee1 = $pronostics_bonus[0]["PronosticsBonus_EquipeReleguee1"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeReleguee1"] : 0;
			$pronosticsBonus_EquipeReleguee2 = $pronostics_bonus[0]["PronosticsBonus_EquipeReleguee2"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeReleguee2"] : 0;
			$pronosticsBonus_EquipeReleguee3 = $pronostics_bonus[0]["PronosticsBonus_EquipeReleguee3"] != null ? $pronostics_bonus[0]["PronosticsBonus_EquipeReleguee3"] : 0;
			$meilleurButeur = $pronostics_bonus[0]["PronosticsBonus_JoueurMeilleurButeur"] != null ? $pronostics_bonus[0]["PronosticsBonus_JoueurMeilleurButeur"] : 0;
			$meilleurPasseur = $pronostics_bonus[0]["PronosticsBonus_JoueurMeilleurPasseur"] != null ? $pronostics_bonus[0]["PronosticsBonus_JoueurMeilleurPasseur"] : 0;
			$nomMeilleurButeur = $pronostics_bonus[0]["NomMeilleurButeur"] != null ? $pronostics_bonus[0]["NomMeilleurButeur"] : 0;
			$nomMeilleurPasseur = $pronostics_bonus[0]["NomMeilleurPasseur"] != null ? $pronostics_bonus[0]["NomMeilleurPasseur"] : 0;
		}
		else {
			$pronosticsBonus_EquipeChampionne = 0;
			$pronosticsBonus_EquipeLDC1 = 0;
			$pronosticsBonus_EquipeLDC2 = 0;
			$pronosticsBonus_EquipeLDC3 = 0;
			$pronosticsBonus_EquipeLDC4 = 0;
			$pronosticsBonus_EquipeReleguee1 = 0;
			$pronosticsBonus_EquipeReleguee2 = 0;
			$pronosticsBonus_EquipeReleguee3 = 0;
			$meilleurButeur = 0;
			$meilleurPasseur = 0;
			$nomMeilleurButeur = '';
			$nomMeilleurPasseur = '';
		}

		echo '<div id="divBonus" class="creation-bonus contenu-page">';
			/*echo '<a class="lienActif" href="cotes/cotes_bonus_fin_de_saison.xls" alt="">Cotes de bonus de fin de saison</a>';
			echo '<br />';*/


			// Equipe championne
			echo '<div class="tuile gauche impair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesChampionnes">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeChampionne == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe championne</label></div>';
			echo '</div>';

			// Meilleur buteur
			echo '<div class="tuile gauche impair">';
				echo '<div class="saisie-joueur">';
					echo '<input type="hidden" id="id-meilleur-buteur" value="' . $meilleurButeur . '" />';
					echo '<label class="actuel-nom-meilleur-buteur">Actuellement : ' . $nomMeilleurButeur . '</label><br />';
					echo '<input type="text" class="nom-meilleur-buteur" value="' . $nomMeilleurButeur . '" />';
					echo '<img src="images/loupe.png" alt="Loupe" />';
					echo '<div class="liste-meilleur-buteur"></div>';
				echo '</div>';
				echo '<div class="texte"><label>Meilleur buteur</label></div>';
			echo '</div>';

			// Meilleur passeur
			echo '<div class="tuile gauche impair">';
				echo '<div class="saisie-joueur">';
					echo '<input type="hidden" id="id-meilleur-passeur" value="' . $meilleurPasseur . '" />';
					echo '<label class="actuel-nom-meilleur-passeur">Actuellement : ' . $nomMeilleurPasseur . '</label><br />';
					echo '<input type="text" class="nom-meilleur-passeur" value="' . $nomMeilleurPasseur . '" />';
					echo '<img src="images/loupe.png" alt="Loupe" />';
					echo '<div class="liste-meilleur-passeur"></div>';
				echo '</div>';
				echo '<div class="texte"><label>Meilleur passeur</label></div>';
			echo '</div>';

			// Equipes LDC
			echo '<div class="tuile colle-gauche pair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesLDC1">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeLDC1 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 1 qualifiée en LDC</label></div>';
			echo '</div>';

			echo '<div class="tuile gauche pair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesLDC2">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeLDC2 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 2 qualifiée en LDC</label></div>';
			echo '</div>';

			echo '<div class="tuile gauche pair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesLDC3">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeLDC3 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 3 qualifiée en LDC</label></div>';
			echo '</div>';

			echo '<div class="tuile gauche pair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesLDC4">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeLDC4 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 4 qualifiée en LDC</label></div>';
			echo '</div>';

			// Equipes releguées
			echo '<div class="tuile colle-gauche impair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesReleguees1">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeReleguee1 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 1 reléguée en ligue 2 (16ème barragiste)</label></div>';
			echo '</div>';

			echo '<div class="tuile gauche impair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesReleguees2">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeReleguee2 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 2 reléguée en ligue 2</label></div>';
			echo '</div>';

			echo '<div class="tuile gauche impair">';
				echo '<div class="saisie">';
					echo '<select id="selectEquipesReleguees3">';
						echo '<option value="-1" selected="selected">Equipes</option>';
						foreach($equipes as $uneEquipe) {
							$selected = $pronosticsBonus_EquipeReleguee3 == $uneEquipe["Equipe"] ? ' selected="selected"' : '';
							echo '<option value="' . $uneEquipe["Equipe"] . '"' . $selected . '>' . $uneEquipe["Equipes_Nom"] . '</option>';
						}
					echo '</select>';
				echo '</div>';
				echo '<div class="texte"><label>Equipe 3 reléguée en ligue 2</label></div>';
			echo '</div>';

			// Bouton de validation
			echo '<div id="divBonusValider" class="colle-gauche">';
				echo '<label id="labelValiderBonus">Valider les bonus</label>';
			echo '</div>';

		echo '</div>';

	?>

	<script>
		$(function() {
			afficherTitrePage('divBonus', 'Saisie des bonus');

			$('#labelValiderBonus').button().click(	function(event) {
														creerBonus_validerBonus();
													}
			);
			$('.nom-meilleur-buteur').keyup(function(event) {
												// Lecture de la taille de la zone de texte
												if($('.nom-meilleur-buteur').val().length >= 3) {
													creerBonus_rechercherJoueur($('.nom-meilleur-buteur').val(), '.liste-meilleur-buteur', '.nom-meilleur-buteur', '#id-meilleur-buteur', '.actuel-nom-meilleur-buteur');
													$('.liste-meilleur-buteur').css({'display': 'block'});
												}
												else {
													$('.liste-meilleur-buteur').css({'display': 'none'});
												}

											}
										);

			$('.nom-meilleur-buteur').blur	(	function() {
												setTimeout(function() {
													$('.liste-meilleur-buteur').css({'display': 'none'});
												}, 500);
											}
										);

			$('.nom-meilleur-buteur').focus(function() {
												// Lecture de la taille de la zone de texte
												if($('.nom-meilleur-buteur').val().length >= 3) {
													creerBonus_rechercherJoueur($('.nom-meilleur-buteur').val(), '.liste-meilleur-buteur', '.nom-meilleur-buteur', '#id-meilleur-buteur', '.actuel-nom-meilleur-buteur');
													$('.liste-meilleur-buteur').css({'display': 'block'});
												}
											}
										);

			$('.nom-meilleur-passeur').keyup(function(event) {
												// Lecture de la taille de la zone de texte
												if($('.nom-meilleur-passeur').val().length >= 3) {
													creerBonus_rechercherJoueur($('.nom-meilleur-passeur').val(), '.liste-meilleur-passeur', '.nom-meilleur-passeur', '#id-meilleur-passeur', '.actuel-nom-meilleur-passeur');
													$('.liste-meilleur-passeur').css({'display': 'block'});
												}
												else {
													$('.liste-meilleur-passeur').css({'display': 'none'});
												}

											}
										);

			$('.nom-meilleur-passeur').blur	(	function() {
												setTimeout(function() {
													$('.liste-meilleur-passeur').css({'display': 'none'});
												}, 500);
											}
										);

			$('.nom-meilleur-passeur').focus(function() {
												// Lecture de la taille de la zone de texte
												if($('.nom-meilleur-passeur').val().length >= 3) {
													creerBonus_rechercherJoueur($('.nom-meilleur-passeur').val(), '.liste-meilleur-passeur', '.nom-meilleur-passeur', '#id-meilleur-passeur', '.actuel-nom-meilleur-passeur');
													$('.liste-meilleur-passeur').css({'display': 'block'});
												}
											}
										);
		});
	</script>
</body>
</html>