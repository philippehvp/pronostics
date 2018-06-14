// Variables globales
/*var MODULES_POSITION_X = new Array(10);
var MODULES_POSITION_Y = new Array(10);*/

// Affichage du titre de la page
function afficherTitrePage(element, titrePage) {
	$('#' + element).prepend('<h1>' + titrePage + '</h1>');
	retournerHautPage();
}

// Première connexion - Soumission de formulaire
function premiereConnexion_validerMotDePasse() {
	var elt = document.forms['formModificationMotDePasse'];
	if (elt != null)
		elt.submit();
}

// Affichage du bouton "retour haut de page"
function retournerHautPage() {
	$('html body').prepend('<a href="#" class="retourHautPage">&nbsp;</a>');
	
	$(window).scroll(function() {
		if ($(this).scrollTop() > 80) {
			$('.retourHautPage').fadeIn();
		} else {
			$('.retourHautPage').fadeOut();
		}
	});
	
	$('.retourHautPage').click(function() {
		$('html, body').animate({scrollTop : 0}, 500);
		return false;
	});
}

// Affichage d'un module
function afficherModule(module, nomConteneur) {
	// Cette fonction affiche ou cache un élément
	// Elle met à jour aussi en base de données l'état d'affichage du module en question
	var etat = -1;
	
	if($('#' + nomConteneur).length) {
		// Si l'objet est déjà affiché, on le cache (et inversement)
		if($('#' + nomConteneur).css('display') == 'block') {
			$('#' + nomConteneur).css('display', 'none');
			etat = 0;
		}
		else {
			$('#' + nomConteneur).css('display', 'block');
			etat = 1;
		}
	}
	else {
		etat = 1;
		// Module non chargé
		$.ajax(	{
					url: 'modules_chargement_module.php',
					type: 'POST',
					data:	{	module: module	}
				}
		).done(function(html) {
			$('body').append(html);
			$('#' + nomConteneur).offset({top: MODULES_POSITION_Y[module], left: MODULES_POSITION_X[module]});
		});
	}

	// Arrivé ici, on sauvegarde l'état pour le module en question afin qu'il se réaffiche automatiquement ou non la prochaine fois
	$.ajax(	{
				url: 'modules_sauvegarde_etat.php',
				type: 'POST',
				data:	{
							module: module,
							etat: etat
						}
			}
	).done(function(html) {
	});
}

// Sauvegarde de la position d'un module
function modules_sauvegarderPositionModule(module, nomConteneur) {
	// Cette fonction regarde la position en X et en Y d'un module et l'écrit en base de données
	// Lecture des coordonnées du module
	var coordonnees = $('#' + nomConteneur).position();
	if(coordonnees != null) {
		$.ajax(	{
					url: 'modules_sauvegarde_position.php',
					type: 'POST',
					data:	{
								module: module,
								x: coordonnees.left,
								y: coordonnees.top
							}
				}
		).done(	function(html) {
		});
	}
}

// Module des pronostics de poule - Fonction Javascript appelée à l'initialisation du module
function module_pronosticsPoule(module, nomConteneur, x, y) {
	// Sauvegarde des positions du module dans une variable globale
	/*MODULES_POSITION_X[module] = x;
	MODULES_POSITION_Y[module] = y;
	$('#' + nomConteneur).draggable	(	{	stop:	function() {
														modules_sauvegarderPositionModule(module, nomConteneur);
													}
										}
									).resizable({minHeight: 100, minWidth: 400});
	$('#' + nomConteneur).offset({top: y, left: x});*/
}

// Module des pronostics de poule - Affichage des journées de poule
function module_pronosticsPoule_afficherJournee(journee) {
	if(journee == null)
		return;

	$.ajax(	{
				url: 'module_pronostics_poule.php',
				type: 'POST',
				data:	{
							appelAjax: 1,
							journeeEnCours: journee
						}
			}
	).done(	function(html) {
		/*$('#divModulePronosticsPoule').empty().append(html);
		$('#divModulePronosticsPoule').resizable('destroy');
		$('#divModulePronosticsPoule').draggable().resizable({});*/
		$('#divVisuPronosticsPoule').empty().append(html);
		$('#divAccueilPronosticsPoule').empty().append(html);
	});
}

// Module des pronostics de phase finale - Fonction Javascript appelée à l'initialisation du module
function module_pronosticsPhaseFinale(module, nomConteneur, x, y) {
	/*MODULES_POSITION_X[module] = x;
	MODULES_POSITION_Y[module] = y;
	$('#' + nomConteneur).draggable	(	{	stop:	function() {
														modules_sauvegarderPositionModule(module, nomConteneur);
													}
										}
									).resizable({minHeight: 100, minWidth: 600});
	$('#' + nomConteneur).offset({top: y, left: x});*/
}

