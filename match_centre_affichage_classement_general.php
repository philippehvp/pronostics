<?php

	// La page peut être appelée de deux manières :
	// - par une inclusion
	// - par un appel Ajax (cas du rafraîchissement)

	$rafraichissementSection = isset($_POST["rafraichissementSection"]) ? $_POST["rafraichissementSection"] : 0;
	if($rafraichissementSection == 1) {
		// Rafraîchissement automatique de la section
		include('commun.php');
		
		// Lecture des paramètres passés à la page
		$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
		$pronostiqueurConsulte = isset($_POST["pronostiqueur_consulte"]) ? $_POST["pronostiqueur_consulte"] : 0;
	}
	
	// Lecture du championnat et des informations de la journée
	$ordreSQL =		'	SELECT		Championnats_Championnat, Journees_DateMAJ, Journees_DateEvenement' .
					'	FROM		journees' .
					'	WHERE		Journee = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$championnat = $journees[0]["Championnats_Championnat"];
	$journeeDateMAJ = $journees[0]["Journees_DateMAJ"];
	$journeeDateEvenement = $journees[0]["Journees_DateEvenement"];
	
	
	echo '<input type="hidden" name="date_maj_journee_temporaire" value="' . $journeeDateMAJ . '">';
	echo '<input type="hidden" name="date_evenement_journee_temporaire" value="' . $journeeDateEvenement . '">';

	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
					'				,classements.Classements_ClassementGeneralMatch' .
					'				,classements.Classements_PointsGeneralMatch, classements.Classements_PointsGeneralButeur' .
					'				,classements_veille.Classements_ClassementGeneralMatch - classements.Classements_ClassementGeneralMatch AS Evolution_Classement' .
					'				,classements.Classements_PointsGeneralMatch - classements_veille.Classements_PointsGeneralMatch AS Evolution_Points' .
					'	FROM		classements' .
					'	JOIN		pronostiqueurs' .
					'				ON		classements.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
					'	LEFT JOIN	(' .
					'					SELECT		Pronostiqueurs_Pronostiqueur, Journees_Journee, Classements_ClassementGeneralMatch, Classements_PointsGeneralMatch' .
					'					FROM		classements' .
					'					JOIN		journees' .
					'								ON		classements.Journees_Journee = journees.Journee' .
					'					WHERE		journees.Championnats_Championnat = ' . $championnat .
					'								AND		journees.Journee = ' . ($journee - 1) .
					'				) classements_veille' .
					'				ON		classements.Pronostiqueurs_Pronostiqueur = classements_veille.Pronostiqueurs_Pronostiqueur' .
					'						AND		classements.Journees_Journee = classements_veille.Journees_Journee + 1' .
					'	WHERE		classements.Journees_Journee = ' . $journee .
					'	ORDER BY	classements.Classements_ClassementGeneralMatch';
	$req = $bdd->query($ordreSQL);
	$classements = $req->fetchAll();
	$nombreClassements = sizeof($classements);

	// Affichage des données en ligne
	if($nombreClassements) {
		$LIGNES_PAR_COLONNE = 8;
		$nombreColonnes = floor($nombreClassements / $LIGNES_PAR_COLONNE);
		if($nombreClassements % $LIGNES_PAR_COLONNE > 0)
			$nombreColonnes++;
		
		for($i = 0; $i < $nombreColonnes; $i++) {
			if($i == 0)							echo '<div class="premiere-colonne">';
			else								echo '<div class="colonne-suivante">';

				echo '<table class="mc--tableau-classements">';
					echo '<tbody>';
						for($j = 0; $j < $LIGNES_PAR_COLONNE; $j++) {
							if($i * $LIGNES_PAR_COLONNE + $j >= $nombreClassements)
								echo '<tr><td colspan="4">&nbsp;</td></tr>';
							else {
								if($classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueur"] == $_SESSION["pronostiqueur"])			echo '<tr class="surbrillance" onclick="classementsPronostiqueurs_afficherPronostiqueur(' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueur"] . ');">';
								else																								echo '<tr onclick="classementsPronostiqueurs_afficherPronostiqueur(' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueur"] . ');">';
									echo '<td class="bordure-basse-legere"><img class="photo" src="images/pronostiqueurs/' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueurs_Photo"] . '" alt="" /></td>';
									echo '<td class="bordure-basse-legere">' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Classements_ClassementGeneralMatch"] . '</td>';
									if($classements[$i * $LIGNES_PAR_COLONNE + $j]["Evolution_Classement"] != null) {
										if($classements[$i * $LIGNES_PAR_COLONNE + $j]["Evolution_Classement"] > 0)
											echo '<td class="bordure-basse-legere"><img src="images/positif.gif" alt="" />+' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Evolution_Classement"] . '</td>';
										else if($classements[$i * $LIGNES_PAR_COLONNE + $j]["Evolution_Classement"] == 0)
											echo '<td class="bordure-basse-legere"><img src="images/identique.gif" alt="" />&nbsp;</td>';
										else
											echo '<td class="bordure-basse-legere"><img src="images/negatif.gif" alt="" />' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Evolution_Classement"] . '</td>';
									}
									else
										echo '<td class="bordure-basse-legere">&nbsp;</td>';
									echo '<td class="aligne-gauche bordure-basse-legere">' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Pronostiqueurs_NomUtilisateur"] . '</td>';
									echo '<td class="aligne-gauche bordure-basse-legere">' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Classements_PointsGeneralMatch"] . ' (' . $classements[$i * $LIGNES_PAR_COLONNE + $j]["Classements_PointsGeneralButeur"] . ')</td>';
								echo '</tr>';
							}
						}
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		}

	}
?>

<script>
	$(function() {
		
		// Création d'un timer de rafraîchissement si celui-ci n'existe pas déjà
		var intervalle = $('input[name="minuteur_journee"]').val();
		var pronostiqueurConsulte;
		
		if(intervalle == 0) {
			intervalle = setInterval(function() {
				// Vérification des données affichées pour rafraîchissement si nécessaire
				// D'abord les matches de la journée (calcul ou modification de score, nouveaux scores du pronostiqueur, match en direct, etc.)
				// Puis les classements (et le graphique)
				var journee = $('input[name="journee"]').val();
				var date_maj_journee = $('input[name="date_maj_journee_temporaire"]').val();
				var date_evenement_journee = $('input[name="date_evenement_journee_temporaire"]').val();
				pronostiqueurConsulte = $('input[name="pronostiqueur_consulte"]').val();
				$('input[name="date_maj_journee"]').val(date_maj_journee);
				$('input[name="date_evenement_journee"]').val(date_evenement_journee);
				$('input[name="pronostiqueur_consulte"]').val(pronostiqueurConsulte);
				matchCentre_rafrichirJournee(journee, pronostiqueurConsulte, date_maj_journee, date_evenement_journee, 'mc--resultats');
				matchCentre_rafrichirClassements(journee, date_maj_journee, 'mc--classement-general', 'mc--classement-journee', 'mc--graphiques', 'mc--statistiques-journee');
			}, 5000);
			
			$('input[name="minuteur_journee"]').val(intervalle);
		}
		
		pronostiqueurConsulte = $('input[name="pronostiqueur_consulte"]').val();
		
		// Championnat en cours
		var championnat = $('input[name="championnat"]').val();
		
		// Journée en cours
		var journee = $('input[name="journee"]').val();

		// Nombre de pronostiqueurs
		var nombrePronostiqueurs = $('input[name="nombrePronostiqueurs"]').val();
		
		// Calcul des dimensions de la zone de dessin
		var		marges = { haut: 20, droite: 30, bas: 60, gauche: 40 }
				,largeurGraphique = $('.mc--graphiques').width() - (marges.gauche + marges.droite)
				,hauteurGraphique = $('.mc--graphiques').height() - (marges.haut + marges.bas);

		// Mise en place de la zone du graphique (largeurGraphique, hauteurGraphique et marges)
		// La zone de dessin contient les deux graphiques (évolution du classement général en courbe et classement journée en histogramme)
		var graphiques = d3.select('.mc--graphiques')
			.attr('width', largeurGraphique + marges.gauche + marges.droite)
			.attr('height', hauteurGraphique + marges.haut + marges.bas)
			.append('g')
			.attr('class', 'graphique')
			.attr('transform', 'translate(' + marges.gauche + ',' + marges.haut + ')');

		// Pour homogénéiser les barres du graphique, on doit d'abord déterminer combien de données doivent être affichées
		// pour ensuite calculer la largeurGraphique de chaque barre
		var echelleXGeneral = d3.scale.linear();
		var echelleXJournee = d3.scale.linear();

		// Pour inverser l'affichage de l'axe y, il faut inverser les bornes
		// Ainsi, une bonne performance sera indiquée par une valeur "élevée"
		var echelleYJournee = d3.scale.linear()
			.range([0, hauteurGraphique])
			.domain([1, nombrePronostiqueurs]);

		// Pour inverser l'affichage de l'axe y, il faut inverser les bornes
		// Ainsi, une bonne performance sera indiquée par une valeur "élevée"
		var echelleYGeneral = d3.scale.linear()
			.range([0, hauteurGraphique])
			.domain([1, nombrePronostiqueurs]);

		// Création de la courbe pleine du classement général
		var courbePleine = d3.svg.area()
			.interpolate('cardinal')
			.x(function(d) { return echelleXGeneral(parseInt(d.J)); })
			.y0(hauteurGraphique)
			.y1(function(d) { return echelleYGeneral(parseInt(d.CG)); });

		// Création de la courbe sans remplissage du classement général
		var courbe = d3.svg.line()
			.interpolate('cardinal')
			.x(function(d) { return echelleXGeneral(parseInt(d.J)); })
			.y(function(d) { return echelleYGeneral(parseInt(d.CG)); });
			
		// Ajout d'une ombre à la courbe
		var defs = graphiques.append('defs');

		var filtre = defs.append('filter')
			.attr('id', 'ombre');

		filtre.append('feGaussianBlur')
			.attr('in', 'SourceAlpha')
			.attr('stdDeviation', 1)
			.attr('result', 'blur');
			
		filtre.append('feOffset')
			.attr('in', 'blur')
			.attr('dx', 0)
			.attr('dy', 2)
			.attr('result', 'offsetBlur');

		var feMerge = filtre.append('feMerge');

		feMerge.append('feMergeNode')
			.attr('in', 'offsetBlur');
			
		feMerge.append('feMergeNode')
			.attr('in', 'SourceGraphic');
			
		function creerAxeY() {
			return d3.svg.axis()
				.scale(echelleYJournee)
				.orient('left');
		}

		// Lecture des données des deux classements (général et journée)
		d3.json('match_centre_graphique_classements.php?championnat=' + championnat + '&pronostiqueur=' + pronostiqueurConsulte, function(erreur, donnees) {
			// Une fois toutes les données lues, on est capable de savoir quel est le nombre de journées
			// Ce qui va nous permettre de modifier l'étendue visuelle (range) du graphique
			var largeurBarre = Math.round(largeurGraphique / donnees.length);
            
            if(donnees.length <= 1)
                return;

			// Attention, pour synchroniser les barres d'histogramme et la courbe, le mieux est de faire "presque" le même calcul de largeur
			// pour être sûr de ne pas avoir de décalage
			echelleXGeneral
				.domain([1, donnees.length])
				.range([0, (donnees.length * largeurBarre) - 1]);

			echelleXJournee
				.domain([1, donnees.length])
				.range([0, (donnees.length - 1) * largeurBarre]);

			graphiques
				.append('g')
				.append('path')
				.datum(donnees)
				.attr('class', 'mc--graphique-courbe-pleine')
				.attr('d', courbePleine);
				
			// Création des barres du graphique
			// Création d'un conteneur groupe
			var barres = graphiques.selectAll('mc--graphique-barre')
				.data(donnees)
				.enter().append('g');

			// Création des barres
			barres
				.append('rect')
				.attr('class', 'mc--graphique-barre')
				.attr('x', function(d) { return echelleXJournee(parseInt(d.J)); })
				.attr('y', function(d) { return echelleYJournee(nombrePronostiqueurs) - echelleYJournee(nombrePronostiqueurs - parseInt(d.CJ) + 1); })
				.attr('height', function(d) { return echelleYJournee(nombrePronostiqueurs - parseInt(d.CJ) + 1); })
				.attr('width', largeurBarre - 1);

			var infobulle = d3.tip()
				.attr('class', 'mc--graphique-infobulle')
				.offset([-10, 0])
				.html(function(d) {
					return 'Journée : ' + d.J + '<br />Général : ' + d.CG + ' (' + d.PG + ' points)<br />Journée : ' + d.CJ + ' (' + d.PJ + ' points)';
				});

			graphiques.call(infobulle);

			// Ajout des événements "entrée" et "sortie" de la souris
			barres
				.on('mouseover', infobulle.show)
				.on('mouseout', infobulle.hide);

			graphiques
				.append('g')
				.append('path')
				.datum(donnees)
				.attr('class', 'mc--graphique-courbe')
				.attr('d', courbe)
				.attr('filter', 'url(#ombre)');

			// L'axe des x affiche les numéros de journée
			// Si le nombre de journées est inférieur ou égal à 3, le comportement normal est laissé au composant
			// Au-dessus, on affiche la première et dernière journée + la journée centrale
			var axeX;
			if(donnees.length <= 6)
				axeX = d3.svg.axis()
					.scale(echelleXJournee)
					.orient('bottom')
					.tickFormat(d3.format('d'))
					.ticks(donnees.length);
			else
				axeX = d3.svg.axis()
					.scale(echelleXJournee)
					.orient('bottom')
					.tickFormat(d3.format('d'))
					.ticks(3)
					.tickSubdivide(0)
					.tickValues([1, Math.floor(donnees.length / 2), donnees.length]);

			// Ajout de l'axe des x (qui est déplacé vers le bas)
			graphiques
				.append('g')
				.attr('class', 'x mc--graphiques-axe')
				.attr('transform', 'translate(' + (largeurBarre / 2) + ', ' + (hauteurGraphique + 2) + ')')
				.call(axeX);

			// Ajout de l'axe des y
			// L'axe des y affiche les classements
			// On affiche la première et dernière place + la place centrale
			var axeY = d3.svg.axis()
				.scale(echelleYJournee)
				.orient('left')
				.tickFormat(d3.format('d'))
				.ticks(3)
				.tickValues([1, Math.floor(nombrePronostiqueurs / 2), nombrePronostiqueurs]);

			graphiques
				.append('g')
				.attr('class', 'y mc--graphiques-axe')
				.call(axeY)
				.attr('transform', 'translate(-2, 0)')
				.append('g')
				.attr('class', 'mc--graphique-grille')
				.call(creerAxeY().tickSize(-(donnees.length * largeurBarre), 0, 0).tickFormat('').ticks(Math.floor(nombrePronostiqueurs / 8)));

			
				
			// Ajout du titre pour les axes

			// Axe des x
			graphiques.append('g')
				.attr('class', 'y mc--graphiques-axe')
				.call(echelleXJournee)
				.append('text')
				.attr('class', 'titre-axe')
				.attr('x', donnees.length * largeurBarre / 2)
				.attr('y', hauteurGraphique + marges.bas / 2)
				.attr('dy', '.71em')
				.style('text-anchor', 'middle')
				.text('Journées');

			// Axe des y
			/*graphiques.append('g')
				.attr('class', 'y mc--graphiques-axe')
				.call(echelleYJournee)
				.append('text')
				.attr('class', 'titre-axe')
				.attr('transform', 'rotate(-90)')
				.attr('x', -marges.haut)
				.attr('y', -marges.gauche)
				.attr('dy', '.71em')
				.style('text-anchor', 'end')
				.text('Classements');*/

		});
	});
</script>