// Module des pronostics de phase finale - Affichage des journées de poule
function module_pronosticsPhaseFinale_afficherJournee(journee) {
	if(journee == null)
		return;

	$.ajax(	{
				url: 'module_pronostics_phase_finale.php',
				type: 'POST',
				data:	{
							appelAjax: 1,
							journeeEnCours: journee
						}
			}
	).done(	function(html) {
		/*$('#divModulePronosticsPhaseFinale').empty().append(html);
		$('#divModulePronosticsPhaseFinale').resizable('destroy');
		$('#divModulePronosticsPhaseFinale').draggable().resizable({});*/
		$('#divVisuPronosticsPhaseFinale').empty().append(html);
		$('#divAccueilPronosticsPhaseFinale').empty().append(html);
	});
}

// Module des pronostics de phase finale - Affichage de la légende
function module_pronosticsPhaseFinale_afficherLegende() {
	$.ajax(	{
				url: 'module_pronostics_phase_finale_legende.php',
				type: 'POST'
			}
	).done(function(html) {
		$('#divLegende').empty().append(html);
		$('#divLegende').dialog({
			autoOpen: true
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Légende'
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
			}

		});

	});
}

// Module des classements de poule - Fonction Javascript appelée à l'initialisation du module
function module_classementsPoule(module, nomConteneur, x, y) {
	// Sauvegarde des positions du module dans une variable globale
	/*MODULES_POSITION_X[module] = x;
	MODULES_POSITION_Y[module] = y;
	$('#' + nomConteneur).draggable	(	{	stop:	function() {
														modules_sauvegarderPositionModule(module, nomConteneur);
													}
										}
									).resizable({minHeight: 100, minWidth: 350});
	$('#' + nomConteneur).offset({top: y, left: x});*/
}

// Module des classements de poule - Affichage d'une poule
function module_classementsPoule_afficherPoule(poule) {
	if(poule == null)
		return;

	$.ajax(	{
				url: 'module_classements_poule.php',
				type: 'POST',
				data:	{
							appelAjax: 1,
							poule: poule
						}
			}
	).done(	function(html) {
		/*$('#divModuleClassementsPoule').empty().append(html);
		$('#divModuleClassementsPoule').resizable('destroy');
		$('#divModuleClassementsPoule').draggable().resizable({});*/
		$('#divVisuClassementsPoule').empty().append(html);
	});
}

// Module direct - But marqué match de poule
function module_directPoule_marquerBut(poule, match, equipe, equipeAB) {
	// Score de l'équipe A ou B modifié ?
	var score;
	if(equipeAB == 1)
		score = $('#txtScoreEquipeA_' + match).val();
	else
		score = $('#txtScoreEquipeB_' + match).val();
	
	if(score == '')
		score = 0;
	else
		score++;

	$.ajax(	{
				url: 'creer_prono_maj_prono_poule.php',
				type: 'POST',
				data:	{
							match: match,
							equipe: equipe,
							score: score
						}
			}
	).done(	function(html) {
		window.location.reload();
	});
}

// Module direct - Changement du score d'un match de phase finale
function module_directPhaseFinale_changerScoreMatch(match) {
	$.ajax(	{
				url: 'module_direct_phase_finale_changement_score.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(	function(html) {
		$('#divScoreMatch').empty().append(html);
		
		var maFenetre =
			$('#divScoreMatch').dialog	(	{
												autoOpen: true
												,width: 'auto'
												,height: 'auto'
												,modal: true
												,title: 'Score du match'
												,position: 'center'
												,buttons: {
													'Fermer':	function() {
														$(this).dialog('close');
														
														$.ajax(	{
																	url: 'creer_prono_maj_sequencement.php',
																	type: 'POST',
																	data:	{	match: match	}
																}
														).done(	function(html) {
															window.location.reload();
														});
													}
											}
										}
									);
	});
}

// Module direct - Sauvegarde du score d'un match de phase finale
function module_directPhaseFinale_sauvegarderScoreMatch(el, type, numeroMatch, equipeAB) {
	if(el == null || type == null || numeroMatch == 0 || numeroMatch == null)
		return;

	/*
		Voici les actions à entreprendre :
		- après une modification (score, score prolongation, sélection d'un vainqueur de TAB), on effectue la MAJ en BDD
		- si les scores 90' sont égaux, on affiche les scores AP
		- si les scores AP sont égaux, on affiche les TAB
	*/
	var score = 0;
	var vainqueur = 0;
	
	if(type == 'score' || type == 'scoreAP')
		score = el.value
	else if(type == 'vainqueur')
		vainqueur = el.value;

	$.ajax(	{
				url: 'module_direct_phase_finale_maj_score.php',
				type: 'POST',
				data:	{
							match: numeroMatch,
							type: type,
							equipeAB: equipeAB,
							score: score,
							vainqueur: vainqueur
						}
			}
	).done(	function(html) {
				// La réponse de la page indique s'il y a prolongation ou non et s'il y a TAB ou non
				// Si le score aller ou retour a été modifié, on regarde s'il faut afficher la zone de scores AP
				//$('#divInfo').empty().append(html);
				
				// La page d'enregistrement a vérifié qu'il était encore possible d'effectuer des modifications
				// Cela empêche qu'un utilisateur laisse sa page Internet active toute la journée avant de valider son pronostic
				if(html.indexOf('DEPASSE') != -1) {
					$('#divPronosticDepasse').html('<label>Désolé, il n\'est plus possible d\'effectuer de pronostic</label>');
					$('#divPronosticDepasse').dialog({
						autoOpen: false
						,width: 'auto'
						,height: 'auto'
						,modal: true
						,title: 'Heure de pronostic dépassée'
						,position: 'center'
						,buttons: {
							'Fermer':	function() {
											$(this).dialog('close');
										}
						}

					});

					$('#divPronosticDepasse').dialog('open');
					return;
				}

				if(type == 'score') {
					if(html.indexOf("PROLONGATION") != -1) {
						// La page de mise à jour a détecté qu'il fallait afficher les scores AP
						// On ne copie pas le score de la 90ème dans le score AP à cause de la notion du match en direct
						var minScoreEquipeA = $('#selectScoreEquipeA').val();
						var minScoreEquipeB = $('#selectScoreEquipeB').val();
						var i;
						$('#selectScoreAPEquipeA').empty();
						$('#selectScoreAPEquipeB').empty();
						
						$('#selectScoreAPEquipeA').append($('<option>', { value: -1, text: 'Score' }));
						for(i = minScoreEquipeA; i <= 15; i++) {
							$('#selectScoreAPEquipeA').append($('<option>', { value: i, text: i }));
						}
						
						$('#selectScoreAPEquipeB').append($('<option>', { value: -1, text: 'Score' }));
						for(i = minScoreEquipeB; i <= 15; i++) {
							$('#selectScoreAPEquipeB').append($('<option>', { value: i, text: i }));
						}
						
						$('#selectScoreAPEquipeA' + ' option:first').attr('selected', 'selected');
						$('#selectScoreAPEquipeB' + ' option:first').attr('selected', 'selected');
						$('#spanProlongationA_match_' + numeroMatch).css({'visibility': 'visible'})
						$('#spanProlongationB_match_' + numeroMatch).css({'visibility': 'visible'})
					}
					else {
						if($('#spanProlongationA_match_' + numeroMatch).length != 0)
							$('#spanProlongationA_match_' + numeroMatch).css({'visibility': 'hidden'});
						
						if($('#spanProlongationB_match_' + numeroMatch).length != 0)
							$('#spanProlongationB_match_' + numeroMatch).css({'visibility': 'hidden'});
					}

					// TAB ?
					if(html.indexOf("TAB") != -1)
						$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'visible'});
					else {
						// Le fait de ne pas afficher les TAB ne signifie pas forcément qu'il faille les effacer
						// En effet, dans les types de matches 1 et 2 (respectivement match de ligue 1 et match aller de LDC)
						// les TAB n'apparaissent jamais
						if($('#spanVainqueur_match_' + numeroMatch).length != 0)
							$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'hidden'});
					}
				}
				else if(type == 'scoreAP') {
					// TAB ?
					if(html.indexOf("TAB") != -1)
						$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'visible'});
					else {
						// Le fait de ne pas afficher les TAB ne signifie pas forcément qu'il faille les effacer
						// En effet, dans les types de matches 1 et 2 (respectivement match de ligue 1 et match aller de LDC)
						// les TAB n'apparaissent jamais
						if($('#spanVainqueur_match_' + numeroMatch).length != 0)
							$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'hidden'});
					}
				}
			}
	);
}

// Module direct - Rafraîchissement automatique (match de poule ou de phase finale)
function module_direct_rafraichirZone() {
	$.ajax(	{
				url: 'module_direct_poule.php',
				type: 'POST',
				data: { appelAjax: 1 }
			}
	
	).done(function(html) {
		if(html.length == 5) {
			clearInterval($('#txtDirect').val());
			$('#divDirect').empty();
		}
		else
			$('#divDirect').empty().append(html);
	});
}

// Module classement général - Affichage du classement général d'une journée
function module_classementGeneral_afficherJournee(journee) {
	if(journee == null)
		return;

	$.ajax(	{
				url: 'module_classement_general.php',
				type: 'POST',
				data:	{
							appelAjax: 1,
							journeeEnCours: journee
						}
			}
	).done(	function(html) {
		/*$('#divModuleClassementGeneral').empty().append(html);
		$('#divModuleClassementGeneral').resizable('destroy');
		$('#divModuleClassementGeneral').draggable().resizable({});*/
		$('#divClassementGeneral').empty().append(html);
	});
}

// Module classement général - Affichage des statistiques d'un pronostiqueur
function module_classementGeneral_afficherStats(pronostiqueur, pronostiqueurNom) {
	if(pronostiqueur == null || pronostiqueur == 0)
		return;
	
	$.ajax(	{
				url: 'module_classement_general_statistiques.php',
				type: 'POST',
				data:	{	pronostiqueur: pronostiqueur
						}
			}
	).done(function(html) {
		$('#divStats').empty().append(html);
		$('#divStats').dialog({
			autoOpen: true
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Statistiques de ' + pronostiqueurNom
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
			}

		});
		
	});
}

// Module classement général - Affichage de l'évolution du classement d'un pronostiqueur
function module_classementGeneral_afficherEvolution(pronostiqueur, pronostiqueurNom) {
	if(pronostiqueur == null || pronostiqueur == 0)
		return;
	
	$.ajax(	{
				url: 'module_classement_general_evolution.php',
				type: 'POST',
				data:	{	pronostiqueur: pronostiqueur
						}
			}
	).done(function(html) {
		$('#divEvolution').empty().append(html);
		$('#divEvolution').dialog({
			autoOpen: true
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Evolution du classement de ' + pronostiqueurNom
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
			}

		});
		
	});
}

// Module classement général - Affichage du détail des points
function module_classementGeneral_afficherPoints(pronostiqueur, pronostiqueurNom) {
	if(pronostiqueur == null || pronostiqueur == 0)
		return;
	
	$.ajax(	{
				url: 'module_classement_general_points.php',
				type: 'POST',
				data:	{	pronostiqueur: pronostiqueur
						}
			}
	).done(function(html) {
		$('#divPoints').empty().append(html);
		$('#divPoints').dialog({
			autoOpen: true
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Détail des points de ' + pronostiqueurNom
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
			}

		});
		
	});
}


// Connexion - Soumission de formulaire
function connexion_connecter(el) {
	var elt = document.forms['formConnexion'];
	if(elt != null)
		elt.submit();
}

// Création d'un pronostics - Rafraîchissement du tableau final
function creerProno_afficherTableau(html, nomDivTableau) {
	$.ajax(	{
		url: 'creer_prono_affichage_tableau.php',
		type: 'POST',
		data:	{	appelAjax: 1, tableau: nomDivTableau	}
	}).done(	function(html) {
					$('#' + nomDivTableau).empty().append(html);
				}
	);

}

// Création d'un pronostic - Sauvegarde d'un pronostic de poule
function creerProno_changerScorePoule(el, poule, match, equipe, nomDivClassement, nomDivTableau, nomBoutonEgalites) {
	if(el == null || match == 0 || match == null)
		return;

	var score = el.value;

	$.ajax(	{
				url: 'creer_prono_maj_prono_poule.php',
				type: 'POST',
				data:	{
							match: match,
							equipe: equipe,
							score: score
						}
			}
	).done(	function(html) {
				$('#divInfo').empty().append(html);
				// La réponse de la page indique :
				// - si l'heure de pronostic est dépassée
				// - s'il y a des cas d'égalité (à condition que tous les pronostics aient été saisis pour la poule)
				if(html.indexOf('DEPASSE') != -1) {
					$('#divPronosticDepasse').html('<label>Désolé, il n\'est plus possible d\'effectuer de pronostic</label>');
					$('#divPronosticDepasse').dialog({
						autoOpen: false
						,width: 'auto'
						,height: 'auto'
						,modal: true
						,title: 'Heure de pronostic dépassée'
						,position: 'center'
						,buttons: {
							'Fermer':	function() {
											$(this).dialog('close');
										}
						}

					});

					$('#divPronosticDepasse').dialog('open');
					return;
				}
				
				if(html.indexOf('EGALITE') != -1) {
					// Affichage du bouton de gestion des égalités
					$('#' + nomBoutonEgalites).show();
				}
				
				// Rafraîchissement du tableau des classements
				$.ajax(	{
					url: 'creer_prono_affichage_classement.php',
					type: 'POST',
					data:	{
								appelAjax: 1,
								poule: poule
							}
						}
				).done(	function(html) {
					$('#' + nomDivClassement).empty().append(html);
					
					// Rafraîchissement du tableau de la phase finale
					creerProno_afficherTableau(html, nomDivTableau);
				});
	});
}

// Création d'un pronostic - Changement du score d'un match de phase finale
function creerProno_changerScoreMatch(match, /*ordre,*/ nomDivTableau) {
	$.ajax(	{
				url: 'creer_prono_changement_score.php',
				type: 'POST',
				data:	{
							match: match/*,
							ordre: ordre*/
						}
			}
	).done(	function(html) {
		$('#divScoreMatch').empty().append(html);
		
		var maFenetre =
			$('#divScoreMatch').dialog	(	{
												autoOpen: true
												,width: 'auto'
												,height: 'auto'
												,modal: true
												,title: 'Score du match'
												,position: 'center'
												,buttons: {
													'Fermer':	function() {
														// Vérification de la saisie correcte des scores du match
														if($('#selectScoreEquipeA').val() == -1 || $('#selectScoreEquipeB').val() == -1)
														{
															alert('Score de l\'une ou des deux équipes non saisi');
															return;
														}
														else {
															if($('#selectScoreEquipeA').val() == $('#selectScoreEquipeB').val())
																if($('#selectScoreAPEquipeA').val() == $('#selectScoreAPEquipeB').val()) {
																	if($('#selectVainqueur').val() == 0) {
																		alert('Nom du vainqueur des tirs au but non saisi');
																		return;
																	}
																}
														}
													
														$(this).dialog('close');
														
														// Vérification de la suite de la compétition pour le match qui vient d'être modifié
														$.ajax(	{
																	url: 'creer_prono_maj_sequencement.php',
																	type: 'POST',
																	data:	{	match: match	}
																}
														).done(	function(html) {
															// Rafraîchissement du tableau de la phase finale
															creerProno_afficherTableau(html, nomDivTableau);
														});
													}
											}
										}
									);
	});
}

// Création d'un pronostic - Sauvegarde du score d'un match de phase finale
function creerProno_sauvegarderScoreMatch(el, type, numeroMatch, equipeAB) {
	if(el == null || type == null || numeroMatch == 0 || numeroMatch == null)
		return;

	/*
		Voici les actions à entreprendre :
		- après une modification (score, score prolongation, sélection d'un vainqueur de TAB), on effectue la MAJ en BDD
		- si les scores 90' sont égaux, on affiche les scores AP
		- si les scores AP sont égaux, on affiche les TAB
	*/
	
	var score = 0;
	var vainqueur = 0;
	
	if(type == 'score' || type == 'scoreAP')
		score = el.value
	else if(type == 'vainqueur')
		vainqueur = el.value;

	$.ajax(	{
				url: 'creer_prono_maj_score.php',
				type: 'POST',
				data:	{
							match: numeroMatch,
							type: type,
							equipeAB: equipeAB,
							score: score,
							vainqueur: vainqueur
						}
			}
	).done(	function(html) {
				// La réponse de la page indique s'il y a prolongation ou non et s'il y a TAB ou non
				// Si le score aller ou retour a été modifié, on regarde s'il faut afficher la zone de scores AP
				//$('#divInfo').empty().append(html);
				
				// La page d'enregistrement a vérifié qu'il était encore possible d'effectuer des modifications
				// Cela empêche qu'un utilisateur laisse sa page Internet active toute la journée avant de valider son pronostic
				if(html.indexOf('DEPASSE') != -1) {
					$('#divPronosticDepasse').html('<label>Désolé, il n\'est plus possible d\'effectuer de pronostic</label>');
					$('#divPronosticDepasse').dialog({
						autoOpen: false
						,width: 'auto'
						,height: 'auto'
						,modal: true
						,title: 'Heure de pronostic dépassée'
						,position: 'center'
						,buttons: {
							'Fermer':	function() {
											$(this).dialog('close');
										}
						}

					});

					$('#divPronosticDepasse').dialog('open');
					return;
				}

				if(type == 'score') {
					if(html.indexOf("PROLONGATION") != -1) {
						// La page de mise à jour a détecté qu'il fallait afficher les scores AP
						// On copie donc le score de la 90ème dans le score AP (en supprimant les scores inférieurs)
						var minScoreEquipeA = $('#selectScoreEquipeA').val();
						var minScoreEquipeB = $('#selectScoreEquipeB').val();
						var i;
						$('#selectScoreAPEquipeA').empty();
						$('#selectScoreAPEquipeB').empty();
						for(i = minScoreEquipeA; i <= 15; i++) {
							$('#selectScoreAPEquipeA').append($('<option>', { value: i, text: i }));
						}
						for(i = minScoreEquipeB; i <= 15; i++) {
							$('#selectScoreAPEquipeB').append($('<option>', { value: i, text: i }));
						}
						
						$('#selectScoreAPEquipeA' + ' option:first').attr('selected', 'selected');
						$('#selectScoreAPEquipeB' + ' option:first').attr('selected', 'selected');
						$('#spanProlongationA_match_' + numeroMatch).css({'visibility': 'visible'})
						$('#spanProlongationB_match_' + numeroMatch).css({'visibility': 'visible'})
					}
					else {
						if($('#spanProlongationA_match_' + numeroMatch).length != 0)
							$('#spanProlongationA_match_' + numeroMatch).css({'visibility': 'hidden'});
						
						if($('#spanProlongationB_match_' + numeroMatch).length != 0)
							$('#spanProlongationB_match_' + numeroMatch).css({'visibility': 'hidden'});
					}

					// TAB ?
					if(html.indexOf("TAB") != -1)
						$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'visible'});
					else {
						// Le fait de ne pas afficher les TAB ne signifie pas forcément qu'il faille les effacer
						// En effet, dans les types de matches 1 et 2 (respectivement match de ligue 1 et match aller de LDC)
						// les TAB n'apparaissent jamais
						if($('#spanVainqueur_match_' + numeroMatch).length != 0)
							$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'hidden'});
					}
				}
				else if(type == 'scoreAP') {
					// TAB ?
					if(html.indexOf("TAB") != -1)
						$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'visible'});
					else {
						// Le fait de ne pas afficher les TAB ne signifie pas forcément qu'il faille les effacer
						// En effet, dans les types de matches 1 et 2 (respectivement match de ligue 1 et match aller de LDC)
						// les TAB n'apparaissent jamais
						if($('#spanVainqueur_match_' + numeroMatch).length != 0)
							$('#spanVainqueur_match_' + numeroMatch).css({'visibility': 'hidden'});
					}
				}
			}
	);
}

// Création d'un pronostic - Recherche d'un joueur
function creerProno_rechercherJoueur(critereRecherche, nomDiv, nomZoneRecherche, nomZoneId, nomZoneNomJoueur) {
	// Recherche du joueur à partir de son nom
	// On va éventuellement remplacer les caractères génériques
	$.ajax(	{
				url: 'creer_prono_recherche_joueur.php',
				type: 'POST',
				data:	{	critereRecherche: critereRecherche,
							nomZoneRecherche: nomZoneRecherche,
							nomZoneId: nomZoneId,
							nomZoneNomJoueur: nomZoneNomJoueur
						}
			}
	).done(	function(html) {
				$(nomDiv).html(html);
			}
	);
}

// Création d'un pronostic - Sélection d'un joueur
function creerProno_selectionnerJoueur(joueur, nomJoueur, nomZoneRecherche, nomZoneId, nomZoneNomJoueur) {
	$(nomZoneRecherche).val(nomJoueur);
	$(nomZoneId).val(joueur);
	$(nomZoneNomJoueur).html('Votre choix du meilleur buteur sur <b>' + nomJoueur + '</b> a été pris en compte');
	
	// Sauvegarde du joueur sélectionné en base
	$.ajax(	{
				url: 'creer_prono_sauvegarde_buteur.php',
				type: 'POST',
				data:	{	joueur: joueur
						}

			}
	).done( function(html) {$('#divInfo').empty().append(html);}
	);
}

// Création d'un pronostic - Gestion des égalités
function creerProno_gererEgalites(poule, nomDivClassement, nomDivTableau, nomBoutonEgalites) {
	$.ajax(	{
				url: 'creer_prono_gestion_egalites.php',
				type: 'POST',
				data:	{
							poule: poule
						}
			}
	).done( function(html) {
		$('#divEgalites').empty().append(html);
		var fenetreEgalites = $('#divEgalites').dialog(	{
										autoOpen: true
										,width: 'auto'
										,height: 'auto'
										,modal: true
										,title: 'Gestion des égalités'
										,position: 'center'
										,buttons: {
											'Fermer':	function() {
												// Fermeture de la fenêtre
												$(this).dialog('close');
											},
											'Valider':	function() {
												// Mise à jour du classement final donné par le pronostiqueur
												// Lecture des choix d'équipe et de classement
												// La valeur contenue dans la liste est de la forme suivante :
												// - numéro d'équipe.classement provisoire
												// La mise à jour en BDD, par la suite, suppose que les équipes qui ont un classement provisoire se voient attribuer un classement final
												// 
												var param, i, j;
												i = 0;
												param = '';
												var valeurInitiale, numeroEquipe, numeroIndex, classementMin;
												var positionPoint;
												$('ul.listeTriee').each	(	function() {
														j = 0;
														$('li', this).each	(	function() {
																// Chaque paramètre contient pour une équipe :
																// - son numéro
																// - un index
																// - le classement min
																// Pour déterminer le nouveau classement
																// il faut additionner l'index et le classement min
																// Pour chaque équipe, on crée trois paramètres, un pour le numéro, un pour l'index, un pour le classement min
																valeurInitiale = $(this).data('val');
																positionPoint = valeurInitiale.indexOf('-');
																numeroEquipe = valeurInitiale.substring(0, positionPoint);
																classementMin = valeurInitiale.substring(positionPoint + 1, positionPoint + 2);
																
																if(param.length > 0)
																	param += '&equipe' + i + '=' + numeroEquipe + '&classement' + i + '=' + (parseInt(classementMin) + j);
																else
																	param = 'equipe' + i + '=' + numeroEquipe + '&classement' + i + '=' + (parseInt(classementMin) + j);
																i++; j++;
															}
														);
													}
												);

												param += '&equipes=' + i + '&poule=' + poule;
												
												// La variable param contient le nombre d'équipes, leur numéro et leur classement
												// Il faut à présent sauvegarder les informations en base
												$.ajax(	{
															url: 'creer_prono_maj_egalites.php',
															type: 'POST',
															data: param
														}
												).done	(	function(html) 	{
																// Fermeture de la fenêtre
																fenetreEgalites.dialog('close');
																
																// Masquage du bouton de gestion des égalités
																$('#' + nomBoutonEgalites).hide();
																
																// Rafraîchissement des classements et du tableau final
																$.ajax(	{
																	url: 'creer_prono_affichage_classement.php',
																	type: 'POST',
																	data:	{
																				appelAjax: 1,
																				poule: poule
																			}
																		}
																).done(	function(html) {
																	$('#' + nomDivClassement).empty().append(html);
																	
																	// Rafraîchissement du tableau de la phase finale
																	creerProno_afficherTableau(html, nomDivTableau);
																});
															}
												);
											}
										}
									}
								);
		$('#divEgalites').dialog('open');
	});
}

// Création d'un pronostic - Effacement des pronostics
function creerProno_effacerPronostics() {
	if(confirm('Etes-vous sûr de vouloir effacer tous vos pronostics ?')) {
		$.ajax(	{
					url: 'creer_prono_effacement_pronostics.php',
					type: 'POST'
				}
		).done( function(html) {
			$('#divReinitialisationPronostics').empty().append('Réinitialisation de vos pronostics effectuée avec succès');
			
			var fenetreReinitialisation = $('#divReinitialisationPronostics').dialog(	{
										autoOpen: true
										,width: 'auto'
										,height: 'auto'
										,modal: true
										,title: 'Réinitialisation des pronostics'
										,position: 'center'
										,buttons: {
											'Fermer':	function() {
												// Fermeture de la fenêtre
												$(this).dialog('close');
												
												// Rafraîchissement de la page
												location.reload();
											}
										}
									}
								);
			fenetreReinitialisation.dialog('open');
		});
	}
}

// Consultation d'un pronostic - Affichage de la page de sélection du pronostiqueur à consulter
function consulterPronostics() {
	$.ajax(	{
				url: 'consulter_prono_selection_pronostiqueur.php',
				type: 'POST'
			}
	).done(function(html) {
		$('body').append(html);
		$('#divPronostiqueursConsultables').dialog	(	{
															autoOpen: true
															,width: 'auto'
															,height: 'auto'
															,modal: true
															,title: 'Consulter les pronostics de...'
															,position: 'center'
															,buttons: {
																'Fermer':	function() {
																				$(this).dialog('close');
																	
																			}
															}
														}
													);
	});
}

// Envoi d'un courrier - Changement de journée
function envoyerCourrier_changerJournee() {
	var journee = $('#selectJourneesEnCours').val();
	
	if(journee == null || journee == 0) {
		return;
	}
	
	$.ajax(	{
				url: 'envoyer_courrier_chargement_journee.php',
				type: 'POST',
				data:	{
							journee: journee
						}
			}
	).done(function(html)	{
		CKEDITOR.instances.divMessage.setData(html);
	});
}

// Envoi de courrier - Sauvegarde d'un message
function envoyer_courrier_sauvegarderJournee(message) {
	var journee = $('#selectJourneesEnCours').val();
	
	if(journee == null || journee == 0) {
		return;
	}
	
	$.ajax(	{
				url: 'envoyer_courrier_sauvegarde_message.php',
				type: 'POST',
				data:	{
							journee: journee,
							message: message
						}
			}
	).done(function(html) {
		alert('Message sauvegardé');
	});
}

// Envoi de courrier - Envoi du courrier
function envoyer_courrier_envoyerCourrier() {
	var journee = $('#selectJourneesEnCours').val();
	
	if(journee == null || journee == 0) {
		return;
	}

	// Envoi du mail
	$.ajax(	{
				url: 'envoyer_courrier_envoi_courrier.php',
				type: 'POST',
				data:	{
							journee: journee
						}
			}
	).done(function(html) {
		alert('Courrier envoyé');
	});
}

// Match en direct - Bascule d'un match en direct / non en direct
function matches_direct_basculer_match(match) {
	if(match == null || match == 0)
		return;
	
	$.ajax(	{
				url: 'matches_direct_basculement_match.php',
				type: 'POST',
				data: {	match: match	}
			}
	).done(function(html) {
		// Recharge de la page pour prendre en compte le nouvel état
		location.reload();
	});
}

// Affichage de résultats - Changement de journée
function consulterResultats_changerJournee() {
	var journee = $('#selectJournee').val();

	if(journee == 0)
		return;

	$.ajax(	{
				url: 'consulter_resultats_resultats_pronostics.php',
				type: 'POST',
				data: { journee: journee }
			}
	).done(	function(html) {
				$('#divResultatsPronostics').html(html);
			}
	);

}

// Affichage de résultats - Détails d'un match
function consulterResultats_afficherMatch(match, equipeDomicile, equipeVisiteur) {
	$.ajax(
			{
				url: 'consulter_resultats_details_match.php',
				type: 'POST',
				data:	{	match: match
						}
			}
	).done(	function(html) {
				$('#divInfo').html(html);
				
				var fenetreDetails = $('#divInfo');
				$('#divInfo').dialog( {
					autoOpen: false
					,maxWidth: 900
					,width: 'auto'
					,height: 'auto'
					,modal: true
					,title: 'Détails du match ' + equipeDomicile + ' - ' + equipeVisiteur
					,position: 'center'
					,buttons: {
						'Fermer':	function() {
									fenetreDetails.dialog('close');
								}
					}

				});
				fenetreDetails.dialog('open');
			}
	);
}

// Affichage de résultats - Détails d'un pronostiqueur pour une journée
function consulterResultats_afficherPronostiqueur(pronostiqueur, pronostiqueurNom) {
	var journee = $('#selectJournee').val();

	$.ajax(
			{
				url: 'consulter_resultats_details_pronostiqueur.php',
				type: 'POST',
				data:	{	pronostiqueurDetail: pronostiqueur,
							journee: journee
						}
			}
	).done(	function(html) {
				$('#divInfo').html(html);
				
				var fenetreDetails = $('#divInfo');
				$('#divInfo').dialog( {
					autoOpen: false
					,maxWidth: 900
					,width: 'auto'
					,height: 'auto'
					,modal: true
					,title: 'Détails du pronostiqueur ' + pronostiqueurNom
					,position: 'center'
					,buttons: {
						'Fermer':	function() {
									fenetreDetails.dialog('close');
								}
					}

				});
				fenetreDetails.dialog('open');
			}
	);
}

// Calcul de résultats - Calcul des résultats
function calculerResultats_calculerResultats() {
	var journee = $('#selectJournee').val();

	$('#divInfo').html('');
	$.ajax(
			{
				url: 'calculer_resultats_calcul_resultats.php',
				type: 'POST',
				data:	{	journee: journee
						}
			}
	).done(	function(html) {
				$('#divInfo').html(html);
				var fenetreInfo = $('#divInfo');
				$('#divInfo').dialog( {
					autoOpen: false
					,width: 400
					,height: 200
					,modal: true
					,title: 'Calcul des scores'
					,position: 'center'
					,buttons: {
						'Fermer':	function() {
									fenetreInfo.dialog('close');
								}
					}

				});
				fenetreInfo.dialog('open');
			}
	);
}

// Visualisation des statistiques - Equipes qualifiées en huitièmes de finale - Nom des pronostiqueurs les ayant trouvées
function voirStatistiques_afficherPronostiqueursHuitiemes(equipe) {
	$.ajax(
			{
				url: 'voir_statistiques_affichage_pronostiqueurs_huitiemes.php',
				type: 'POST',
				data:	{ equipe: equipe }
			}
	).done(function(html) {
		$('#divPronostiqueursEquipesQualifiees').empty().append(html);
		$('#divPronostiqueursEquipesQualifiees').dialog	(	{
																autoOpen: true
																,width: 600
																,height: 'auto'
																,modal: true
																,title: 'Ont trouvé l\'équipe'
																,position: 'center'
																,buttons: {
																	'Fermer':	function() {
																		// Fermeture de la fenêtre
																		$(this).dialog('close');
																	}
																}
															}
														);
		
	});
}

// Visualisation des statistiques - Equipes qualifiées en quarts de finale - Nom des pronostiqueurs les ayant trouvées
function voirStatistiques_afficherPronostiqueursQuarts(equipe) {
	$.ajax(
			{
				url: 'voir_statistiques_affichage_pronostiqueurs_quarts.php',
				type: 'POST',
				data:	{ equipe: equipe }
			}
	).done(function(html) {
		$('#divPronostiqueursEquipesQualifiees').empty().append(html);
		$('#divPronostiqueursEquipesQualifiees').dialog	(	{
																autoOpen: true
																,width: 600
																,height: 'auto'
																,modal: true
																,title: 'Ont trouvé l\'équipe'
																,position: 'center'
																,buttons: {
																	'Fermer':	function() {
																		// Fermeture de la fenêtre
																		$(this).dialog('close');
																	}
																}
															}
														);
		
	});
}

// Visualisation des statistiques - Equipes qualifiées en demi-finales - Nom des pronostiqueurs les ayant trouvées
function voirStatistiques_afficherPronostiqueursDemi(equipe) {
	$.ajax(
			{
				url: 'voir_statistiques_affichage_pronostiqueurs_demi.php',
				type: 'POST',
				data:	{ equipe: equipe }
			}
	).done(function(html) {
		$('#divPronostiqueursEquipesQualifiees').empty().append(html);
		$('#divPronostiqueursEquipesQualifiees').dialog	(	{
																autoOpen: true
																,width: 600
																,height: 'auto'
																,modal: true
																,title: 'Ont trouvé l\'équipe'
																,position: 'center'
																,buttons: {
																	'Fermer':	function() {
																		// Fermeture de la fenêtre
																		$(this).dialog('close');
																	}
																}
															}
														);
		
	});
}

// Visualisation des statistiques - Equipes dans la petite finale - Nom des pronostiqueurs les ayant trouvées
function voirStatistiques_afficherPronostiqueursPetiteFinale(equipe) {
	$.ajax(
			{
				url: 'voir_statistiques_affichage_pronostiqueurs_petite_finale.php',
				type: 'POST',
				data:	{ equipe: equipe }
			}
	).done(function(html) {
		$('#divPronostiqueursEquipesQualifiees').empty().append(html);
		$('#divPronostiqueursEquipesQualifiees').dialog	(	{
																autoOpen: true
																,width: 600
																,height: 'auto'
																,modal: true
																,title: 'Ont trouvé l\'équipe'
																,position: 'center'
																,buttons: {
																	'Fermer':	function() {
																		// Fermeture de la fenêtre
																		$(this).dialog('close');
																	}
																}
															}
														);
		
	});
}

// Visualisation des statistiques - Equipes en finale - Nom des pronostiqueurs les ayant trouvées
function voirStatistiques_afficherPronostiqueursFinale(equipe) {
	$.ajax(
			{
				url: 'voir_statistiques_affichage_pronostiqueurs_finale.php',
				type: 'POST',
				data:	{ equipe: equipe }
			}
	).done(function(html) {
		$('#divPronostiqueursEquipesQualifiees').empty().append(html);
		$('#divPronostiqueursEquipesQualifiees').dialog	(	{
																autoOpen: true
																,width: 600
																,height: 'auto'
																,modal: true
																,title: 'Ont trouvé l\'équipe'
																,position: 'center'
																,buttons: {
																	'Fermer':	function() {
																		// Fermeture de la fenêtre
																		$(this).dialog('close');
																	}
																}
															}
														);
		
	});
}
