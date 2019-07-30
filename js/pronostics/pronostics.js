// Numéro du module de tchat
var numeroModuleTchat = 50;

// Gestion du son
var objetSon;

// Concours centre
var cc_ongletActif = 0;
var cc_sousOngletActif = 0;
var cc_pronostiqueurConsulte = 0;


// Mise en place de la notification de présence de l'utilisateur
var timerActivite = 0;
var intervalleActivite = 20000;
function activitePronostiqueur() {
	if(timerActivite == 0) {
		timerActivite = setInterval(function() {
			activitePronostiqueur_rafraichissement();

		}, intervalleActivite);
	}
}

// Notification de présence de l'utilisateur - Rafraîchissement
function activitePronostiqueur_rafraichissement() {
	$.ajax(	{
				url: 'activite_pronostiqueur.php',
				type: 'POST'
			}

	).done(function(html) {
	});
}

// Mise en place de la vérification de message
var timerMessage = 1;
var intervalleMessage = 5000;
function verificationMessage() {
	if(timerMessage == 0) {
		timerMessage = setInterval(function() {
			verificationMessage_rafraichissement();

		}, intervalleMessage);
	}
}

// Vérification de message - Rafraîchissement
function verificationMessage_rafraichissement() {
	$.ajax(	{
				url: 'verifier_messages.php',
				type: 'POST',
				dataType: 'json'
			}

	).done(function(html) {
		if(html.nombreMessagesConversationsNonLues != '0') {
			if(!$('#liTchat').hasClass('rouge')) {
				$('#liTchat').addClass('rouge');

				soundManager.setup({
					url: 'swf/',
					onready: function() {
						objetSon = soundManager.createSound({
							id : 'objetSon',
							url : 'sons/message.mp3'
						});
						objetSon.play();
					}
				});
			}

			// Pour le Match centre, il y a aussi une icône des messages non lus
			// Elle est normalement cachée, sauf en cas de nouveau message
			if($('#mc--bulle-tchat').length) {
				$('#mc--bulle-tchat').css('display', 'inline-block');
			}
		}
		else {
			$('#liTchat').removeClass('rouge');
			if($('#mc--bulle-tchat').length)
				$('#mc--bulle-tchat').css('display', 'none');
		}

		if(html.nombreMessagesTchatGroupeNonLus != '0') {
			$('#liTchatGroupe').addClass('rouge');
		}
		else
			$('#liTchatGroupe').removeClass('rouge');
	}).fail(function(html) { console.log('Fonction verificationMessage_rafraichissement : dans le fail'); });
}

// Affichage du titre de la page
function afficherTitrePage(element, titrePage) {
    if(element[0] != '.')
		$('#' + element).prepend('<h1>' + titrePage + '</h1>');
	else
		$(element).prepend('<h1>' + titrePage + '</h1>');

    retournerHautPage();
    activitePronostiqueur();
	verificationMessage();

}

// Affichage d'un message d'information
// Cette fonction permet également de basculer sur une autre page si celle-ci est mentionnée
function afficherMessageInformation(titre, message, page) {
	if($('.info').length == 0)
		$('body').append('<div class="info" style="z-index: 20000;"></div>');

	$('.info').empty().append('<label>' + message + '</label>');
    $('.info').dialog({
        title: titre,
        modal: true,
        autoOpen: true
        ,width: 'auto'
        ,height: 'auto'
        ,position: 'center'
        ,buttons:   {
                        'Fermer':   function() {
                                        $(this).dialog('close');
                                        if(page != '')
                                            window.open(page, '_self');
                        }
                    }
    });
}

// Affichage d'un message d'information dans un bandeau
// Le bandeau disparaît automatiquement après un laps de temps indiqué
function afficherMessageInformationBandeau(message, delai, page) {
	// Création d'une zone d'information
	if($('.bandeau-info').length == 0)
		$('body').append('<div class="bandeau-info"><label>' + message + '</label></div>');

	$('.bandeau-info').position({
		my: "left top",
		at: "left top",
		of: window
	});

	$('.bandeau-info').fadeIn('fast').delay(delai).fadeOut('slow', function() {
		this.remove();
		if(page != '')
			window.open(page, '_self');
	});
}

// Affichage du bouton "retour haut de page" lorsque le scroll dépasse une certaine valeur
function retournerHautPage() {
    $('html body').prepend('<a href="#" class="retour-haut-page">&nbsp;</a>');

    $(window).scroll(function() {
        if ($(this).scrollTop() > 80) {
            $('.retour-haut-page').fadeIn();
        } else {
            $('.retour-haut-page').fadeOut();
        }
    });

    $('.retour-haut-page').click(function() {
        $('html, body').animate({scrollTop : 0}, 500);
        return false;
    });
}

// Changement de thème de l'interface utilisateur
function changerTheme(theme) {
	// Mise à jour du thème en base de données
	$.ajax(	{
				url: 'changement_theme.php',
				type: 'POST',
				data:	{
							theme: theme
						}
			}
	).done(function(html) {
		// Rechargement de la page
		location.reload();
	});

}

// Centrage horizontal d'un élément
function centrerObjet(element, centrerHorizontalement, centrerVerticalement) {
	var objetElement;
	if(element[0] != '.')
		objetElement = $('#' + element);
	else
		objetElement = $(element);

	if(centrerHorizontalement == 1) {
		var x = $(window).width() / 2 - (objetElement.width() / 2);
		objetElement.css('left', x + 'px');
	}

	if(centrerVerticalement == 1) {
		var y = $(window).height() / 2 - (objetElement.height() / 2);
		objetElement.css('top', y + 'px');
	}
}

// Affichage / masquage d'un élément
function afficherMasquerObjet(element) {
	$('#' + element).slideToggle(400);
}

// Changement de la couleur d'une liste pour refléter un choix non effectué
function modifierCouleur(elt, valeur, classe) {
	if($(elt).find(":selected").val() == valeur) {
		$(elt).addClass(classe);
	}
	else {
		$(elt).removeClass(classe);
	}
}

// Enregistrement règlement LDC
function enregistrerReglement(championnat) {
	var reglement = CKEDITOR.instances.txtReglement.getData();
	$.ajax(	{
				url: 'reglement_maj.php',
				type: 'POST',
				data:	{
							championnat: championnat,
							reglement: reglement
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Règlement sauvegardé avec succès', 2000, '');
	});
}

// Création de compte-rendu - Enregistrement compte-rendu
function enregistrerCompteRendu() {
	var compteRendu = CKEDITOR.instances.txtCompteRendu.getData();
	$.ajax(	{
				url: 'creer_compte_rendu_maj_compte_rendu.php',
				type: 'POST',
				data:	{
							compte_rendu: compteRendu
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Compte-rendu sauvegardé avec succès', 2000, '');
	});
}


// Connexion - Soumission de formulaire
function connexion_connecter(el) {
    var elt = document.forms['formConnexion'];
    if(elt != null)
        elt.submit();
}

// Première connexion - Soumission de formulaire
function premiereConnexion_validerMotDePasse() {
    var elt = document.forms['formModificationMotDePasse'];
    if(elt != null)
        elt.submit();
}

// Modification de mot de passe - Soumission de formulaire
function modifierMotDePasse_validerMotDePasse() {
    var elt = document.forms['formModificationMotDePasse'];
    if(elt != null)
        elt.submit();
}

// Affichage / masquage d'une option de menu
function menu_basculerAffichage(menu) {
	$.ajax(	{
				url: 'menu_bascule_affichage.php',
				type: 'POST',
				data: { menu: menu    }
	});
}

// Gestion de match - Changement de championnat
function creerMatch_changerChampionnat() {
    var championnat = $('#selectChampionnat').val();

    if(championnat == 0) {
        $('#spanListeJournees').html('');
        $('#divListeMatches').html('');
        return;
    }

    $.ajax( {
                url: 'creer_match_liste_journees.php',
                type: 'POST',
                data: { championnat: championnat    }
            }
    ).done(function(html)  {
                $('#spanListeJournees').html(html);
                $('#divListeMatches').html('');
            }
    );
}

// Gestion de match - Changement de journée
function creerMatch_changerJournee() {
    var journee = $('#selectJournee').val();

    if(journee == 0)
        return;

    $.ajax( {
                url: 'creer_match_liste_matches.php',
                type: 'POST',
                data: { journee: journee }
            }
    ).done(function(html) {
                $('#divListeMatches').html(html);
            }
    );
}

// Gestion de match - Initialisation du match Canal
function creerMatch_initialiserMatchCanal(numeroJournee) {
    if(numeroJournee == 0 || numeroJournee > 38)
		return;
		
	// Appel de la page d'initialisation des matches Canal pour les pronostiqueurs
	$.ajax( {
		url: 'creer_match_initialisation_canal.php',
		type: 'POST',
		data:	{
					journee: numeroJournee
				}
	}).done(function(html) {
		afficherMessageInformationBandeau('Initialisation du match Canal effectuée avec succès', 2000, '');
	});
}

// Gestion de match - Activation / désactivation d'une journée
function creerMatch_activerDesactiverJournee() {
    var journee = $('#selectJournee').val();

    if(journee == 0)
        return;

    $.ajax( {
                url: 'creer_match_activation_journee.php',
                type: 'POST',
                data:   {
                            journee: journee
                        }
            }
    ).done(function(html) {
		$('#labelEtatJournee').html(html);
	});
}

// Gestion de match - Evénement sur un match (et donc écrit dans la journée)
function creerMatch_ecrireEvenement(numeroMatch, evenement) {
	$.ajax(	{
				url: 'creer_match_maj_evenement.php',
				type: 'POST',
				data:	{
							match: numeroMatch,
							evenement: evenement
						}
			}
	).done(function(html) {
	});
}

// Gestion de match - Sauvegarde d'un match
// Le code événement permet de mettre à jour, éventuellement, la journée pour indiquer qu'une mise à jour a été effectuée
// Cela sert au module d'affichage des données sur une journée
// Le paramètre element désigne le contrôle qui a généré l'événement
// Dans le cas d'un changement de score, par exemple, si le score est mis à 0, il n'est pas nécessaire de noter l'événement
// puisque cela correspond au début du match
// Dans le cas d'un changement de vainqueur de TAB, si la valeur est -1, c'est qu'on a remis la zone à zéro, etc.
function creerMatch_sauvegarderMatch(evenement, element, numeroMatch) {
    if(numeroMatch == 0 || numeroMatch == null)
        return;

    var equipeD = $('#equipeD_match_' + numeroMatch).val();                                     // Equipe domicile
    var equipeV = $('#equipeV_match_' + numeroMatch).val();                                     // Equipe visiteur
    var coteEquipeD = $('#coteEquipeD_match_' + numeroMatch).val();                             // Cote équipe domicile
    var coteNul = $('#coteNul_match_' + numeroMatch).val();                                     // Cote du match nul
    var coteEquipeV = $('#coteEquipeV_match_' + numeroMatch).val();                             // Cote équipe visiteur
    var dateDebut = $('#dateDebut_match_' + numeroMatch).val();                                 // Date de début du match
    var heureDebut = $('#heureDebut_match_' + numeroMatch).val();                               // Heure de début du match
    var minuteDebut = $('#minuteDebut_match_' + numeroMatch).val();                             // Minute de début du match
    var scoreEquipeD = $('#scoreEquipeD_match_' + numeroMatch).val();                           // Score équipe docmicile
    var scoreEquipeV = $('#scoreEquipeV_match_' + numeroMatch).val();                           // Score équipe visiteur
    var scoreAPEquipeD = $('#scoreAPEquipeD_match_' + numeroMatch).val();                       // Score AP équipe docmicile
    var scoreAPEquipeV = $('#scoreAPEquipeV_match_' + numeroMatch).val();                       // Score AP équipe visiteur
    var vainqueur = $('#vainqueur_match_' + numeroMatch).val();                                 // Vainqueur du match
    var matchCanal = $('#matchCanal_match_' + numeroMatch).prop('checked') ? 1 : 0;             // Match Canal
    var report = $('#report_match_' + numeroMatch).prop('checked') ? 1 : 0;                     // Match reporté
    var matchCS = $('#matchCS_match_' + numeroMatch).prop('checked') ? 1 : 0;                   // Match de la Community Shield
	var matchIgnore = $('#matchIgnore_match_' + numeroMatch).prop('checked') ? 1 : 0;			// Match ignoré de la surveillance
	var matchHorsPronostic = $('#matchHorsPronostic_match_' + numeroMatch).prop('checked') ? 1 : 0;			// Match ignoré des points des pronostiqueurs

    var pointsQualificationEquipeD = null;
    if($('#pointsQualificationEquipeD_match_' + numeroMatch).length != 0)
        pointsQualificationEquipeD = $('#pointsQualificationEquipeD_match_' + numeroMatch).val();	// Points de qualification équipe domicile

    var pointsQualificationEquipeV = null;
    if($('#pointsQualificationEquipeV_match_' + numeroMatch).length != 0)
        pointsQualificationEquipeV = $('#pointsQualificationEquipeV_match_' + numeroMatch).val();	// Points de qualification équipe visiteur

    var matchDirect = $('#matchDirect_match_' + numeroMatch).prop('checked') ? 1 : 0;				// Match en direct

	var matchLienPage = $('#lien_match_' + numeroMatch).val();										// Lien page de MAJ
	var matchLienPageComplementaire = $('#lien_match_complementaire_' + numeroMatch).val();			// Lien page complémentaire de MAJ

    // Match avec prolongation (match de Coupe), est également utilisé en championnat (match 11)
    // Mis à jour uniquement si la case à cocher est visible (cas du match 11 de ligue 1)
    var matchAP = null;
    if($('#matchAP_match_' + numeroMatch).length != 0)
        matchAP = $('#matchAP_match_' + numeroMatch).prop('checked') ? 1 : 0;

    var nomMatch = $('#nomMatch_match_' + numeroMatch).val();                                   // Nom du match européen

    // Appel de la page de sauvegarde du match avec les paramètres
    $.ajax( {
                url: 'creer_match_maj_match.php',
                type: 'POST',
                data:   {
                            match: numeroMatch,
                            equipeD: equipeD,
                            equipeV: equipeV,
                            coteEquipeD: coteEquipeD,
                            coteNul: coteNul,
                            coteEquipeV: coteEquipeV,
                            dateDebut: dateDebut,
                            heureDebut: heureDebut,
                            minuteDebut: minuteDebut,
                            scoreEquipeD: scoreEquipeD,
                            scoreEquipeV: scoreEquipeV,
                            scoreAPEquipeD: scoreAPEquipeD,
                            scoreAPEquipeV: scoreAPEquipeV,
                            vainqueur: vainqueur,
                            matchCanal: matchCanal,
                            report: report,
                            matchCS: matchCS,
                            matchAP: matchAP,
                            nomMatch: nomMatch,
                            pointsQualificationEquipeD: pointsQualificationEquipeD,
                            pointsQualificationEquipeV: pointsQualificationEquipeV,
                            matchDirect: matchDirect,
							matchLienPage: matchLienPage,
							matchLienPageComplementaire: matchLienPageComplementaire,
							matchIgnore : matchIgnore,
							matchHorsPronostic: matchHorsPronostic
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div');

        $('.info').empty().append(html);
		// Si l'événement généré est différent de 0, alors on doit regarder de quoi il s'agit
		switch(evenement) {
			case 1: // Match en direct ou non
				creerMatch_ecrireEvenement(numeroMatch, evenement);
			break;
			case 2:
			case 3: // Changement de score ou de vainqueur de TAB
				// Dans tous les cas, même si le score repasse à 0 ou que le vainqueur de TAB est réinitialisé, il est nécessaire de faire le rafraîchissement du module
				// Mais, l'événement (par exemple un but marqué) n'est indiqué que s'il a eu lieu (score différent de 0)
				if($('#' + element + ' option:selected').val() > 0)
					creerMatch_ecrireEvenement(numeroMatch, evenement);
				else
					creerMatch_ecrireEvenement(numeroMatch, 0);
			break;
		}
	});
}

// Gestion de match - Affichage points de qualification d'une équipe
function creerMatch_afficherPointsQualification(numeroMatch) {
    if(numeroMatch == 0 || numeroMatch == null)
        return;

    var checked = $('#matchAP_match_' + numeroMatch).prop('checked') ? 1 : 0;
    if(checked == 1)
        $('#spanPointsQualification_match_' + numeroMatch).css({'visibility': 'visible'});
    else {
        $('#spanPointsQualification_match_' + numeroMatch).css({'visibility': 'hidden'});
        $('#pointsQualificationEquipeD_match_' + numeroMatch).val('');
        $('#pointsQualificationEquipeV_match_' + numeroMatch).val('');
    }
}

// Gestion de match - Confirmation de la liste des joueurs ayant participé à un match
function creerMatch_confirmerParticipants(numeroMatch, equipe) {
    // Lecture des valeurs saisies par l'utilisateur dans l'interface
    var numeroEquipe = 0;
    var dateDebutMatch = null;

    if(numeroMatch == 0 || numeroMatch == null)
        return;

    // Si le paramètre equipe vaut 0, on prend l'équipe domicile sinon c'est l'équipe visiteur
    if(equipe == 0) {
        numeroEquipe = $('#equipeD_match_' + numeroMatch).val();
        if(numeroEquipe == 0) {
            alert('Veuillez choisir une équipe domicile');
            return;
        }
    }
    else {
        numeroEquipe = $('#equipeV_match_' + numeroMatch).val();
        if(numeroEquipe == 0) {
            alert('Veuillez choisir une équipe visiteur');
            return;
        }
    }

    dateDebutMatch = $('#dateDebut_match_' + numeroMatch).val();
    if(dateDebutMatch == '') {
        alert('Veuillez choisir une date');
        return;
    }

    // Appel de la page de sélection des joueurs
    $.ajax( {
                url: 'creer_match_liste_participants.php',
                type: 'POST',
                data:   {
                            match: numeroMatch,
                            equipe: numeroEquipe,
                            date: dateDebutMatch
                        }
            }
    ).done(function(html) {
		if($('.listeParticipants').length == 0)
			$('body').append('<div class="listeParticipants"></div>');

		$('.listeParticipants').empty().append(html);
		$('.listeParticipants').dialog({
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Sélection des participants'
			,position: 'center'
			,buttons: {
				'Valider':  function() {
								$(this).dialog('close');
								var param = 'match=' + numeroMatch;
								param += '&equipe=' + numeroEquipe;

								var i = 0;
								$('.participants li').each    (   function() {
																				param += '&joueur' + i++ + '=' + $(this).attr('value');
																			}
																		);
								param += '&joueurs=' + i;
								$.ajax( {
											url: 'creer_match_maj_participants.php',
											type: 'POST',
											data: param
										}
								).done(function(html) {
								});
							},
				'Annuler':  function() {
								$(this).dialog('close');
							}
			}

		});

		$('.listeParticipants').dialog('open');
		$('.participants').on('click', 'li',  function() {
			var numeroJoueur = $(this).attr('value');
			var nomJoueur = $(this).text();
			var classe = $(this).attr('class');
			$(this).remove();
			$('.effectif').append($('<li>', { value: numeroJoueur, text: nomJoueur, class: classe }));
		});

		$('.effectif').on('click', 'li',  function() {
			var numeroJoueur = $(this).attr('value');
			var nomJoueur = $(this).text();
			var classe = $(this).attr('class');
			$(this).remove();
			$('.participants').append($('<li>', { value: numeroJoueur, text: nomJoueur, class: classe }));
		});
	});
}

// Gestion de match - Détection des cotes des buteurs v1
function creerMatch_detecterCotesV1(numeroMatch) {
    // Lecture des valeurs saisies par l'utilisateur dans l'interface
    var numeroEquipeDomicile = 0;
    var numeroEquipeVisiteur = 0;

    if(numeroMatch == 0 || numeroMatch == null)
        return;

    numeroEquipeDomicile = $('#equipeD_match_' + numeroMatch).val();
    numeroEquipeVisiteur = $('#equipeV_match_' + numeroMatch).val();
    if(numeroEquipeDomicile == 0 || numeroEquipeVisiteur == 0) {
        alert('Veuillez renseigner les deux équipes');
        return;
    }

    dateDebutMatch = $('#dateDebut_match_' + numeroMatch).val();
    if(dateDebutMatch == '') {
        alert('Veuillez choisir une date');
        return;
    }

    // Appel de la page de détection des cotes joueurs
    $.ajax( {
                url: 'creer_match_detection_cotes.php',
                type: 'POST'
            }
    ).done(function(html) {
		if($('.cotesJoueurs').length == 0)
			$('body').append('<div class="cotesJoueurs"></div>');

		$('.cotesJoueurs').empty().append(html);
		$('.cotesJoueurs').dialog({
				autoOpen: true
				,width: 'auto'
				,height: 'auto'
				,modal: true
				,title: 'Insertion des cotes'
				,position: 'center'
				,buttons: {
					'Valider':  function() {
                        $(this).dialog('close');

                        // Lecture des différentes cotes saisies
                        var listeCotesJoueurs = $('#txtCotesJoueurs').val();

                        // Appel de la page de détection des cotes joueurs
                        $.ajax(	{
                                    url: 'creer_match_maj_par_detection_cotes.php',
                                    type: 'POST',
                                    data:   {
                                                match: numeroMatch,
                                                equipe_domicile: numeroEquipeDomicile,
                                                equipe_visiteur: numeroEquipeVisiteur,
                                                date_debut_match: dateDebutMatch,
                                                liste_cotes_joueurs: listeCotesJoueurs
                                            },
                                    dataType: 'json'
                                }
                        ).done(function(html) {
                            //console.log(html);
                            if(html.nombre_cotes_detectees == 0) {
                                alert('Aucune cote détectée');
                                return;
                            }

                            if(html.nombre_joueurs_inconnus > 0) {
                                // Affichage d'une fenêtre de mise à jour des données des joueurs pour lesquels la recherche a été infructueuse
                                $.ajax(	{
                                            url: 'creer_match_correction_cotes.php',
                                            type: 'POST',
                                            data:	{	match: numeroMatch,
                                                        joueurs_inconnus_equipe_domicile: html.joueurs_inconnus_equipe_domicile,
                                                        joueurs_inconnus_equipe_visiteur: html.joueurs_inconnus_equipe_visiteur
                                                    }
                                }).done(function(html) {
                                    if($('.listeJoueurs').length == 0)
                                        $('body').append('<div class="listeJoueurs"></div>');


                                        $('.listeJoueurs').empty().append(html);
                                        $('.listeJoueurs').dialog({
                                                autoOpen: true
                                                ,width: 'auto'
                                                ,height: 'auto'
                                                ,modal: true
                                                ,title: 'Correction des joueurs non trouvés'
                                                ,position: 'center'
                                                ,buttons: {
                                                    'Fermer':  function() {
                                                        $(this).dialog('close');
                                                    }
                                                }
                                        });
                                });
                            }
                            else
                                afficherMessageInformationBandeau('Lecture des cotes des joueurs effectuée avec succès', 2000, '');
                        }).fail(function(html) {
                            console.log('Fonction creerMatch_detecterCotesV1 : dans le fail - ' + html);
                        });
                    },
                    'Annuler':  function() {
						$(this).dialog('close');
					}
                }
        });
    });
}

// Gestion de match - Détection des cotes des buteurs v2
function creerMatch_detecterCotesV2(numeroMatch) {
	// Lecture des valeurs saisies par l'utilisateur dans l'interface
    var numeroEquipeDomicile = 0;
    var numeroEquipeVisiteur = 0;

    if(numeroMatch == 0 || numeroMatch == null)
        return;

    numeroEquipeDomicile = $('#equipeD_match_' + numeroMatch).val();
    numeroEquipeVisiteur = $('#equipeV_match_' + numeroMatch).val();
    if(numeroEquipeDomicile == 0 || numeroEquipeVisiteur == 0) {
        alert('Veuillez renseigner les deux équipes');
        return;
    }

    dateDebutMatch = $('#dateDebut_match_' + numeroMatch).val();
    if(dateDebutMatch == '') {
        alert('Veuillez choisir une date');
        return;
    }

    // Appel de la page de détection des cotes joueurs
    $.ajax( {
                url: 'creer_match_detection_cotes_v2.php',
                type: 'POST'
            }
    ).done(function(html) {
		if($('.cotesJoueurs').length == 0)
			$('body').append('<div class="cotesJoueurs"></div>');

		$('.cotesJoueurs').empty().append(html);
		$('.cotesJoueurs').dialog({
				autoOpen: true
				,width: 'auto'
				,height: 'auto'
				,modal: true
				,title: 'Insertion des cotes'
				,position: 'center'
				,buttons: {
					'Valider':  function() {
                        $(this).dialog('close');

                        // Lecture des différentes cotes saisies
                        var listeCotesJoueurs = $('#txtCotesJoueurs').val();

                        // Appel de la page de détection des cotes joueurs
                        $.ajax(	{
                                    url: 'creer_match_maj_par_detection_cotes_v2.php',
                                    type: 'POST',
                                    data:   {
                                                match: numeroMatch,
												equipe_domicile: numeroEquipeDomicile,
                                                equipe_visiteur: numeroEquipeVisiteur,
                                                date_debut_match: dateDebutMatch,
                                                liste_cotes_joueurs: listeCotesJoueurs
                                            },
                                    dataType: 'json'
                                }
                        ).done(function(html) {
                            if(html.nombre_cotes_detectees == 0) {
                                alert('Aucune cote détectée');
                                return;
                            }

                            if(html.nombre_joueurs_inconnus > 0) {
                                // Affichage d'une fenêtre de mise à jour des données des joueurs pour lesquels la recherche a été infructueuse
                                $.ajax(	{
                                            url: 'creer_match_correction_cotes_v2.php',
                                            type: 'POST',
                                            data:	{	match: numeroMatch,
														joueurs_inconnus_equipe_domicile: html.joueurs_inconnus_equipe_domicile,
                                                        joueurs_inconnus_equipe_visiteur: html.joueurs_inconnus_equipe_visiteur
                                                    }
                                }).done(function(html) {
                                    if($('.listeJoueurs').length == 0)
                                        $('body').append('<div class="listeJoueurs"></div>');

                                        $('.listeJoueurs').empty().append(html);
                                        $('.listeJoueurs').dialog({
                                                autoOpen: true
                                                ,width: 'auto'
                                                ,height: 'auto'
                                                ,modal: true
                                                ,title: 'Correction des joueurs non trouvés'
                                                ,position: 'center'
                                                ,buttons: {
                                                    'Fermer':  function() {
                                                        $(this).dialog('close');
                                                    }
                                                }
                                        });
                                });
                            }
                            else
                                afficherMessageInformationBandeau('Lecture des cotes des joueurs effectuée avec succès', 2000, '');
                        }).fail(function(html) {
                            console.log('Fonction creerMatch_detecterCotesV2 : dans le fail - ' + html);
                        });
                    },
                    'Annuler':  function() {
						$(this).dialog('close');
					}
                }
        });
    });
}

// Gestion de match - Remplissage des cotes buteurs
function creerMatch_remplirCotes(numeroMatch) {
    if(confirm('Etes-vous sûr de vouloir effectuer le remplissage automatique ? Les cotes existantes ne sont pas effacées !')) {
        // Appel de la page de saisie des cotes joueurs
        $.ajax( {
                    url: 'creer_match_remplissage_cotes.php',
                    type: 'POST',
                    data:	{
                                match: numeroMatch
                            }
                }
        ).done(function(html) {
            afficherMessageInformationBandeau('Cotes buteur remplies automatiquement', 2000, '');
        });
    }
}

// Gestion de match - Saisie des cotes des buteurs
function creerMatch_saisirCotes(numeroMatch, equipe) {
    // Lecture des valeurs saisies par l'utilisateur dans l'interface
    var numeroEquipe = 0;

    if(numeroMatch == 0 || numeroMatch == null)
        return;

    // Si le paramètre equipe vaut 0, on prend l'équipe domicile sinon c'est l'équipe visiteur
    if(equipe == 0) {
        numeroEquipe = $('#equipeD_match_' + numeroMatch).val();
        if(numeroEquipe == 0) {
            alert('Veuillez choisir une équipe domicile');
            return;
        }
    }
    else {
        numeroEquipe = $('#equipeV_match_' + numeroMatch).val();
        if(numeroEquipe == 0) {
            alert('Veuillez choisir une équipe visiteur');
            return;
        }
    }

    dateDebutMatch = $('#dateDebut_match_' + numeroMatch).val();
    if(dateDebutMatch == '') {
        alert('Veuillez choisir une date');
        return;
    }

    // Appel de la page de saisie des cotes joueurs
    $.ajax( {
                url: 'creer_match_saisie_cotes.php',
                type: 'POST',
				data:	{
							match: numeroMatch,
							numero_equipe: numeroEquipe,
							date_debut_match: dateDebutMatch
						}
            }
    ).done(function(html) {
		if($('.cotesJoueurs').length == 0)
			$('body').append('<div class="cotesJoueurs"></div>');

		$('.cotesJoueurs').empty().append(html);
		$('.cotesJoueurs').dialog({
				autoOpen: true
				,width: 'auto'
				,height: 'auto'
				,modal: true
				,title: 'Saisie des cotes'
				,position: 'center'
				,buttons: {
					'Fermer':  function() {
						$(this).dialog('close');
					}
				}
		});
	});
}

// Gestion de match - Sauvegarde des cotes des buteurs
function creerMatch_sauvegarderCotesJoueurs(numeroMatch, numeroEquipe, numeroJoueur, nomChampCote) {
	var cote = $('#' + nomChampCote).val();

	$.ajax( {
                url: 'creer_match_maj_cotes.php',
                type: 'POST',
				data:	{
							match: numeroMatch,
							numero_equipe: numeroEquipe,
							numero_joueur: numeroJoueur,
							cote: cote
						}
            }
    ).done(function(html) {
	});
}

// Gestion de match - Modification de la colonne cote des buteurs
function creerMatch_modifierColonneCote(colonne) {
	var colonneCote = $(colonne).val();

	if(colonneCote != null) {
		$.ajax( {
					url: 'creer_match_maj_colonne_cote.php',
					type: 'POST',
					data:	{
								colonne_cote: colonneCote
							}
				}
		).done(function(html) {
			afficherMessageInformationBandeau('Colonne de cote mise à jour avec succès', 2000, '');
		});
	}
}

// Gestion de match - Sauvegarde du poste d'un joueur
function creerMatch_sauvegarderPostesJoueurs(numeroJoueur, nomChampPoste) {
	var poste = $('#' + nomChampPoste).val();

	$.ajax( {
                url: 'creer_match_maj_postes.php',
                type: 'POST',
				data:	{
							numero_joueur: numeroJoueur,
							poste: poste
						}
            }
    ).done(function(html) {
	});
}

// Gestion de match - Confirmation de la liste des buteurs d'un match
function creerMatch_confirmerButeurs(numeroMatch, equipe) {
    if(numeroMatch == 0 || numeroMatch == null)
        return;

    var numeroEquipe = 0;
    var dateDebutMatch = null;

    // Si le paramètre equipe vaut 0, alors c'est l'équipe domicile sinon c'est l'équipe visiteur
    if(equipe == 0) {
        numeroEquipe = $('#equipeD_match_' + numeroMatch).val();
        if(numeroEquipe == 0) {
            alert('Veuillez choisir une équipe domicile');
            return;
        }
    }
    else {
        numeroEquipe = $('#equipeV_match_' + numeroMatch).val();
        if(numeroEquipe == 0) {
            alert('Veuillez choisir une équipe visiteur');
            return;
        }
    }

    dateDebutMatch = $('#dateDebut_match_' + numeroMatch).val();
    if(dateDebutMatch == '') {
        alert('Veuillez choisir une date');
        return;
    }

    // Appel de la page de sélection des buteurs
    $.ajax( {
                url: 'creer_match_liste_buteurs.php',
                type: 'POST',
                data:   {
                            match: numeroMatch,
                            equipe: numeroEquipe,
                            date: dateDebutMatch
                        }
            }
    ).done(function(html) {
		if($('.listeButeurs').length == 0)
			$('body').append('<div class="listeButeurs"></div>');

		$('.listeButeurs').empty().append(html);
		var fenetreButeurs = $('.listeButeurs');
		$('.listeButeurs').dialog({
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Sélection des buteurs'
			,position: 'center'
			,buttons: {
				'Valider': function() {
					fenetreButeurs.dialog('close');
					// Sauvegarde des buteurs saisis par l'utilisateur
					var param = 'match=' + numeroMatch;
					param += '&equipe=' + numeroEquipe;

					var i = 0;
					// Dans la "value" du joueur, on a combiné son ID, sa cote pour la sauvegarder dans la table et le but CSC
					// Si la cote n'existe pas, ce qui arrive si le buteur a déjà été ajouté avant, il faut mettre la valeur 0
					var cote;
					$('.buteurs li').each (   function() {
													param += '&joueur' + i++ + '=' + $(this).attr('value');
												}
											);
					param += '&joueurs=' + i;

					$.ajax( {
								url: 'creer_match_maj_buteur.php',
								type: 'POST',
								data: param
							}
					).done(function(html)  {
						if($('.info').length == 0)
							$('body').append('<div class="info"></div>');

						$('.info').empty().append(html);

						$('.listeButeurs').empty();

						// On indique qu'un événement a eu lieu dans le match pour que les modules puissent éventuellement se rafraîchir
						creerMatch_ecrireEvenement(numeroMatch, 4);
					});
				},
				'Annuler': function() {
					fenetreButeurs.dialog('close');
					$('.listeButeurs').empty();
				}
			}
		});

		$('.listeButeurs').dialog('open');

		$('.buteurs').on('click', 'li', function() {  $(this).remove();   });

		$('.participants').on  ('click', 'li', function() {
												var numeroJoueurPur = $(this).val();
												var numeroJoueur = $(this).val();
												var nomJoueur = $(this).text();
												var classe = $(this).attr('class');

												// Il faut demander la cote d'un buteur si on ne la connaît pas encore
												// Mais dans tous les cas, à chaque ajout d'un buteur, il est nécessaire de savoir s'il s'agit d'un but CSC ou un but normal
												var demanderCote = 1;
												if($('.buteurs').html().indexOf(numeroJoueur + '-0') >= 0)
													demanderCote = 0;
												$.ajax( {
															url: 'creer_match_informations_buteurs.php',
															type: 'POST',
															data:   {   joueur: numeroJoueurPur,
																		match: numeroMatch,
																		equipe: numeroEquipe,
																		demander_cote: demanderCote
																	}
														}
												).done(function(html) {
													if($('.informationsButeur').length == 0)
														$('body').append('<div class="informationsButeur"></div>');

													$('.informationsButeur').empty().append(html);
													$('.informationsButeur').dialog({
														autoOpen: false
														,width: 'auto'
														,height: 'auto'
														,modal: true
														,title: 'Cote de ' + nomJoueur
														,position: 'center'
														,buttons:   {
																		'Valider':  function() {
																						// On met la valeur de la cote en "value" dans la liste s'il ne s'agit pas d'un but CSC
																						// Si c'est le cas, la cote est égale à 0
																						var csc = 0;
																						var cote = 0;

																						if($('input[name=inputCSC]').prop('checked'))
																							csc = 1;
																						if(csc == 0)
																							cote = $('#inputCote').val();
																						if(cote == null)
																							cote = 0;
																						if(csc == 0)
																							$('.buteurs').append($('<li>', { value: numeroJoueur + '-' + csc + '.'+ cote, text: nomJoueur, class: classe }));
																						else
																							$('.buteurs').append($('<li>', { value: numeroJoueur + '-' + csc + '.'+ cote, text: nomJoueur + ' (CSC)', class: classe }));
																						$(this).dialog('close');
																					},
																		'Annuler': function() { $(this).dialog('close'); }
																	}

													});

													$('.informationsButeur').dialog('open');
												});

											}
							);
	});
}

// Gestion de match - Affichage de la page de trophées pour les pronostiqueurs à leur prochaine connexion
function creerMatch_afficherTrophees(championnat) {
	if(!confirm('Etes-vous sûr de bien vouloir faire afficher la page de trophées ?'))
		return;

	$.ajax( {
				url: 'creer_match_affichage_trophees.php',
				type: 'POST',
				data:   {
							championnat: championnat
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Affichage automatique de la page de trophées à la prochaine connexion', 2000, '');
	});
}

// Gestion de match - Génération du compte-rendu
function creerMatch_genererCR(journee) {
	$.ajax( {
				url: 'creer_match_generation_cr.php',
				type: 'POST',
				data:   {
							journee: journee
						}
			}
	).done(function(html) {
		alert(html);
	});
}

// Gestion de match - Détermination des liens des pages pour les matches de la journée
function creerMatch_lireLiensMatches(journee) {
	$.ajax(	{
				url: 'creer_match_lecture_liens_matches.php',
				type: 'POST',
				data:	{
							journee: journee
						}
			}
	).done(function(html) {
		//console.log(html.replace(/<br\s*\/?>/mg,"\n"));

		// Rechargement de la page
		location.reload();
	});
}

// Gestion de match - Détermination des liens des pages pour les matches de la journée sur le site ScoresPro
function creerMatch_lireLiensMatchesScoresPro(journee) {
	$.ajax(	{
				url: 'creer_match_lecture_liens_matches_scorespro.php',
				type: 'POST',
				data:	{
							journee: journee
						}
			}
	).done(function(html) {
		//console.log(html.replace(/<br\s*\/?>/mg,"\n"));

		// Rechargement de la page
		location.reload();
	});
}

// Gestion de match - Lecture des effectifs des deux équipes d'un match
// Le paramètre origine indique s'il s'agit de l'effectif lu sur Match en Direct ou sur ScoresPro
function creerMatch_lireEffectif(match, champLienPage, origine) {
	if($('#' + champLienPage).val() == '') {
		alert('Veuillez saisir un lien de page pour le match');
		return;
	}

	var page = '';
	if(origine == 1)
		page = 'creer_match_lecture_effectif_equipes.php';
	else
		page = 'creer_match_lecture_effectif_equipes_scorespro.php';
	$.ajax(	{
				url: page,
				type: 'POST',
				data: {
					match: match
				},
				dataType: 'json'
			}
	).done(function(html) {
		if(html.joueurs && html.joueurs.length > 0) {
			// Affichage d'une fenêtre de mise à jour des données des joueurs pour lesquels la recherche a été infructueuse
			$.ajax(	{
						url: 'creer_match_correction_effectif.php',
						type: 'POST',
						data:	{	match: match,
									joueurs: html.joueurs,
									origine: origine
								}
			}).done(function(html) {
				if($('.listeJoueurs').length == 0)
					$('body').append('<div class="listeJoueurs"></div>');

				$('.listeJoueurs').empty().append(html);
				$('.listeJoueurs').dialog({
						autoOpen: true
						,width: 'auto'
						,height: 'auto'
						,modal: true
						,title: 'Correction des joueurs non trouvés'
						,position: 'center'
						,buttons: {
							'Fermer':  function() {
								$(this).dialog('close');
							}
						}
				});
			});
		}
		else
			afficherMessageInformationBandeau('Vérification des effectifs effectuée avec succès', 2000, '');
	}).fail(function(html) {
		console.log('Fonction creerMatch_lireEffectif : dans le fail', html);
	});
}

// Gestion de match - Lecture de la composition des deux équipes d'un match
// Le paramètre origine indique s'il s'agit de l'effectif lu sur Match en Direct ou sur ScoresPro
function creerMatch_lireComposition(match, champLienPage, origine) {
	var page = '';
	if(origine == 1)
		page = 'creer_match_lecture_composition_equipes.php';
	else
		page = 'creer_match_lecture_composition_equipes_scorespro.php';

	$.ajax(	{
				url: page,
				type: 'POST',
				data:	{
							match: match
						},
				dataType: 'json'
			}
	).done(function(html) {
		if(html.length > 0) {
			// Affichage d'une fenêtre de mise à jour des données des joueurs pour lesquels la recherche a été infructueuse
			$.ajax(	{
						url: 'creer_match_correction_effectif.php',
						type: 'POST',
						data:	{	match: match,
									joueurs: html,
									origine: origine
								}
			}).done(function(html) {
				if($('.listeJoueurs').length == 0)
					$('body').append('<div class="listeJoueurs"></div>');

				$('.listeJoueurs').empty().append(html);
				$('.listeJoueurs').dialog({
						autoOpen: true
						,width: 'auto'
						,height: 'auto'
						,modal: true
						,title: 'Correction des joueurs non trouvés'
						,position: 'center'
						,buttons: {
							'Fermer':  function() {
								$(this).dialog('close');
							}
						}
				});
			});
		}
		else
			afficherMessageInformationBandeau('Composition remplie avec succès', 2000, '');
	}).fail(function(html) {
		console.log('Fonction creerMatch_lireComposition : dans le fail');
	});
}

// Gestion de match - Lecture des informations d'un joueur pour l'afficher dans la fenêtre de correction de l'effectif
function creerMatch_lireJoueur(identifiant, champPrenom, champNom, champCorrespondance, origine) {
	// Lecture des informations du joueur pour mise à jour des champs
	var joueur = $('#selectListeJoueurs_' + identifiant).val();

	if(joueur != null && joueur != 0) {
		$.ajax(	{
					url: 'creer_match_lecture_joueur.php',
					type: 'POST',
					data:	{
								joueur: joueur,
								origine: origine
							},
					dataType: 'json'

		}).done(function(html) {
			if(html.erreur == '0') {
				$('#' + champPrenom).val(html.prenom);
				$('#' + champNom).val(html.nom);
				$('#' + champCorrespondance).val(html.correspondance);
			}
		}).fail(function(html) {
			console.log('Fonction creerMatch_lireJoueur : dans le fail');
		});
	}
	else {
		$('#' + champPrenom).val('');
		$('#' + champNom).val('');
		$('#' + champCorrespondance).val('');
	}
}

// Gestion de match - Modification du prénom du joueur dans la fenêtre de correction de l'effectif
function creerMatch_modifierPrenomJoueur(elt, identifiant) {
	var joueur = $('#selectListeJoueurs_' + identifiant).val();
	var prenom = elt.value;

	$.ajax(	{
				url: 'creer_match_modification_prenom_joueur.php',
				type: 'POST',
				data:	{
							joueur: joueur,
							prenom: encodeURIComponent(prenom)
						}
	}).done(function(html) {
	});
}

// Gestion de match - Modification du nom du joueur dans la fenêtre de correction de l'effectif
function creerMatch_modifierNomJoueur(elt, identifiant) {
	var joueur = $('#selectListeJoueurs_' + identifiant).val();
	var nom = elt.value;

	$.ajax(	{
				url: 'creer_match_modification_nom_joueur.php',
				type: 'POST',
				data:	{
							joueur: joueur,
							nom: encodeURIComponent(nom)
						}
	}).done(function(html) {
	});
}

// Gestion de match - Copie du nom lu vers le nom de correspondance
// Le paramètre origine indique s'il s'agit du nom de correspondance NomCorrespondance, NomCorrespondanceComplementaire ou NomCorrespondanceCote
function creerMatch_copierNomCorrespondance(nomCorrespondance, identifiant, champCorrespondance, origine) {
	var joueur = $('#selectListeJoueurs_' + identifiant).val();

	if(joueur != 0) {
		$.ajax(	{
					url: 'creer_match_copie_nom_correspondance_joueur.php',
					type: 'POST',
					data:	{
								joueur: joueur,
								nom_correspondance: nomCorrespondance,
								origine: origine
							}
		}).done(function(html) {
			$('#' + champCorrespondance).val(decodeURIComponent(nomCorrespondance));
		});
	}
	else
		alert('Veuillez choisir un joueur');
}

// Gestion de match - Suppression du nom lu vers le nom de correspondance
// Le paramètre origine indique s'il s'agit du nom de correspondance NomCorrespondance, NomCorrespondanceComplementaire ou NomCorrespondanceCote
function creerMatch_supprimerNomCorrespondance(identifiant, champCorrespondance, origine) {
	var joueur = $('#selectListeJoueurs_' + identifiant).val();

	if(joueur != 0) {
		$.ajax(	{
					url: 'creer_match_suppression_nom_correspondance_joueur.php',
					type: 'POST',
					data:	{
								joueur: joueur,
								origine: origine
							}
		}).done(function(html) {
			$('#' + champCorrespondance).val('');
		});
	}
	else
		alert('Veuillez choisir un joueur');
}

// Gestion de match - Création d'un joueur unique
function creerMatch_creerJoueur(champPrenom, champNom, champNomCorrespondance, champPoste, champDateDebut, equipe) {
	var nomFamille = encodeURIComponent($('#' + champNom).val());
	var prenom = encodeURIComponent($('#' + champPrenom).val());
	var nomCorrespondance = encodeURIComponent($('#' + champNomCorrespondance).val());
	var poste = $('#' + champPoste).val();
	var dateDebutPresence = $('#' + champDateDebut).val();

	if(nomFamille == null || poste == null || dateDebutPresence == null) {
		alert('Champs non remplis correctement');
		return;
	}

	$.ajax(	{
				url: 'gerer_effectif_creation_joueur_avec_correspondance.php',
				type: 'POST',
				data:	{
							nom_famille: nomFamille,
							prenom: prenom,
							nom_correspondance: nomCorrespondance,
							poste: poste,
							date_debut_presence: dateDebutPresence,
							equipe: equipe
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Joueur créé avec succès', 1000, '');
	});
}

// Gestion de match - Recherche des informations d'un joueur sur Google
function creerMatch_rechercherJoueur(joueur, equipe) {
	$.ajax(	{
				url: 'creer_match_recherche_joueur.php',
				type: 'POST',
				data:	{
							joueur: joueur,
							equipe: equipe
						}
	}).done(function(html) {
		if($('.rechercheJoueur').length == 0)
			$('body').append('<div class="rechercheJoueur"></div>');

		$('.rechercheJoueur').empty().append(html);
		$('.rechercheJoueur').dialog({
			autoOpen: true
			,width: 'auto'
			,height: 'auto'
			,modal: false
			,title: 'Recherche du joueur ' + decodeURI(joueur)
			,position: 'center'
			,buttons: {
				'Fermer':  function() {	$(this).dialog('close');	}
			}

		});

	});
}

// Gestion de match - Passage d'un match en mode direct
function creerMatch_passerEnDirect(match) {
	$.ajax(	{
				url: 'creer_match_ajout_direct.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Match passé en direct', 2000, '');
	});
}

// Gestion de match - Suppression d'un match du direct
function creerMatch_supprimerDuDirect(match) {
	$.ajax(	{
				url: 'creer_match_suppression_direct.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Match supprimé du direct', 2000, '');
	});
}

// Gestion de match - Réinitialisation du match
function creerMatch_reinitialiserMatch(match) {
	var confirmation = confirm('Etes-vous sûr de bien vouloir réinitialiser le match (et les points gagnés par les pronostiqueurs) ?');
	if(confirmation == false)
		return;

	$.ajax(	{
				url: 'creer_match_reinitialisation_match.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		location.reload();
	}).fail(function(html) {
		console.log('Fonction creerMatch_reinitialiserMatch : dans le fail');
	});
}

// Gestion de match - Génération du CR
function creerMatch_genererCR() {
	var journee = $('#selectJournee').val();

	if(journee == 0)
			return;

	$.ajax(	{
	  url: 'creer_match_generation_cr.php',
	  type: 'POST',
	  data:   {
	              journee: journee
	          }
	}).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info" style="z-index: 20000;"></div>');

		$('.info').html(html);
		$('.info').dialog({
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Compte-rendu'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
								$(this).dialog('close');
							}
			}

		});

		$('.info').dialog('open');
	});
}

// Création d'un pronostic - Sauvegarde d'un pronostic
function creerProno_sauvegarderPronostic(el, type, numeroMatch, equipe, numeroMatchLie) {
    if(el == null || type == null || numeroMatch == 0 || numeroMatch == null)
        return;

    /*
        Voici les actions à entreprendre :
        - après une modification (score aller, score retour, score prolongation, sélection d'un vainqueur), on effectue la MAJ en BDD
        - si le score modifié est l'aller ou le retour, on regarde s'il faut afficher ou non le score de la prolongation (et du coup les TAB)
        - si le score de la prolongation a été modifié, on regarde s'il faut demander le vainqueur ou pas (cas des TAB)
    */

    var score = 0;
    var vainqueur = 0;

    if(type == 'score' || type == 'scoreAP')
        score = el.value
    else if(type == 'vainqueur')
        vainqueur = el.value;

    $.ajax( {
                url: 'creer_prono_maj_prono.php',
                type: 'POST',
                data:   {
                            match: numeroMatch,
                            type: type,
                            equipe: equipe,
                            score: score,
                            vainqueur: vainqueur
                        }
            }
    ).done(function(html) {

                // La réponse de la page indique s'il y a prolongation ou non et s'il y a TAB ou non
                // Si le score aller ou retour a été modifié, on regarde s'il faut afficher la zone de scores AP
                // La page d'enregistrement a vérifié qu'il était encore possible d'effectuer des modifications
                // Cela empêche qu'un utilisateur laisse sa page Internet active toute la journée avant de valider son pronostic
                if(html.indexOf('DEPASSE') != -1) {
					if($('.info').length == 0)
						$('body').append('<div class="info" style="z-index: 20000;"></div>');

                    $('.info').html('<label>Désolé, il n\'est plus possible d\'effectuer de pronostic sur ce match</label>');
                    $('.info').dialog({
                        autoOpen: false
                        ,width: 'auto'
                        ,height: 'auto'
                        ,modal: true
                        ,title: 'Heure de pronostic dépassée'
                        ,position: 'center'
                        ,buttons: {
                            'Fermer':   function() {
                                            $(this).dialog('close');
                                        }
                        }

                    });

                    $('.info').dialog('open');
                    return;
                }

                if(numeroMatchLie == 0)
                    return;

                if(type == 'score') {
                    if(html.indexOf("PROLONGATION") != -1) {
                        // La page de mise à jour a détecté qu'il fallait afficher les scores AP
                        // On copie donc le score de la 90ème dans le score AP (en supprimant les scores inférieurs)
                        var minScoreEquipeDomicile = $('#selectButsD_match_' + numeroMatchLie).val();
                        var minScoreEquipeVisiteur = $('#selectButsV_match_' + numeroMatchLie).val();
                        var i;
                        $('#selectButsAPD_match_' + numeroMatchLie).empty();
                        $('#selectButsAPV_match_' + numeroMatchLie).empty();
                        for(i = minScoreEquipeDomicile; i <= 15; i++) {
                            $('#selectButsAPD_match_' + numeroMatchLie).append($('<option>', { value: i, text: i }));
                        }
                        for(i = minScoreEquipeVisiteur; i <= 15; i++) {
                            $('#selectButsAPV_match_' + numeroMatchLie).append($('<option>', { value: i, text: i }));
                        }

                        $('#selectButsAPD_match_' + numeroMatchLie + ' option:first').attr('selected', 'selected');
                        $('#selectButsAPV_match_' + numeroMatchLie + ' option:first').attr('selected', 'selected');
                        $('#spanProlongationD_match_' + numeroMatchLie).css({'visibility': 'visible'});
                        $('#spanProlongationV_match_' + numeroMatchLie).css({'visibility': 'visible'});
                    }
                    else {
                        // Le fait de ne pas afficher les scores AP ne signifie pas forcément qu'il faille les effacer
                        // En effet, dans les types de matches 1, 2 et 5 (respectivement match de ligue 1, match aller de LDC et match de Comunnity Shield)
                        // les scores AP n'apparaissent jamais
                        if($('#spanProlongationD_match_' + numeroMatchLie).length != 0)
                            $('#spanProlongationD_match_' + numeroMatchLie).css({'visibility': 'hidden'});

                        if($('#spanProlongationV_match_' + numeroMatchLie).length != 0)
                            $('#spanProlongationV_match_' + numeroMatchLie).css({'visibility': 'hidden'});
                    }

                    // TAB ?
                    if(html.indexOf("TAB") != -1)
                        $('#spanVainqueur_match_' + numeroMatchLie).css({'visibility': 'visible'});
                    else {
                        // Le fait de ne pas afficher les TAB ne signifie pas forcément qu'il faille les effacer
                        // En effet, dans les types de matches 1 et 2 (respectivement match de ligue 1 et match aller de LDC)
                        // les TAB n'apparaissent jamais
                        if($('#spanVainqueur_match_' + numeroMatchLie).length != 0)
                            $('#spanVainqueur_match_' + numeroMatchLie).css({'visibility': 'hidden'});
                    }
                }
                else if(type == 'scoreAP') {
                    // TAB ?
                    if(html.indexOf("TAB") != -1)
                        $('#spanVainqueur_match_' + numeroMatchLie).css({'visibility': 'visible'});
                    else {
                        // Le fait de ne pas afficher les TAB ne signifie pas forcément qu'il faille les effacer
                        // En effet, dans les types de matches 1 et 2 (respectivement match de ligue 1 et match aller de LDC)
                        // les TAB n'apparaissent jamais
                        if($('#spanVainqueur_match_' + numeroMatchLie).length != 0)
                            $('#spanVainqueur_match_' + numeroMatchLie).css({'visibility': 'hidden'});
                    }
                }
            }
    );
}

// Création d'un pronostic - Pronostic des buteurs d'un match
function creerProno_pronostiquerButeurs(el, type, numeroMatch, numeroEquipe) {
    // Appel de la page de sélection des buteurs
    if(el == null || numeroEquipe == null || numeroEquipe == 0 || type == null)
        return;

    var param = 'match=' + numeroMatch;
    param += '&equipe=' + numeroEquipe;
    param += '&type=' + type;

    $.ajax( {
                url: 'creer_prono_liste_buteurs.php',
                type: 'POST',
                data: param
            }
    ).done(function(html) {
		if($('.listeButeurs').length == 0)
			$('body').append('<div class="listeButeurs"></div>');

		$('.listeButeurs').empty().append(html);
		var fenetreButeurs = $('.listeButeurs');
		$('.listeButeurs').dialog({
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Sélection des buteurs'
			,position: 'center'
			,buttons: {
				'Valider': function() {
					fenetreButeurs.dialog('close');

					// Sauvegarde des buteurs saisis par l'utilisateur
					var i = 0;
					// Dans la "value" du joueur, on a combiné son ID et sa cote pour la sauvegarder dans la table
					$('.pronostics-buteurs li.buteur').each    (   function() {
															param += '&joueur' + i++ + '=' + $(this).val();
														}
													);
					param += '&joueurs=' + i;
					$.ajax( {
								url: 'creer_prono_maj_buteur.php',
								type: 'POST',
								data: param,
								dataType: 'json'
							}
					).done(function(html) {
						// Mise à jour de la zone d'affichage des buteurs
						if(type == 'D') {
							if(html.buteurs != '')
								$('#labelButeursD_match_' + numeroMatch).html(html.buteurs);
							else
								$('#labelButeursD_match_' + numeroMatch).html('Aucun');
						}
						else if(type == 'V') {
							if(html.buteurs != '')
								$('#labelButeursV_match_' + numeroMatch).html(html.buteurs);
							else
								$('#labelButeursV_match_' + numeroMatch).html('Aucun');
						}
					}).fail(function(html) { console.log('Fonction creerProno_pronostiquerButeurs : dans le fail'); });
				},
				'Annuler': function() {
					fenetreButeurs.dialog('close');
				}
			}
		});

		$('.listeButeurs').dialog('open');

		$('.pronostics-buteurs').on    ('click', 'li.buteur', function() {    $(this).remove();   });

		$('.effectif').on   ('click', 'li.buteur', function() {
														var numeroJoueur = $(this).val();
														var nomJoueur = $(this).text();
														var classe = $(this).attr('class');
														$('.pronostics-buteurs').append($('<li>', { value: numeroJoueur, text: nomJoueur, class: classe }));
											}
							);
	});
}

// Création d'un pronostic - Affichage des derniers résultats des équipes
function creerProno_afficherDerniersResultats(match) {
	$.ajax(	{
				url: 'creer_prono_affichage_resultats.php',
				type: 'POST',
				data:	{	match: match	}
			}
	).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreDetails = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,maxWidth: 900
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Statistiques'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreDetails.dialog('close');
						}
			}

		});
		fenetreDetails.dialog('open');
	});
}

// Création d'un pronostic - Sélection du match Canal
function creerProno_selectionnerMatchCanal(numeroJournee, numeroMatch) {
	$.ajax(	{
		url: 'creer_prono_selection_match_canal.php',
		type: 'POST',
		data:	{	journee: numeroJournee, match: numeroMatch	}
	}
	).done(function(html) {
		if(html.indexOf('DEPASSE') != -1) {
			if($('.info').length == 0)
				$('body').append('<div class="info" style="z-index: 20000;"></div>');

			$('.info').html('<label>Désolé, il n\'est plus possible de sélectionner ce match en match Canal</label>');
			$('.info').dialog({
				autoOpen: false
				,width: 'auto'
				,height: 'auto'
				,modal: true
				,title: 'Heure de pronostic dépassée'
				,position: 'center'
				,buttons: {
					'Fermer':   function() {
									$(this).dialog('close');
								}
				}

			});

			$('.info').dialog('open');
			return;
		}
	});
}

// Affichage de résultats - Changement de journée
function consulterResultats_changerJournee() {
    var journee = $('#selectJournee').val();

    if(journee == 0)
        return;

    $.ajax( {
                url: 'consulter_resultats_resultats_pronostics.php',
                type: 'POST',
                data: { journee: journee }
            }
    ).done(function(html) {
                $('#divResultatsPronostics').html(html);
            }
    );

}

// Affichage de résultats - Détail d'un match
function consulterResultats_afficherMatch(match, equipeDomicile, equipeVisiteur, modeRival, modeConcurrentDirect) {
    $.ajax(
            {
                url: 'consulter_resultats_details_match.php',
                type: 'POST',
                data:   {   match: match,
                            modeRival: modeRival,
							modeConcurrentDirect: modeConcurrentDirect
                        }
            }
    ).done(function(html) {
		if($('.detailMatch').length == 0)
			$('body').append('<div class="detailMatch"></div>');

		$('.detailMatch').empty().append(html);

		var titre = '';
		if(modeConcurrentDirect == 1)				titre = 'Détail du match ' + equipeDomicile + ' - ' + equipeVisiteur + ' (classement journée)';
		else										titre = 'Détail du match ' + equipeDomicile + ' - ' + equipeVisiteur;

		var fenetreDetails = $('.detailMatch');
		$('.detailMatch').dialog( {
			autoOpen: false
			,maxWidth: 900
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: titre
			,position: 'center'
			,buttons: {
				'Fermer': function() {
                    fenetreDetails.dialog('close');
				}
			}
		});
		fenetreDetails.dialog('open');
	});
}

// Affichage de résultats - Buteurs d'un match pour une équipe
function afficherButeurs(match, equipe) {
	$.ajax(	{
				url: 'consulter_buteurs.php',
				type: 'POST',
				data:	{
							match: match,
							equipe: equipe
						}
			}
	).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreButeurs = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,maxWidth: 900
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Buteurs de l\'équipe'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreButeurs.dialog('close');
						}
			}

		});
		fenetreButeurs.dialog('open');
	});
}

// Affichage de résultats - Détail d'un pronostiqueur pour une journée
function consulterResultats_afficherPronostiqueur(pronostiqueur, pronostiqueurNom, journee) {
    $.ajax(
            {
                url: 'consulter_resultats_details_pronostiqueur.php',
                type: 'POST',
                data:   {   pronostiqueurDetail: pronostiqueur,
                            journee: journee
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreDetails = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,maxWidth: 900
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Détail du pronostiqueur ' + pronostiqueurNom
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreDetails.dialog('close');
						}
			}

		});
		fenetreDetails.dialog('open');
	});
}

// Calcul de résultats - Changement de championnat
function calculerResultats_changerChampionnat() {
    // Simple effacement de la zone d'information
	if($('.info').length == 0)
		$('body').append('<div class="info"></div>');

	$('.info').empty();
}

// Calcul de résultats - Calcul des résultats
function calculerResultats_calculerResultats() {
    var journee = $('#selectJournee').val();

	$.ajax(	{
				url: 'calculer_resultats_calcul_resultats.php',
				type: 'POST',
				data:   {   journee: journee
						}
	}).done(function(html) {
		afficherMessageInformationBandeau('Calculs effectués avec succès pour la journée ' + journee, 2000);
	});
}

// Calcul de résultats - Finalisation des confrontations d'une journée de Coupe de France
function calculerResultats_finaliserConfrontations() {
	if(confirm('Etes-vous sûr de vouloir finaliser la journée ?') == false)
		return;

    var journee = $('#selectJournee').val();

    $.ajax(
            {
                url: 'calculer_resultats_finalisation_confrontations.php',
                type: 'POST',
                data:   {   journee: journee
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreInfo = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Finalisation des confrontations'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreInfo.dialog('close');
						}
			}

		});
		fenetreInfo.dialog('open');
	});
}

// Gestion d'effectif - Affichage de l'effectif de l'équipe
function gererEffectif_afficherEffectif() {
    // Lecture des différents joueurs de l'équipe sélectionnée
    var equipe = $('#selectEquipes').val();
    if(equipe != -1) {
        $.ajax( {
                url: 'gerer_effectif_affichage_effectif.php',
                type: 'POST',
                data: { equipe: equipe }
            }
        ).done(function(html) {
                    $('#divEffectif').html(html);
                }
        );
    }
    else
        $('#divEffectif').html('');
}

// Gestion d'effectif - Transfert d'un joueur de l'équipe
function gererEffectif_transfererJoueur(action, joueur, nomJoueur) {
    // Le paramètre action permet de savoir s'il s'agit d'un transfert (0) ou d'une création (1)
    var titre = action == 0 ? ('Transfert du joueur ' + nomJoueur) : ('Création du joueur ' + nomJoueur);
	var prenomJoueur = '';

	// Dans le cas de la création d'un joueur, il est nécessaire de demander son prénom
	if(action == 1) {
		prenomJoueur = window.prompt('Veuillez saisir le prénom du joueur ' + nomJoueur, '');
		if(prenomJoueur == null)
			return;
	}

    $.ajax( {
                url: 'gerer_effectif_transfert_joueur.php',
                type: 'POST',
                data: { joueur: joueur }
            }
    ).done(function(html) {
                $('#divTransfertJoueur').html(html);

                var fenetreTransfert = $('#divTransfertJoueur');
                $('#divTransfertJoueur').dialog( {
                    autoOpen: false
                    ,width: 'auto'
                    ,height: 'auto'
                    ,modal: true
                    ,title: titre
                    ,position: 'center'
                    ,buttons: {
                        'Valider': function() {
                            fenetreTransfert.dialog('close');

                            // Lecture de l'équipe où le joueur a été déplacé
                            var nouvelleEquipe = $('#selectEquipesTransfert').val();

                            // Lecture de la date effective du déplacement
                            var dateTransfert = $('#dateDebutTransfert').val();

                            $.ajax( {
                                        url: 'gerer_effectif_maj_transfert.php',
                                        type: 'POST',
                                        data:   {
                                                    joueur: joueur,
                                                    nomJoueur: nomJoueur,
													prenomJoueur: prenomJoueur,
                                                    equipe: nouvelleEquipe,
                                                    dateTransfert: dateTransfert,
                                                    action: action
                                                }
                                    }
                            ).done(function(html) {
								if($('.info').length == 0)
									$('body').append('<div class="info"></div>');

								$('.info').empty().append(html);
								gererEffectif_afficherEffectif();
							});


                        },
                        'Annuler':  function() {
                                    fenetreTransfert.dialog('close');
                                }
                    }

                });
                fenetreTransfert.dialog('open');
            }


    );
}

// Gestion d'effectif - Suppression d'un joueur
function gererEffectif_supprimerJoueur(joueur) {
    // Dans un premier temps, on vérifie que ce joueur n'apparaît dans aucun match de la saison, ni dans les buteurs
    // Si c'est le cas, on l'indique et on indique qu'il n'est pas possible d'effacer le joueur
    $.ajax( {
                url: 'gerer_effectif_verifier_joueur.php',
                type: 'POST',
                data:	{
							joueur: joueur
						},
                dataType: 'json'
            }
    ).done(function(html) {
        if(html.joueurAParticipeOuMarque) {
            alert('Ce joueur a déjà participé ou marqué. Suppression non effectuée');
            return;
        }
    });

    if(!confirm('Etes-vous sûr de vouloir supprimer ce joueur ?'))
        return;

    // Arrivé à ce stade, on peut supprimer le joueur
    $.ajax( {
                url: 'gerer_effectif_supprimer_joueur.php',
                type: 'POST',
                data:	{
							joueur: joueur
						}
            }
    ).done(function(html) {
        afficherMessageInformationBandeau('Joueur supprimé avec succès', 1000, '');
        gererEffectif_afficherEffectif();
    });
}

// Gestion d'effectif - Recherche d'un joueur
function gererEffectif_rechercherJoueur(critereRecherche, el, modeRechercheSimple) {
    // Recherche du joueur à partir de son nom
    // On va éventuellement remplacer les caractères génériques
    $.ajax( {
                url: 'gerer_effectif_recherche_joueur.php',
                type: 'POST',
                data:	{
							critereRecherche: critereRecherche,
							modeRechercheSimple: modeRechercheSimple
						}
            }
    ).done(function(html) {
        $(el).html(html);
    });
}

// Gestion d'effectif - Créer un joueur
function gererEffectif_creerJoueur(nomFamille, prenom, postes, dateDebutPresence, equipes) {
	$.ajax(	{
				url: 'gerer_effectif_creation_joueur.php',
				type: 'POST',
				data:	{
							nomFamille: $('#' + nomFamille).val(),
							prenom: $('#' + prenom).val(),
							poste: $('#' + postes).val(),
							dateDebutPresence: $('#' + dateDebutPresence).val(),
							equipe: $('#' + equipes).val()
						}
			}
	).done(function(html) {
		// Se placer sur la zone de saisie du nom du joueur
		$('#' + nomFamille).focus();

		// Effacement des zones du nom de famille et du prénom
		$('#' + nomFamille).val('');
		$('#' + prenom).val('');

		afficherMessageInformationBandeau('Joueur créé avec succès', 1000, '');

	});
}

// Gestion d'effectif - Modification d'une information d'un joueur
function gererEffectif_modifierJoueur(elt, joueur, champ) {
    // Le paramètre champ indique quelle est l'information à modifier
    var valeur = $(elt).val();

	$.ajax(	{
				url: 'gerer_effectif_modification_joueur.php',
				type: 'POST',
				data:	{
							joueur: joueur
                            ,valeur: valeur
                            ,champ: champ
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Modification effectuée avec succès', 1000, '');
	});
}


// Création des bonus - Recherche d'un joueur
function creerBonus_rechercherJoueur(critereRecherche, nomDiv, nomZoneRecherche, nomZoneId, nomZoneNomJoueur) {
    // Recherche du joueur à partir de son nom
    // On va éventuellement remplacer les caractères génériques
    $.ajax( {
                url: 'creer_bonus_recherche_joueur.php',
                type: 'POST',
                data:   {   critereRecherche: critereRecherche,
                            nomZoneRecherche: nomZoneRecherche,
                            nomZoneId: nomZoneId,
                            nomZoneNomJoueur: nomZoneNomJoueur
                        }
            }
    ).done(function(html) {
                $(nomDiv).html(html);
            }
    );
}

// Création des bonus - Sélection d'un joueur
function creerBonus_selectionnerJoueur(joueur, nomJoueur, nomZoneRecherche, nomZoneId, nomZoneNomJoueur) {
    $(nomZoneRecherche).val(nomJoueur);
    $(nomZoneId).val(joueur);
    $(nomZoneNomJoueur).html(nomJoueur);
}

// Création des bonus - Validation d'un bonus
function creerBonus_validerBonus() {
    // Lecture des champs saisis par l'utilisateur
    var equipeChampionne = $('#selectEquipesChampionnes').val();
    var equipeLDC1 =  $('#selectEquipesLDC1').val();
    var equipeLDC2 =  $('#selectEquipesLDC2').val();
    var equipeLDC3 =  $('#selectEquipesLDC3').val();
    var equipeReleguee1 =  $('#selectEquipesReleguees1').val();
    var equipeReleguee2 =  $('#selectEquipesReleguees2').val();
    var equipeReleguee3 =  $('#selectEquipesReleguees3').val();
    var meilleurButeur = $('#id-meilleur-buteur').val();
    var meilleurPasseur = $('#id-meilleur-passeur').val();

    if(equipeChampionne == -1 || equipeLDC1 == -1 || equipeLDC2 == -1 || equipeLDC3 == -1 || equipeReleguee1 == -1 || equipeReleguee2 == -1 || equipeReleguee3 == -1 || meilleurButeur == null || meilleurPasseur == null) {
        alert('Veuillez saisir toutes les informations avant de valider vos bonus');
        return;
    }

    $.ajax( {
                url: 'creer_bonus_maj_bonus.php',
                type: 'POST',
                data:   {   equipeChampionne: equipeChampionne,
                            equipeLDC1: equipeLDC1,
                            equipeLDC2: equipeLDC2,
                            equipeLDC3: equipeLDC3,
                            equipeReleguee1: equipeReleguee1,
                            equipeReleguee2: equipeReleguee2,
                            equipeReleguee3: equipeReleguee3,
                            meilleurButeur: meilleurButeur,
                            meilleurPasseur: meilleurPasseur
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreValiderBonus = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Création des pronostics de bonus'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreValiderBonus.dialog('close');
						}
			}

		});
		fenetreValiderBonus.dialog('open');

	});
}

// Gestion des bonus - Validation d'un bonus
function gererBonus_validerBonus() {
    // Lecture des champs saisis par l'utilisateur
    var equipeChampionnePoints = $('#pointsEquipeChampionne').val();
    var equipeLDCPoints =  $('#pointsEquipeLDC').val();
    var equipeRelegueePoints =  $('#pointsEquipeReleguee').val();
    var meilleurButeurPoints = $('#pointsMeilleurButeur').val();
    var meilleurPasseurPoints = $('#pointsMeilleurPasseur').val();

    $.ajax( {
                url: 'gerer_bonus_maj_bonus.php',
                type: 'POST',
                data:   {   equipeChampionnePoints: equipeChampionnePoints,
                            equipeLDCPoints: equipeLDCPoints,
                            equipeRelegueePoints: equipeRelegueePoints,
                            meilleurButeurPoints: meilleurButeurPoints,
                            meilleurPasseurPoints: meilleurPasseurPoints
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreValiderBonus = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Gestion des pronostics de bonus'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreValiderBonus.dialog('close');
						}
			}

		});
		fenetreValiderBonus.dialog('open');

	});
}

// Gestion des bonus - Calcul des points bonus
function gererBonus_calculerBonus() {
    $.ajax( {
                url: 'gerer_bonus_calcul_bonus.php',
                type: 'POST'
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreCalculerBonus = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Calcul des points bonus'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreCalculerBonus.dialog('close');
						}
			}

		});
		fenetreCalculerBonus.dialog('open');

	});
}

// Création des qualifications - Validation des équipes qualifiées
function creerQualification_validerQualifiees(nomGroupe, nombreGroupes, numeroPremierGroupe, nombreEquipes) {
    // Le nom final du groupe est la contraction de la racine (passée dans le paramètre nomGroupe, et le nombre de groupes
    var grp = nomGroupe;
    var i;
    var param = 'groupes=' + nombreGroupes + '&equipes=' + nombreEquipes + '&numeroPremierGroupe=' + numeroPremierGroupe;
    for(i = 0; i < nombreGroupes; i++) {
        var obj = $('#ulGroupe' + i + ' > li');
        obj.each(   function(j) {
                        param += '&groupe' + i + 'equipe' + j + '=' + $(this).attr('data-val');
                    }
        );
    }
    $.ajax( {
                url: 'creer_qualification_maj_qualifications.php',
                type: 'POST',
                data: param
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreValiderQualifees = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Création des qualifications'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreValiderQualifees.dialog('close');
						}
			}

		});
		fenetreValiderQualifees.dialog('open');
	});
}

// Gestion des qualifications - Validation des équipes qualifiées pour toutes les poules
function gererQualification_validerQualifiees(championnat, nombreGroupes, numeroPremierGroupe, nombreEquipes) {
    var param = 'championnat=' + championnat + '&groupes=' + nombreGroupes + '&equipes=' + nombreEquipes + '&numeroPremierGroupe=' + numeroPremierGroupe;
    for(var i = 0; i < nombreGroupes; i++) {
        var obj = $('#ulGroupe' + i + ' > li');
        obj.each(   function(j) {
                        param += '&groupe' + i + 'equipe' + j + '=' + $(this).attr('data-val');
                    }
        );
    }

    $.ajax( {
                url: 'gerer_qualification_maj_qualifications.php',
                type: 'POST',
                data: param
            }
    ).done(function(html) {
		afficherMessageInformationBandeau('Sauvegarde effectuée avec succès', 2000, '');
	});
}

// Gestion des qualifications - Validation des équipes qualifiées pour une poule
function gererQualification_validerQualifieesPoule(championnat, numeroIndice, numeroPremierGroupe, nombreEquipes) {
	var numeroGroupe = numeroIndice + numeroPremierGroupe;
    var param = 'championnat=' + championnat + '&equipes=' + nombreEquipes + '&groupe=' + numeroGroupe + '&numeroPremierGroupe=' + numeroPremierGroupe;
	var obj = $('#ulGroupe' + numeroIndice + ' > li');
	obj.each(   function(j) {
		param += '&groupe' + numeroIndice + 'equipe' + j + '=' + $(this).attr('data-val');
	});

    $.ajax( {
                url: 'gerer_qualification_maj_qualifications_poule.php',
                type: 'POST',
                data: param
            }
    ).done(function(html) {
		afficherMessageInformationBandeau('Sauvegarde et calculs effectués avec succès', 2000, '');
	});
}

// Gestion des poules - Création des poules
function gererPoules_creerPoules(championnat, nombreGroupes, numeroPremierGroupe, nombreEquipes) {
    var param = 'championnat=' + championnat + '&groupes=' + nombreGroupes + '&equipes=' + nombreEquipes + '&numeroPremierGroupe=' + numeroPremierGroupe;
    for(var i = 0; i < nombreGroupes; i++) {
        var obj = $('#tdGroupe' + i + ' > select');
        obj.each(   function(j) {
                        param += '&groupe' + i + 'equipe' + j + '=' + $(this).val();
                    }
        );
    }

    $.ajax( {
                url: 'gerer_poules_maj_poules.php',
                type: 'POST',
                data: param
            }
    ).done(function(html) {
		afficherMessageInformationBandeau('Création des poules effectuée avec succès', 2000, '');
	});
}

// Classements pronostiqueurs - Affichage d'une journée
function classementsPronostiqueurs_afficherJournee(championnat, journee, dateReference, nomDiv, affichageNeutre, sansButeur) {
    $.ajax( {
                url: 'classements_pronostiqueurs_affichage_journee.php',
                type: 'POST',
                data:   {   journee: journee
							,date_reference: dateReference
							,championnat: championnat
							,sans_buteur: sansButeur
                        }
            }
    ).done(function(html) {
                $('#' + nomDiv).html(html);

				// Dans le cas d'un affichage neutre, ne pas mettre en surbrillance les lignes du pronostiqueur connecté
				if(affichageNeutre == 1) {
					var styleNormal = $('.tableau--classement--corps td').css('background-color');
					$('.tableau--classement tbody td.surbrillance').css('background-color', styleNormal + ' !important');
				}
            }
    );
}

// Classements pronostiqueurs - Affichage de la fiche d'identité d'un pronostiqueur
function classementsPronostiqueurs_afficherPronostiqueur(pronostiqueurConsulte) {
	// Dans le cas où la touche Contrôle est enfoncée, il convient d'afficher une partie du CC mais de manière simulée
	if(window.event.ctrlKey || window.event.shiftKey) {
		if($('.cc').length == 0) {
			$('body').append('<div class="cc" style="background-color: #fff; color: #000;"><div class="cc--contenu-interieur" style="z-index: 20000; width: 1168px; height: 900px;"></div></div>');
		}

		var appelClassique = 0;
		var onglet = 1;
		var sousOnglet = 2;
		$.when(concoursCentre_afficherPronostiqueurs('cc--contenu-interieur', onglet, 'cc--pronostiqueurs-entete', 'cc--pronostiqueurs-detail', pronostiqueurConsulte)).done(function() {
			$.when(concoursCentre_afficherPronostiqueurDetail(pronostiqueurConsulte, 'cc--pronostiqueurs-detail', sousOnglet)).done(function() {
				$('.cc').dialog({
					title: 'Détail du pronostiqueur',
					modal: true,
					autoOpen: false
					,width: 'auto'
					,height: 'auto'
					,position: 'center'
					,close: function() {
						$(this).dialog('close');
						$(this).empty().remove();
						cc_ongletActif = 0;
						cc_sousOngletActif = 0;
						cc_pronostiqueurConsulte = 0;
					}
					,buttons: {
						'Fermer': function() {
							$(this).dialog('close');
							$(this).empty().remove();
							cc_ongletActif = 0;
							cc_sousOngletActif = 0;
							cc_pronostiqueurConsulte = 0;
						}
					}
				});
				$('.cc').dialog('open');
			});
		});
	}
	else {
		$.ajax( {
					url: 'consulter_fiches_affichage_pronostiqueur.php',
					type: 'POST',
					data:   {   pronostiqueurConsulte: pronostiqueurConsulte
								,modeFenetre: 1
							}
				}
		).done(function(html) {
			if($('.fiche').length == 0)
				$('body').append('<div class="fiche"></div>');
			$('.fiche').empty().append(html);
			$('.fiche').addClass('fondTransparent');
			$('.fiche').dialog( {
				autoOpen: false
				,width: 'auto'
				,height: 'auto'
				,modal: true
				,title: 'Fiche d\'identité'
				,position: 'center'
				,buttons: {
					'Fermer':   function() {
								$(this).dialog('close');
							}
				}

			});

			$('.fiche').dialog('open');

		});
	}
}

// Classements divisions pronostiqueurs - Affichage d'une période
function classementsDivisionsPronostiqueurs_afficherPeriode(periode, nomDiv, affichageNeutre) {
    $.ajax( {
                url: 'classements_divisions_pronostiqueurs_affichage_periode.php',
                type: 'POST',
                data:   {   periode: periode
                        }
            }
    ).done(function(html) {
                $('#' + nomDiv).html(html);

				// Dans le cas d'un affichage neutre, ne pas mettre en surbrillance les lignes du pronostiqueur connecté
				if(affichageNeutre == 1) {
					var styleNormal = $('.tableau--classement--corps td').css('background-color');
					$('.tableau--classement tbody td.surbrillance').css('background-color', styleNormal + ' !important');
				}
            }
    );
}

// Création de la fiche d'identité - Validation de la fiche
function creerFiche_validerFiche(premiereConnexion) {
    var nom = $('#txtNom').val();
    var prenom = $('#txtPrenom').val();
    var mel = $('#txtMEL').val();
    var dateDeNaissance = $('#txtDateDeNaissance').val();
    var lieuDeResidence = $('#txtLieuDeResidence').val();
    var equipeFavorite = $('#txtEquipeFavorite').val();
    var ambitions = $('#taAmbitions').val();
    var carriere = $('#taCarriere').val();
    var commentaire = $('#taCommentaire').val();

    $.ajax( {
                url: 'creer_fiche_maj_fiche.php',
                type: 'POST',
                data:   {   nom: nom
                            ,prenom: prenom
                            ,mel: mel
                            ,dateDeNaissance: dateDeNaissance
                            ,lieuDeResidence: lieuDeResidence
                            ,equipeFavorite: equipeFavorite
                            ,ambitions: ambitions
                            ,carriere: carriere
                            ,commentaire: commentaire
                        }
            }
    ).done(function(html) {
        if(premiereConnexion)
            afficherMessageInformationBandeau('Sauvegarde effectuée avec succès', 2000, 'accueil.php');
        else
            afficherMessageInformationBandeau('Sauvegarde effectuée avec succès', 2000, '');
    });
}

// Consultation de la fiche d'identité - Consultation d'une fiche
function consulterFiches_consulterFiche(pronostiqueurConsulte) {
    $.ajax( {
                url: 'consulter_fiches_affichage_pronostiqueur.php',
                type: 'POST',
                data:   {   pronostiqueurConsulte: pronostiqueurConsulte
                            ,modeFenetre: 0
                        }
            }
    ).done(function(html) {
		if($('.fiche').length == 0)
            $('body').append('<div class="fiche"></div>');
        $('.fiche').empty().append(html);
    });

}

// Consultation de la fiche d'identité - MAJ d'une fiche
function consulterFiches_majPronostiqueur(pronostiqueurConsulte) {
    var prenom = $('#txtPrenom').val();
    var mel = $('#txtMEL').val();
    var dateDeNaissance = $('#txtDateDeNaissance').val();
    var lieuDeResidence = $('#txtLieuDeResidence').val();
    var ambitions = $('#taAmbitions').val();
    var palmares = $('#taPalmares').val();
    var carriere = $('#taCarriere').val();
    var commentaire = $('#taCommentaire').val();

    $.ajax( {
                url: 'creer_fiche_maj_fiche_administrateur.php',
                type: 'POST',
                data:   {   pronostiqueurConsulte: pronostiqueurConsulte
                            ,prenom: prenom
                            ,mel: mel
                            ,dateDeNaissance: dateDeNaissance
                            ,lieuDeResidence: lieuDeResidence
                            ,ambitions: ambitions
                            ,palmares: palmares
                            ,carriere: carriere
                            ,commentaire: commentaire
                        }
            }
    ).done(function(html) {

    });
}

// Consultation de la fiche d'identié - Ajout / suppression de rival
function consulterFiches_ajoutRival(pronostiqueurConsulte, mode) {
    $.ajax( {
                url: 'consulter_fiches_ajout_rival.php',
                type: 'POST',
                data:   {
                            pronostiqueurConsulte: pronostiqueurConsulte,
                            mode: mode
                        }
            }
    ).done(function(html) {
        // Si le mode rival est activé et que l'on vient d'ajouter ou de supprimer un rival, il est nécessaire de rafraîchir la fenêtre
    });
}

// Consultation de trohpées - Affichage d'une journée
function consulterTrophees_afficherJournee(championnat, journee, nomDiv) {
    $.ajax( {
                url: 'consulter_trophees_affichage_journee.php',
                type: 'POST',
                data:   {   journee: journee
                            ,championnat: championnat
                        }
            }
    ).done(function(html) {
                $('#' + nomDiv).html(html);
            }
    );
}

// Module du classement général et de journée - Affichage des pronostics en cours
function moduleClassementGeneral_afficherPronostics(nomDiv) {
    var fenetrePronostics = $('#' + nomDiv);
    $('#' + nomDiv).dialog( {
        autoOpen: false
        ,width: 'auto'
        ,height: 'auto'
        ,modal: true
        ,title: 'Pronostics en cours'
        ,position: 'center'
        ,buttons: {
            'Fermer':   function() {
                        fenetrePronostics.dialog('close');
                    }
        }

    });
    fenetrePronostics.dialog('open');
}

// Module d'affichage des résultats d'une journée - Affichage des détails d'un match
function consulterMatch_afficherMatch(match) {
    $.ajax( {
                url: 'consulter_match.php',
                type: 'POST',
                data:   {
                            match: match
                        }
            }
    ).done(function(html) {
		if($('.consulter-match').length == 0)
			$('body').append('<div class="consulter-match"></div>');

		$('.consulter-match').empty().append(html);

        $('.consulter-match').addClass('fondTransparent');
        var fenetreMatch = $('.consulter-match');
        $('.consulter-match').dialog( {
            autoOpen: false
            ,width: 'auto'
            ,height: 'auto'
            ,modal: true
            ,title: 'Détail du match'
            //,position: 'center'
            ,buttons: {
                'Fermer':   function() {
                            fenetreMatch.dialog('close');
                        }
            }

        });

        fenetreMatch.dialog('open');
    });
}

// Module d'affichage des résultats d'une journée - Affichage de la répartition des vainqueurs pronostiqués d'un match régulier (type 1 et 2) avec 3 résultats possibles (victoire, match nul, défaite)
function consulterMatch_afficherRepartitionVainqueurPronostiqueMatchRegulier(match) {
	$.ajax(	{
				url: 'consulter_match_repartition_pronostics_match_regulier.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Répartition des pronostics'
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
            }
        });

        $('.info').dialog('open');
	});
}

// Module d'affichage des résultats d'une journée - Affichage de la répartition des vainqueurs pronostiqués d'un match de coupe (type 4 et 5) avec 2 résultats possibles (victoire, défaite)
function consulterMatch_afficherRepartitionVainqueurPronostiqueMatchCoupe(match) {
	$.ajax(	{
				url: 'consulter_match_repartition_pronostics_match_coupe.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Répartition des pronostics'
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
            }
        });

        $('.info').dialog('open');
	});
}

// Module d'affichage des résultats d'une journée - Affichage de la répartition des vainqueurs pronostiqués pour le match retour d'une confrontation directe (victoire, match nul, défaite)
function consulterMatch_afficherResultatMatchRetour(match) {
	$.ajax(	{
				url: 'consulter_match_resultat_match_retour.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Répartition des pronostics du match retour avant TAB'
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
            }
        });

        $('.info').dialog('open');
	});
}

// Module d'affichage des résultats d'une journée - Affichage de la répartition des équipes qualifiées pronostiqués
function consulterMatch_afficherRepartitionVainqueurQualifie(match) {
	$.ajax(	{
				url: 'consulter_match_equipe_qualifiee.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		$('.info').dialog( {
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Répartition des pronostics de qualification'
			,position: 'center'
			,buttons: {
				'Fermer':	function() {
								$(this).dialog('close');
							}
            }
        });

        $('.info').dialog('open');
	});
}

// Affichage / masquage des modules du concours
// Est appelé uniquement depuis le menu des modules
// Ne concerne donc que les modules de type championnat (hors tchat)
function afficherMasquerModule(module, nomConteneur, parametre) {
	// Le fonctionnement est le suivant :
	// - si le groupe est activé :
	//   * si le module est activé, alors le masquer
	//   * si le module n'est pas activé, alors l'afficher
	// - si le groupe n'est pas activé, alors ne rien faire de spécial sur le module

	// Cette fonction affiche un module si celui-ci est masqué (ou l'inverse)
	if($('#' + nomConteneur + parametre).length == 0)
		modules_afficherModule(module, nomConteneur, parametre);
	else
		modules_masquerModule(module, nomConteneur, parametre);

}

// Bascule le module de championnat (hors tchat) d'un état à l'autre
// On détermine s'il faut afficher ou non le module selon l'état d'activation du groupe
function basculerEtatModule(groupeActif, moduleActif, module, nomConteneur, parametre) {
	if(groupeActif == 1) {
		// le groupe en question étant activé, le fait d'afficher ou de masquer un module doit se voir tout de suite
		afficherMasquerModule(module, nomConteneur, parametre);
	}
	else {
		// Ici, on ne fait que sauvegarder le nouvel état (affiché ou masqué) du module sans faire de mise à jour visuelle
		// puisque le module n'est pas censé être visible
		modules_sauvegarderEtatModule(moduleActif, module, nomConteneur, parametre);
	}
}

// Bascule le groupe de module d'un état à l'autre
function basculerEtatGroupeModule(groupeActif, parametre) {
	$.ajax(	{
				url: 'modules_sauvegarde_etat_groupe.php',
				type: 'POST',
				data:	{
							parametre: parametre,
							groupe_actif: groupeActif
						}
			}
	).done(function(html) {
		// Une fois un groupe de modules activé ou masqué, il est nécessaire d'afficher / masquer les modules qui appartiennent à ce groupe
		// Pour cela, il est nécessaire de parcourir la liste des modules actifs du groupe et de les afficher ou masquer
		$.ajax(	{
					url: 'modules_affichage_masquage_module.php',
					type: 'POST',
					data:	{
								parametre: parametre
							},
					dataType: 'json'
				}
		).done(function(html) {
			// Une fois un groupe de modules activé ou masqué, il est nécessaire d'afficher / masquer les modules qui appartiennent à ce groupe
			// Pour cela, il est nécessaire de parcourir la liste des modules actifs du groupe et de les afficher ou masquer
			// Etant donné que le masquage d'un module affecte son état, il est nécessaire de le réécrire comme activé après l'avoir supprimé
			for(var i = 0; i < html.donnees.length; i++) {
				if(groupeActif == 1) {
					afficherMasquerModule(html.donnees[i].Module, 'divModule' + html.donnees[i].Module, html.donnees[i].Modules_Parametre);
				}
				else {
					afficherMasquerModule(html.donnees[i].Module, 'divModule' + html.donnees[i].Module, html.donnees[i].Modules_Parametre);
					modules_sauvegarderEtatModule(1, html.donnees[i].Module, 'divModule' + html.donnees[i].Module, html.donnees[i].Modules_Parametre);
				}
			}
		});
	});
}


// Modules

// Définition d'une variable globale qui permet de savoir quel est le numéro du dernier message affiché (module tchat)
var dernierMessage = 0;

// Module de tchat - Envoi d'un message
function moduleTchat_envoyerMessage(nomDiv, tchatGroupe) {
	var message = $('#' + nomDiv).find('textarea').val();

	$.ajax(	{
				url: 'module_tchat_gestion_message.php',
				type: 'POST',
				data:	{
							action: 'ajoutMessage',
							message: message,
							tchatGroupe: tchatGroupe
						},
				dataType: 'json'
			}
	).done(function(html) {
		$('#' + nomDiv + ' textarea').val('');
		if(html.etat == 'OK') {
			moduleTchat_lectureDerniersMessages(nomDiv, tchatGroupe);
		}
		else {
		}
	});
}

// Module de tchat - Lecture des derniers messages
function moduleTchat_lectureDerniersMessages(nomDiv, tchatGroupe) {
	$.ajax(	{
				url: 'module_tchat_gestion_message.php',
				type: 'POST',
				data:	{
							action: 'lectureMessage',
							dernierMessage: dernierMessage,
							tchatGroupe: tchatGroupe
						},
				dataType: 'json'
			}
	).done(function(html) {
		$(nomDiv).append(html);
		dernierMessage = html.dernierMessage;
	});
}

// Module de tchat - Création d'une conversation avec un interlocuteur
function moduleTchat_creerConversation(interlocuteur) {

	// Création de la discussion (ainsi que le pronostiqueur ayant créé la discussion)
	$.ajax(	{
				url: 'module_tchat_groupe_creation_ajout.php',
				type: 'POST',
				data:	{
							typeTchat: 0,
							nomTchatGroupe: 'Discussion',
							listePronostiqueurs: interlocuteur
						},
				dataType: 'json'
			}
	).done(function(html) {
		// Ouverture automatique d'une fenêtre de tchat
		modules_afficherModule(numeroModuleTchat, 'divModule' + numeroModuleTchat, html.tchatGroupe);
	}).fail(function(html) { console.log('Fonction moduleTchat_creerConversation : dans le fail'); });

}

// Module de tchat de groupe - Création d'un tchat de groupe
function moduleTchatGroupe_creerTchatGroupe(nomTchatGroupe, listePronostiqueurs) {
	$.ajax(	{
				url: 'module_tchat_groupe_creation.php',
				type: 'POST'
			}

	).done(function(html) {
        if($('.tchat').length == 0) {
            $('body').append('<div class="tchat"></div>');
			$('.tchat').append(html);
		}
		else
			$('.tchat').empty().append(html);
        $('.tchat').dialog( {
            autoOpen: false
            ,width: 'auto'
            ,height: 'auto'
            ,modal: true
            ,title: 'Création d\'un tchat de groupe'
            ,position: 'center'
            ,buttons: {
				'Créer':  function() {
								// On vérifie que le nom du groupe n'est pas vide (ce n'est pas important s'il existe déjà, même pour le même pronostiqueur)
								if($('#' + nomTchatGroupe).val() == '' || $('#' + listePronostiqueurs).val() == '') {
									alert('Veuillez saisir un nom de groupe et/ou des pronostiqueurs');
									return;
								}

								$(this).dialog('close');

								// Création du tchat de groupe et ajout des pronostiqueurs (ainsi que le pronostiqueur ayant créé le groupe)
								$.ajax(	{
											url: 'module_tchat_groupe_creation_ajout.php',
											type: 'POST',
											data:	{
														typeTchat: 1,
														nomTchatGroupe: $('#' + nomTchatGroupe).val(),
														listePronostiqueurs: $('#' + listePronostiqueurs).val()
													},
											dataType: 'json'
										}

								).done(function(html) {
									// Ouverture automatique d'une fenêtre de tchat
									modules_afficherModule(numeroModuleTchat, 'divModule' + numeroModuleTchat, html.tchatGroupe);
								});

							},
                'Annuler':   function() {
                            $(this).dialog('close');
                        }
            }

        });

        $('.tchat').dialog('open');
	});
}

// Module de tchat de groupe - Création d'une conversation avec un interlocuteur
function moduleTchatGroupe_creerConversation(module, nomConteneur, parametre) {
	// Sélection de l'interlocuteur
	$.ajax(	{
				url: 'module_tchat_groupe_liste_pronostiqueurs.php',
				type: 'POST',
				data:	{
							typeTchat: 0,
							pronostiqueursSelectionnes: ''
						}
			}

	).done(function(html) {
        if($('.conversation').length == 0)
            $('body').append('<div class="conversation"></div>');
        $('.conversation').empty().append(html);
        $('.conversation').dialog( {
            autoOpen: false
            ,width: 'auto'
            ,height: 'auto'
            ,modal: true
            ,title: 'Discuter avec...'
            ,position: 'center'
            ,buttons: {
				'Valider':  function() {
								$(this).dialog('close');
								var param = '';

								// Pour une conversation, pronostiqueur sélectionné
								$('.conversation input[type="radio"]:checked ').each(function() {
										param = $(this).attr('value');
									}
								);

								// Création de la discussion (ainsi que le pronostiqueur ayant créé la discussion)
								$.ajax(	{
											url: 'module_tchat_groupe_creation_ajout.php',
											type: 'POST',
											data:	{
														typeTchat: 0,
														nomTchatGroupe: 'Discussion',
														listePronostiqueurs: param
													},
											dataType: 'json'
										}
								).done(function(html) {
									// Ouverture automatique d'une fenêtre de tchat
									modules_afficherModule(numeroModuleTchat, 'divModule' + numeroModuleTchat, html.tchatGroupe);
								}).fail(function(html) { console.log('Fonction moduleTchatGroupe_creerConversation : dans le fail'); });
							},
                'Annuler':   function() {
                            $(this).dialog('close');
                        }
            }

        });

        $('.conversation').dialog('open');
	});
}

// Module de tchat de groupe - Suppression d'un tchat de groupe
function moduleTchatGroupe_supprimerTchatGroupe(tchatGroupe, tchatGroupeIdentifiant) {
	if(confirm('Etes-vous sûr de bien vouloir supprimer ce groupe de tchat ainsi que tous ses messages ?')) {
		$.ajax(	{
					url: 'module_tchat_groupe_suppression.php',
					type: 'POST',
					data:	{
								tchatGroupe: tchatGroupe
							},
					dataType: 'json'
				}
		).done(function(html) {
			if(html.etat == 'OK') {
				// Suppression de la ligne dans le menu
				$('#' + tchatGroupeIdentifiant).remove();
			}
		});
	}
}

// Module de tchat de groupe - Ajout / suppression de pronostiqueurs
function moduleTchatGroupe_selectionnerPronostiqueurs(nomControle) {
	$.ajax(	{
				url: 'module_tchat_groupe_liste_pronostiqueurs.php',
				type: 'POST',
				data:	{
							typeTchat: 1,
							pronostiqueursSelectionnes: $('#' + nomControle).val()
						}
			}

	).done(function(html) {
        if($('#divTchatGroupeListePronostiqueurs').length == 0)
            $('body').append('<div id="divTchatGroupeListePronostiqueurs"></div>');
        $('#divTchatGroupeListePronostiqueurs').empty().append(html);
        $('#divTchatGroupeListePronostiqueurs').dialog( {
            autoOpen: false
            ,width: 'auto'
            ,height: 'auto'
            ,modal: true
            ,title: 'Sélection des pronostiqueurs'
            ,position: 'center'
            ,buttons: {
				'Valider':  function() {
								$(this).dialog('close');
								var param = '';

								var i = 0;
								$('#divTchatGroupeListePronostiqueurs input[type="checkbox"]:checked ').each(function() {
																				param += $(this).attr('value') + ';';
																			}
																		);

								$('#' + nomControle).val(param);
							},
                'Annuler':   function() {
                            $(this).dialog('close');
                        }
            }

        });

        $('#divTchatGroupeListePronostiqueurs').dialog('open');
	});
}

// Modules - Activation / désactivation du mode rival
function modules_changerModeRival(module, modeRival, parametre) {
    $.ajax( {
                url: 'modules_sauvegarde_mode_rival.php',
                type: 'POST',
                data:   {
                            module: module,
                            modeRival: modeRival,
							parametre: parametre
                        }
            }
    );
}

// Modules - Activation / désactivation du mode concurrent direct
function modules_changerModeConcurrentDirect(module, modeConcurrentDirect, parametre) {
    $.ajax( {
                url: 'modules_sauvegarde_mode_concurrent_direct.php',
                type: 'POST',
                data:   {
                            module: module,
                            modeConcurrentDirect: modeConcurrentDirect,
							parametre: parametre
                        }
            }
    ).done(function(html) {});
}

// Modules - Activation / désactivation du mode incrustation
function modules_changerModeIncrustation(module, modeIncrustation, parametre) {
	// Le mode incrustation arrive tel qu'il a été lu en base
	// Il faut donc l'inverser
	var nouveauModeIncrustation = (modeIncrustation == 1) ? 0 : 1;

    $.ajax( {
                url: 'modules_sauvegarde_mode_incrustation.php',
                type: 'POST',
                data:   {
                            module: module,
                            modeIncrustation: nouveauModeIncrustation,
							parametre: parametre
                        }
            }
    ).done(function(html) {

	});
}

// Modules - Changement de l'intervalle de rafraîchissement
function modules_changerIntervalleRafraichissement(module, parametre, intervalleRafraichissement) {

	$.ajax(	{
				url: 'modules_sauvegarde_intervalle_rafraichissement.php',
				type: 'POST',
				data:	{
							module: module,
							parametre: parametre,
							intervalleRafraichissement: intervalleRafraichissement
						}
			}
	).done(function(html) {
	});
}

// Modules - Affichage d'un module (affichage uniquement)
function modules_afficherModule(module, nomConteneur, parametre) {
	// Si le module existe déjà, on ne fait rien
    if($('#' + nomConteneur + parametre).length != 0) {
        return;
    }
    else {
		// Avant d'appeler le module, on regarde le nom de la page où l'on se trouve pour lui passer cette information
		var nomPage = $('#nomPage').val();
		if(nomPage == null)
			nomPage = '';


        // Création du module
        $.ajax( {
                    url: 'modules.php',
                    type: 'POST',
                    data:   {
                                appelAjax: 1,
                                module: module,
								parametre: parametre,
								nomPage: nomPage
                            }
                }
        ).done(function(html) {
			$('body').append(html);
            modules_sauvegarderEtatModule(1, module, nomConteneur, parametre);
        }).fail(function(html) { console.log('Fonction modules_afficherModule : dans le fail'); });
    }
}

// Modules - Masquage d'un module
function modules_masquerModule(module, nomConteneur, parametre) {
    if($('#' + nomConteneur + parametre).length != 0) {
        $('#' + nomConteneur + parametre).css('display', 'none');
        modules_sauvegarderEtatModule(0, module, nomConteneur, parametre);
		$('#' + nomConteneur + parametre).find('.module--contenu').getNiceScroll().remove();
        $('#' + nomConteneur + parametre).remove();
    }
}

// Modules - Relance d'un module
function modules_relancerModule(module, nomConteneur, parametre) {
    // Détruit le module
    modules_masquerModule(module, nomConteneur, parametre);

    // ... pour le recréer juste après
    modules_afficherModule(module, nomConteneur, parametre);
}

// Modules - Sauvegarde de l'état (actif ou non) d'un module
function modules_sauvegarderEtatModule(moduleActif, module, nomConteneur, parametre) {
    // Cette fonction sauvegarde en base l'état (affiché ou masqué) d'un module
    $.ajax( {
                url: 'modules_sauvegarde_etat.php',
                type: 'POST',
                data:   {
                            module: module,
							parametre: parametre,
                            actif: moduleActif
                        }
            }
    ).done(function(html) {
    });
}

// Modules - Sauvegarde de la position d'un module
function modules_sauvegarderPositionModule(module, nomConteneur, parametre) {
    // Cette fonction regarde la position en X et en Y d'un module et l'écrit en base de données
    // Lecture des coordonnées du module
    var coordonnees = $('#' + nomConteneur + parametre).position();
    if(coordonnees != null) {
        $.ajax( {
                    url: 'modules_sauvegarde_position.php',
                    type: 'POST',
                    data:   {
                                module: module,
								parametre: parametre,
                                x: coordonnees.left,
                                y: coordonnees.top
                            }
                }
        ).done(function(html) {
        });
    }
}

// Modules - Sauvegarde de la taille d'un module
function modules_sauvegarderTailleModule(module, nomConteneur, parametre) {
    // Cette fonction regarde la largeur et la hauteur d'un module et l'écrit en base de données
    // Lecture des coordonnées du module
    var largeur = $('#' + nomConteneur + parametre).width();
    var hauteur = $('#' + nomConteneur + parametre).height();

    $.ajax( {
                url: 'modules_sauvegarde_taille.php',
                type: 'POST',
                data:   {
                            module: module,
							parametre: parametre,
                            largeur: largeur,
                            hauteur: hauteur
                        }
            }
    ).done(function(html) {
    });
}



// Concours centre

// Concours centre - Affichage du Concours centre
function concoursCentre_afficherConcoursCentre() {
	// Si la page n'est pas affichée tout en haut, alors il faut le forcer manuellement
	if ($(window).scrollTop() > 0) {
		$('html, body').animate({scrollTop : 0}, 500);
	}

	// Lecture des dimensions de la fenêtre
	var largeur = $('body').width();


	$.ajax(	{
				url: 'concours_centre.php',
				type: 'POST'
			}
	).done(function(html) {
		if($('.cc').length == 0)
			$('body').append('<div class="cc" style="display: none;"></div>');

		$('.cc').empty().append(html);

		var gauche = (largeur - $('.cc').width()) / 2;

		$('.cc').css({ top: '0px' });
		$('.cc').css({ left: gauche + 'px' });

		// Affichage de haut en bas
		$('.cc').slideDown(500);

	});

}

// Concours centre - Masquage du Concours centre
function concoursCentre_masquerConcoursCentre() {
	if($('.cc').length != 0) {
		$('.cc').slideUp(500, function() {
			$('.cc').remove();
			cc_ongletActif = 0;
			cc_sousOngletActif = 0;
		});
	}
}

// Concours centre - Monter / descendre dans la liste des vignettes
function concoursCentre_monterListeVignettes(classe) {
	var y = $('.' + classe).scrollTop();
	var decalage = parseInt($('.' + classe).css('height')) / 1.2;

	$('.' + classe).animate({scrollTop: y - decalage}, 500);
}

// Concours centre - Monter / descendre dans la liste des vignettes
function concoursCentre_descendreListeVignettes(classe) {
	var y = $('.' + classe).scrollTop();
	var decalage = parseInt($('.' + classe).css('height')) / 1.2;

	$('.' + classe).animate({scrollTop: y + decalage}, 500);
}

// Concours centre - Affichage des pronostiqueurs du concours
function concoursCentre_afficherPronostiqueurs(classe, ongletActif, classeEntete, classeDetail, pronostiqueurConsulte, appelClassique) {
	// Si l'onglet affiché est l'onglet actif, alors ne rien faire
	if(cc_ongletActif == ongletActif)
		return;

	var largeur = parseInt($('.' + classe).css('width'));

	if(appelClassique == 1)
		cc_sousOngletActif = 1;
	else
		cc_sousOngletActif = 2;

	$.ajax(	{
				url: 'concours_centre_affichage_pronostiqueurs.php',
				type: 'POST',
				data:	{
							largeur: largeur,
							sousOnglet: cc_sousOngletActif,
							pronostiqueurConsulte: pronostiqueurConsulte
						}
			}

	).done(function(html) {
		cc_ongletActif = ongletActif;
		cc_pronostiqueurConsulte = pronostiqueurConsulte;

		if(appelClassique == 1) {
			$('.' + classe).fadeOut(500, function() {
				// A l'ouverture de la page, on affiche l'en-tête et la fiche du pronostiqueur connecté
				$.ajax(	{
							url: 'concours_centre_affichage_pronostiqueurs_entete.php',
							type: 'POST',
							data:	{
										pronostiqueurConsulte: pronostiqueurConsulte
									}
						}
				).done(function(htmlEntete) {
					$.ajax(	{
								url: 'concours_centre_affichage_pronostiqueurs_detail.php',
								type: 'POST',
								data:	{
											pronostiqueurConsulte: pronostiqueurConsulte,
											sousOnglet: 1
										}
							}
					).done(function(htmlDetail) {
						$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
						$('.' + classeEntete).fadeOut(250, function() {
							$(this).empty().append(htmlEntete).fadeIn(250);
						});
						$('.' + classeDetail).fadeOut(250, function() {
							$(this).empty().append(htmlDetail).fadeIn(250);
						});
					});
				});
			});
		}
		else {
			return $.ajax(	{
						url: 'concours_centre_affichage_pronostiqueurs_entete.php',
						type: 'POST',
						data:	{
									pronostiqueurConsulte: pronostiqueurConsulte
								}
					}
			).done(function(htmlEntete) {
				$.ajax(	{
							url: 'concours_centre_affichage_pronostiqueurs_detail.php',
							type: 'POST',
							data:	{
										pronostiqueurConsulte: pronostiqueurConsulte,
										sousOnglet: 2
									}
						}
				).done(function(htmlDetail) {
					$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
					$('.' + classeEntete).empty().append(htmlEntete)
					$('.' + classeDetail).empty().append(htmlDetail);
				});
			});
		}
	});
}

// Concours centre - Affichage de l'en-tête d'un pronostiqueur
function concoursCentre_afficherPronostiqueurEntete(pronostiqueurConsulte, classe) {
	$.ajax(	{
				url: 'concours_centre_affichage_pronostiqueurs_entete.php',
				type: 'POST',
				data:	{
							pronostiqueurConsulte: pronostiqueurConsulte
						}
			}
	).done(function(html) {
		$('.' + classe).fadeOut(250, function() {
			$(this).empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des détail d'un pronostiqueur (informations différentes à afficher, qui dépendent du sous-onglet actif)
// Si le paramètre pronostiqueurConsulte vaut 0, cela signifie que l'on est déjà sur un pronostiqueur et que l'on désire afficher une information différente (changer de sous-onglet)
// Si le paramètre sousOnglet vaut 0, cela signifie que l'on est déjà sur un onglet d'information et que l'on désire passer sur un autre pronostiqueur
function concoursCentre_afficherPronostiqueurDetail(pronostiqueurConsulte, classe, sousOnglet) {
	if(pronostiqueurConsulte == 0)
		pronostiqueurConsulte = cc_pronostiqueurConsulte;
	else
		cc_pronostiqueurConsulte = pronostiqueurConsulte;

	if(sousOnglet == 0) {
		sousOnglet = cc_sousOngletActif;
	}
	else
		cc_sousOngletActif = sousOnglet;

	return $.ajax(	{
				url: 'concours_centre_affichage_pronostiqueurs_detail.php',
				type: 'POST',
				data:	{
							pronostiqueurConsulte: pronostiqueurConsulte,
							sousOnglet: sousOnglet,
							zoneDessinLargeur: $('.' + classe).width(),
							zoneDessinHauteur: 150
						}
			}
	).done(function(html) {
		$('.' + classe).fadeOut(250, function() {
			$(this).empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des onglets des championnats pour les statistiques buteur
function concoursCentre_afficherStatistiquesButeur(classe, ongletActif) {
	if(cc_ongletActif == ongletActif)
		return;

	cc_ongletActif = ongletActif;
	cc_sousOngletActif = 0;

	// Affichage des sous-onglets des championnats
	$.ajax(	{
			url: 'concours_centre_affichage_statistiques_buteur_onglets.php',
			type: 'POST'
		}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des statistiques buteur pour un championnat
function concoursCentre_afficherStatistiquesButeurChampionnat(championnat, classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_statistiques_buteur.php',
				type: 'POST',
				data:	{
							championnat: championnat
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": 0
			}]
			,"scrollY": "680px"
			,"scrollX": true
			,"bPaginate": false
			,"bFilter": false
			,"bInfo": false
			,"order": [[2, 'desc'], [3, 'desc'], [4, 'desc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable);

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});


	});
}

// Concours centre - Affichage des onglets des championnats pour le palmarès 2015
function concoursCentre_afficherPalmares(classe, ongletActif) {
	// Si l'onglet affiché est l'onglet actif, alors ne rien faire
	if(cc_ongletActif == ongletActif)
		return;

	cc_ongletActif = ongletActif;
	cc_sousOngletActif = 0;

	// Affichage des sous-onglets des championnats
	$.ajax(	{
			url: 'concours_centre_affichage_palmares_onglets.php',
			type: 'POST'
		}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des palmarès pour un championnat
function concoursCentre_afficherPalmaresChampionnat(championnat, classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_palmares.php',
				type: 'POST',
				data:	{
							championnat: championnat
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": 0
			}]
			,"scrollY": "680px"
			,"scrollX": true
			,"bPaginate": false
			,"bFilter": false
			,"bInfo": false
			,"order": [[2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable, {leftColumns: 2});

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});
	});
}

// Concours centre - Affichage des onglets des championnats pour la répartition des points
function concoursCentre_afficherRepartitionPoints(classe, ongletActif) {
	// Si l'onglet affiché est l'onglet actif, alors ne rien faire
	if(cc_ongletActif == ongletActif)
		return;

	cc_ongletActif = ongletActif;
	cc_sousOngletActif = 0;

	// Affichage des sous-onglets des championnats
	$.ajax(	{
			url: 'concours_centre_affichage_repartition_points_onglets.php',
			type: 'POST'
		}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage de la répartition des points pour un championnat
function concoursCentre_afficherRepartitionPointsChampionnat(championnat, classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_repartition_points.php',
				type: 'POST',
				data:	{
							championnat: championnat
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": 0
			}]
			,"scrollY": "680px"
			,"scrollX": true
			,"bPaginate": false
			,"bFilter": false
			,"bInfo": false
			,"order": [[2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [8, 'desc'], [9, 'desc'], [10, 'desc'], [11, 'asc'], [12, 'asc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable, {leftColumns: 2});

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});
	});
}

// Concours centre - Affichage de l'onglet des statistiques de ligue 1
function concoursCentre_afficherStatistiquesLigue1(classe, ongletActif) {
	if(cc_ongletActif == ongletActif)
		return;

	cc_ongletActif = ongletActif;
	cc_sousOngletActif = 0;

	// Affichage des sous-onglets
	$.ajax(	{
			url: 'concours_centre_affichage_statistiques_l1_onglets.php',
			type: 'POST'
		}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des victoires / nuls / défaites pronostiqués et réels
function concoursCentre_afficherVictoiresNulsDefaites(classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_victoires_nuls_defaites.php',
				type: 'POST',
				data:	{
							championnat: 1
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": 0
			}]
			,"scrollY": "670px"
			,"scrollX": true
			,"bPaginate": false
			,"bFilter": false
			,"bInfo": false, "order": [[4, 'desc'], [7, 'desc'], [10, 'desc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable);

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});
	});
}

// Concours centre - Affichage des pourcentages de points marqués
function concoursCentre_afficherPourcentagePoints(classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_pourcentage_points.php',
				type: 'POST',
				data:	{
							championnat: 1
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"scrollY": "610px"
			,"scrollX": true
			,paging: false
			,"bScrollCollapse": true
			,"bAutoWidth": true
			,"bFilter": false
			,"bInfo": false
			,"bSort": true
			,"order": [[2, 'desc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable, {leftColumns: 2});

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});
	});
}

// Concours centre - Affichage des points marqués par équipe
function concoursCentre_afficherPointsParEquipe(classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_points_par_equipe.php',
				type: 'POST',
				data:	{
							championnat: 1
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": 0
			}]
			,"scrollY": "670px"
			,"scrollX": true
			,paging: false
			,"bScrollCollapse": true
			,"bAutoWidth": true
			,"bFilter": false
			,"bInfo": false
			,"bSort": true
			,"order": [[1, 'asc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable, {leftColumns: 2});

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});
	});
}

// Concours centre - Affichage des points marqués par équipe et par ordre décroissant de points
function concoursCentre_afficherMeilleuresEquipes(classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_meilleures_equipes.php',
				type: 'POST',
				data:	{
							championnat: 1
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"scrollY": "670px"
			,"scrollX": true
			,paging: false
			,"bScrollCollapse": true
			,"bAutoWidth": true
			,"bFilter": false
			,"bInfo": false
			,"bSort": false
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable);
	});
}

// Concours centre - Affichage des points du match Canal
function concoursCentre_afficherMatchCanal(classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_match_canal.php',
				type: 'POST',
				data:	{
							championnat: 1
						}
			}
	).done(function(html) {
		$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html);
		var oTable = $('.cc--tableau').DataTable({
			"scrollY": "670px"
			,"scrollX": true
			,paging: false
			,"bScrollCollapse": true
			,"bAutoWidth": false
			,"bFilter": false
			,"bInfo": false
			,"bSort": true
			,"order": [[2, 'desc']]
		});
		var obj = new $.fn.dataTable.FixedColumns(oTable);

		oTable.column(0).nodes().each(function(cellule, i) {
			cellule.innerHTML = i + 1;
		}).draw();

		oTable.on('order.dt', function() {
			oTable.column(0).nodes().each(function(cellule, i) {
				cellule.innerHTML = i + 1;
			});
		});
	});
}

// Concours centre - Affichage des onglets des championnats pour les classements comparés
function concoursCentre_afficherClassements(classe, ongletActif, generalJournee) {
	// Si l'onglet affiché est l'onglet actif, alors ne rien faire
	if(cc_ongletActif == ongletActif)
		return;

	cc_ongletActif = ongletActif;
	cc_sousOngletActif = 0;

	// Affichage des sous-onglets des championnats
	$.ajax(	{
			url: 'concours_centre_affichage_classements_onglets.php',
			type: 'POST',
			data:	{
						generalJournee: generalJournee
					}
		}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des classements pour un championnat
function concoursCentre_afficherClassementsChampionnat(championnat, classe, sousOngletActif, generalJournee) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	$.ajax(	{
				url: 'concours_centre_affichage_classements.php',
				type: 'POST',
				data:	{
							championnat: championnat,
							zoneDessinLargeur: $('.' + classe).width(),
							zoneDessinHauteur: 600,
							generalJournee: generalJournee
						}
			}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Comparaison des classements entre deux pronostiqueurs
function concoursCentre_comparerClassementsPronostiqueur(championnat, classe, classeSecondaire, pronostiqueurConsulte, nombrePronostiqueurs, generalJournee) {
	// Il est nécessaire de recopier les informations de coordonnées et de taille de la classe de base vers la classe qui va accueillir le graphique secondaire
	$('.' + classeSecondaire).css('top', $('.' + classe).position().top + 'px');
	$('.' + classeSecondaire).css('left', $('.' + classe).position().left + 'px');
	$('.' + classeSecondaire).width($('.' + classe).width());
	$('.' + classeSecondaire).height($('.' + classe).height());

	$.ajax(	{
				url: 'concours_centre_affichage_classements_secondaires.php',
				type: 'POST',
				data:	{
							championnat: championnat,
							zoneDessinLargeur: $('.' + classeSecondaire).width(),
							zoneDessinHauteur: 600,
							nombrePronostiqueurs: nombrePronostiqueurs,
							pronostiqueurConsulte: pronostiqueurConsulte,
							generalJournee: generalJournee
						}
			}
	).done(function(html) {
		$('.' + classeSecondaire).fadeOut(500, function() {
			$('.' + classeSecondaire).empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage de l'onglet de sélection des équipes engagées dans le concours
function concoursCentre_afficherOngletEquipes(classe, ongletActif) {
	if(cc_ongletActif == ongletActif)
		return;

	cc_ongletActif = ongletActif;
	cc_sousOngletActif = 0;

	// Affichage des sous-onglets des championnats
	$.ajax(	{
			url: 'concours_centre_affichage_equipes_onglets.php',
			type: 'POST'
		}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$('.' + classe).removeClass('cc--contenu-interieur-initial').empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des équipes engagées dans le concours (L1 pure, européennes de L1, LDC et EL)
function concoursCentre_afficherEquipes(classe, sousOngletActif) {
	if(cc_sousOngletActif == sousOngletActif)
		return;

	cc_sousOngletActif = sousOngletActif;

	var largeur = parseInt($('.' + classe).css('width'));

	$.ajax(	{
				url: 'concours_centre_affichage_equipes.php',
				type: 'POST',
				data:	{
							largeur: largeur,
							typeEquipe: sousOngletActif
						}
			}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$(this).empty().append(html).fadeIn(250);
		});
	});
}

// Concours centre - Affichage des détail d'une équipe
function concoursCentre_afficherEquipeDetail(equipe, typeEquipe, classe) {
	$.ajax(	{
				url: 'concours_centre_affichage_equipes_detail.php',
				type: 'POST',
				data:	{
							equipe: equipe,
							typeEquipe: typeEquipe
						}
			}
	).done(function(html) {
		$('.' + classe).fadeOut(500, function() {
			$(this).empty().append(html).fadeIn(250);
		});
	});
}

// Coupe de France - Détail d'une confrontation
function cdf_afficherConfrontation(confrontation) {

    $.ajax(
            {
                url: 'cdf_consultation_confrontation.php',
                type: 'POST',
                data:   {   confrontation: confrontation
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreDetails = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,maxWidth: 900
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Détail de la confrontation'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreDetails.dialog('close');
						}
			}

		});
		fenetreDetails.dialog('open');
	});
}

// Coupe de France des saisons précédentes - Détail d'une confrontation
function cdf_prec_afficherConfrontation(saison, confrontation) {

    $.ajax(
            {
                url: 'cdf_prec_consultation_confrontation.php',
                type: 'POST',
                data:   {   saison: saison,
                            confrontation: confrontation
                        }
            }
    ).done(function(html) {
		if($('.info').length == 0)
			$('body').append('<div class="info"></div>');

		$('.info').empty().append(html);

		var fenetreDetails = $('.info');
		$('.info').dialog( {
			autoOpen: false
			,maxWidth: 900
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Détail de la confrontation'
			,position: 'center'
			,buttons: {
				'Fermer':   function() {
							fenetreDetails.dialog('close');
						}
			}

		});
		fenetreDetails.dialog('open');
	});
}

// Gestion de la Coupe de France - Modifier l'adresse de la vidéo
function gererCDF_modifierAdresseVideo(elt) {
    var adresse = $(elt).val();

    $.ajax(
            {
                url: 'gerer_cdf_modification_adresse_video.php',
                type: 'POST',
                data: {
                    adresse: adresse
                }
            }
    ).done(function(html) {

    });
}

// Gestion de la Coupe de France - Réinitialiser toutes les confrontations
function gererCDF_reinitialiserConfrontations() {
    if(!confirm('Etes-vous sûr de vouloir tout réinitialiser ?'))
        return;

    $.ajax(
            {
                url: 'gerer_cdf_reinitialisation.php',
                type: 'POST'
            }
    ).done(function(html) {
    });

}


// Gestion de la Coupe de France - Placement des 4 premiers pronostiqueurs de Ligue 1
function gererCDF_placerPronostiqueurs1A4() {
    $.ajax(
            {
                url: 'gerer_cdf_placement1a4.php',
                type: 'POST'
            }
    ).done(function(html) {
    });
}

// Gestion de la Coupe de France - Sélection d'un pronostiqueur
function gerer_cdf_selectionnerPronostiqueur(numeroCase) {
    if(numeroCase == null || numeroCase < 5 || numeroCase > 50)
        return;


    // Maintenant, on détermine si on modifie une case pour un joueur exempté ou une confrontation normale
    var exempte = (numeroCase >= 5 && numeroCase <= 14) ? 1 : 2;

    $.ajax(
        {
            url: 'gerer_cdf_liste_pronostiqueurs.php',
            type: 'POST',
            data:   {   exempte: exempte
                    }
        }
    ).done(function(html) {
        if($('.liste_pronostiqueurs').length == 0)
            $('body').append('<div class="liste_pronostiqueurs"></div>');

        $('.liste_pronostiqueurs').empty().append(html);

        var fenetreListePronostiqueurs = $('.liste_pronostiqueurs');
        $('.liste_pronostiqueurs').dialog( {
            autoOpen: false
            ,maxWidth: 900
            ,width: 'auto'
            ,height: 'auto'
            ,modal: true
            ,title: 'Sélection d\'un pronostiqueur'
            ,position: 'center'
            ,buttons: {
                'Annuler':   function() {
                            fenetreListePronostiqueurs.dialog('close');
                        },
                'Valider':   function() {
                            fenetreListePronostiqueurs.dialog('close');
                            var pronostiqueur = $('#idPronostiqueurSelectionne').val();

                            $.ajax(
                                {
                                    url: 'gerer_cdf_inscription_pronostiqueur_confrontation.php',
                                    type: 'POST',
                                    data:   {   numero_case: numeroCase,
                                                pronostiqueur: pronostiqueur,
                                                exempte: exempte
                                            }
                                }
                            ).done(function(html) {

                            });

                        }
            }
        });
        fenetreListePronostiqueurs.dialog('open');
    });
}

// Match Centre

var mc_ongletActif = 0;


// Match centre - Affichage du Match centre
function matchCentre_afficherMatchCentre() {
	// Si la page n'est pas affichée tout en haut, alors il faut le forcer manuellement
	if ($(window).scrollTop() > 0) {
		$('html, body').animate({scrollTop : 0}, 500);
	}

	// Lecture des dimensions de la fenêtre
	var largeur = $('body').width();


	$.ajax(	{
				url: 'match_centre.php',
				type: 'POST'
			}
	).done(function(html) {
		if($('.mc').length == 0)
			$('body').append('<div class="mc" style="display: none;"></div>');

		$('.mc').empty().append(html);

		var gauche = (largeur - $('.mc').width()) / 2;

		$('.mc').css({ top: '0px' });
		$('.mc').css({ left: gauche + 'px' });

		// Affichage de haut en bas
		$('.mc').slideDown(500);

	});

}

// Match centre - Masquage du Match centre
function matchCentre_masquerMatchCentre() {
	if($('.mc').length != 0) {
		$('.mc').slideUp(500, function() {
			$('.scroll-pane').getNiceScroll().remove();
			$('.mc').remove();
			mc_ongletActif = 0;
		});
	}
}

// Match centre - Affichage d'un championnat
function matchCentre_afficherChampionnat(classe, championnat, pronostiqueurConsulte) {
	$.ajax(	{
				url: 'match_centre_affichage_championnat.php',
				type: 'POST',
				data:	{
							championnat: championnat
						}
			}
	).done(function(html) {
		$('.' + classe).find('.scroll-pane').getNiceScroll().remove();
		$('.' + classe).empty().append(html);

		// On remet à 0 le numéro du match sélectionné (utilisé pour remettre en surbrillance un match sélectionné)
		$('input[name="match_selectionne"]').val(0);

		$('input[name="pronostiqueur_consulte"]').val(pronostiqueurConsulte);

	});
}

// Match centre - Affichage du détail d'un match
function matchCentre_afficherDetailMatch(classe, match) {
	$.ajax(	{
				url: 'match_centre_affichage_match.php',
				type: 'POST',
				data:	{
							match: match
						}
			}
	).done(function(html) {
		$('.' + classe).empty().append(html);

		// Si la zone n'est pas visible, alors la rendre visible
		if($('.' + classe).css('visibility') == 'hidden')
			$('.' + classe).css('visibility', 'visible');

		// Lorsque le joueur clique sur un match, on le met en surbrillance
		// Au prochain rafraîchissement des données, le match en surbrillance perd son statut de match sélectionné
		// C'est la raison pour laquelle on doit sauvegarder quelque part dans la page le numéro de match sélectionné
		// Ainsi, lors du rafraîchissement des données, ce numéro est déjà connu

		$('input[name="match_selectionne"]').val(match);
	});
}

// Match centre - Rafraîchissement de la journée
function matchCentre_rafrichirJournee(journee, pronostiqueurConsulte, date_maj_journee, date_evenement_journee, classe) {
	$.ajax(	{
				url: 'match_centre_rafraichissement_journee.php',
				type: 'POST',
				data:	{
							journee: journee,
							date_maj_journee: date_maj_journee,
							date_evenement_journee: date_evenement_journee
						},
				dataType: 'json'
			}
	).done(function(html) {
		if(html.rafraichir == '1') {
			$('input[name="date_maj_journee"]').val(html.date_maj_journee);
			$('input[name="date_evenement_journee"]').val(html.date_evenement_journee);

			$.ajax(	{
						url: 'match_centre_affichage_journee.php',
						type: 'POST',
						data:	{
									rafraichissementSection: 1,
									journee: journee,
									pronostiqueur_consulte: pronostiqueurConsulte
								}
					}
			).done(function(html) {
				$('.' + classe).html(html);
			});
		}
	});
}

// Match centre - Rafraîchissement de la journée
function matchCentre_afficherPronostiqueur(journee, pronostiqueurConsulte, classeResultats, classeClassementGeneral, classeGraphiques) {
	$('input[name="pronostiqueur_consulte"]').val(pronostiqueurConsulte);

	$.ajax(	{
				url: 'match_centre_affichage_journee.php',
				type: 'POST',
				data:	{
							rafraichissementSection: 1,
							journee: journee,
							pronostiqueur_consulte: pronostiqueurConsulte
						}
			}
	).done(function(html) {
		$('.' + classeResultats).find('.scroll-pane').getNiceScroll().remove();
		$('.' + classeResultats).html(html);

		$.ajax(	{
					url: 'match_centre_affichage_classement_general.php',
					type: 'POST',
					data:	{
								rafraichissementSection: 1,
								journee: journee,
								pronostiqueur_consulte: pronostiqueurConsulte
							}
				}
		).done(function(html) {
			// Effacement de la zone de graphiques
			$('.' + classeGraphiques).empty();

			$('.' + classeClassementGeneral).empty().append(html);
		});
	});
}

// Match centre - Rafraîchissement des classements
function matchCentre_rafrichirClassements(journee, date_maj_journee, classeClassementGeneral, classeClassementJournee, classeGraphiques, classeStatistiques) {
	$.ajax(	{
				url: 'match_centre_rafraichissement_classements.php',
				type: 'POST',
				data:	{
							journee: journee,
							date_maj_journee: date_maj_journee
						},
				dataType: 'json'
			}
	).done(function(html) {
		if(html.rafraichir == '1') {
			// Rafraîchissement du classement général
			$('input[name="date_maj_journee"]').val(html.date_maj_journee);
			var pronostiqueurConsulte = $('input[name="pronostiqueur_consulte"]').val();

			$.ajax(	{
						url: 'match_centre_affichage_classement_general.php',
						type: 'POST',
						data:	{
									rafraichissementSection: 1,
									journee: journee,
									pronostiqueur_consulte: pronostiqueurConsulte
								}
					}
			).done(function(html) {
				// Effacement de la zone de graphiques
				$('.' + classeGraphiques).empty();

				$('.' + classeClassementGeneral).empty().append(html);

				// Rafraîchissement du classement journée
				$.ajax(	{
							url: 'match_centre_affichage_classement_journee.php',
							type: 'POST',
							data:	{
										rafraichissementSection: 1,
										journee: journee
									}
						}
				).done(function(html) {
					$('.' + classeClassementJournee).empty().append(html);
				});
			});
		}
	});

}

// Match centre - Rafraîchissement du détail d'un match
function matchCentre_rafrichirMatch(match, date_maj_match, classe) {
	$.ajax(	{
				url: 'match_centre_rafraichissement_match.php',
				type: 'POST',
				data:	{
							match: match,
							date_maj_match: date_maj_match
						},
				dataType: 'json'
			}
	).done(function(html) {
		if(html.rafraichir == '1') {
			// Rafraîchissement du détail du match
			$('input[name="date_maj_match"]').val(html.date_maj_match);

			$.ajax(	{
						url: 'match_centre_affichage_match.php',
						type: 'POST',
						data:	{
									match: match
								}
					}
			).done(function(html) {
				$('.' + classe).html(html);
			});
		}
	});
}

// Panthéon - Affichage des données d'un pronostiqueur
function pantheon_afficherPronostiqueur(pronostiqueurConsulte, classe) {
	$.ajax(	{
				url: 'pantheon_affichage_pronostiqueur.php',
				type: 'POST',
				data:	{
							pronostiqueur_consulte: pronostiqueurConsulte
						}
			}
	).done(function(html) {
		$('.' + classe).empty().append(html);
	});
}

// Gestion du site - Sauvegarde des données dans les tables archive
function gererSite_sauvegarderDonnees() {
  var saison = $('#txtSaison').val();

  if(saison.trim().length == 0) {
    afficherMessageInformationBandeau('Aucune saison fournie', 2000, '');
    return;
  }

	$.ajax(	{	url: 'gerer_site_sauvegarde_donnees.php',
				type: 'POST',
				data:	{
							saison: saison
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Sauvegarde effectuée', 2000, '');
	});
}

// Gestion du site - Réinitialisation des données
function gererSite_reinitialiserDonnees() {
  if(!confirm('Etes-vous sûr de bien vouloir réinitialiser la saison ?'))
    return;

  if(!confirm('Vraiment sûr ?'))
    return;

	$.ajax(	{	url: 'gerer_site_reinitialisation_donnees.php',
				type: 'POST'
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Réinitialisation effectuée', 2000, '');
	});
}

// Gestion du site - Modification de la date max de saisie des bonus
function gererSite_modifierDateBonus() {
    var date = $('#bonusDateMaxDate').val();
    var heure = $('#bonusDateMaxHeure').val();
    var minute = $('#bonusDateMaxMinute').val();

    $.ajax(	{	url: 'gerer_site_modification_bonus.php',
				type: 'POST',
				data:	{
							date: date,
                            heure: heure,
                            minute: minute
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Modification de la date max de bonus effectuée avec succès', 2000, '');
	}).
  fail(function() {
    console.log('Fonction gererSite_modifierDateBonus : dans le fail');
  });
}

// Gestion du site - Modification de la date max de saisie des qualifications LDC
function gererSite_modifierDateQualificationsLDC() {
    var date = $('#qualificationsLDCDateMaxDate').val();
    var heure = $('#qualificationsLDCDateMaxHeure').val();
    var minute = $('#qualificationsLDCDateMaxMinute').val();

    $.ajax(	{	url: 'gerer_site_modification_qualifications.php',
				type: 'POST',
				data:	{
                            championnat: 2,
							date: date,
                            heure: heure,
                            minute: minute
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Modification de la date max de qualifications LDC effectuée avec succès', 2000, '');
	}).
  fail(function() {
    console.log('Fonction gererSite_modifierDateQualificationsLDC : dans le fail');
  });
}

// Gestion du site - Modification de la date max de saisie des qualifications EL
function gererSite_modifierDateQualificationsEL() {
    var date = $('#qualificationsELDateMaxDate').val();
    var heure = $('#qualificationsELDateMaxHeure').val();
    var minute = $('#qualificationsELDateMaxMinute').val();

    $.ajax(	{	url: 'gerer_site_modification_qualifications.php',
				type: 'POST',
				data:	{
                            championnat: 3,
							date: date,
                            heure: heure,
                            minute: minute
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Modification de la date max de qualifications EL effectuée avec succès', 2000, '');
	}).
  fail(function() {
    console.log('Fonction gererSite_modifierDateQualificationsEL : dans le fail');
  });
}

// Gestiion des pronostiqueurs - Création d'un pronostiqueur
function gererPronostiqueur_creerPronostiqueur() {
  // Lecture des champs saisis
  var nomUtilisateur = $('#txtNomUtilisateur').val();
  var prenom = $('#txtPrenom').val();
  var nomFamille = $('#txtNomFamille').val();
  var motDePasse = $('#txtMotDePasse').val();

  if(nomUtilisateur.trim().length == 0 || prenom.trim().length == 0 || nomFamille.trim().length == 0 || motDePasse.trim().length == 0) {
    window.alert('Un ou plusieurs champs obligatoires non renseignés', '');
    return;
  }

	$.ajax(	{	url: 'gerer_pronostiqueurs_creation_pronostiqueur.php',
				type: 'POST',
				data:	{
							nom_utilisateur: nomUtilisateur,
              prenom: prenom,
              nom_famille: nomFamille,
              mot_de_passe: motDePasse
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Création du pronostiqueur effectuée avec succès', 2000, '');
    // Rechargement de la page
		location.reload();
	}).
  fail(function() {
    console.log('Fonction gererPronostiqueur_creerPronostiqueur : dans le fail');
  });
}

// Gestion des pronostiqueurs - Vérification de l'existence d'un pronostiqueur durant le processus de création
function gererPronostiqueurs_verifierExistence() {
  var nomUtilisateur = $('#txtNomUtilisateur').val();

  // Recherche de l'existence de ce nom d'utilisateur
	$.ajax(	{	url: 'gerer_pronostiqueurs_verification_existence.php',
				type: 'POST',
				data:	{
							nom_utilisateur: nomUtilisateur
						},
        dataType: 'json'
			}
	).done(function(html) {
    if(html.existeDeja == 1)
      alert('Attention, ce nom d\'utilisateur existe déjà ! Veuillez en choisir un autre');
	}).
  fail(function() {
    console.log('Fonction gererPronostiqueurs_verifierExistence : dans le fail');
  });
}

// Gestion des pronostiqueurs - Inscription / désinscription à un championnat
function gererPronostiqueur_modifierInscription(elt, pronostiqueur, championnat) {
	var action = -1;
	if(elt.prop('checked'))
		action = 1;
	else if(!elt.prop('checked'))
		action = 0;

	$.ajax(	{	url: 'gerer_pronostiqueurs_maj_inscription.php',
				type: 'POST',
				data:	{
							pronostiqueur: pronostiqueur,
							championnat: championnat,
							action: action
						}
			}
	).done(function(html) {
		if(action == 1)
			afficherMessageInformationBandeau('Inscription effectuée', 1000, '');
		else if(action == 0)
			afficherMessageInformationBandeau('Désinscription effectuée', 1000, '');
	});
}

// Gestion des pronostiqueurs - Suppression d'un pronostiqueur
function gererPronostiqueur_effacerPronostiqueur(pronostiqueur, deplacement) {
  if(!confirm('Etes-vous sûr de bien vouloir effacer ce pronostiqueur ?'))
		return;

	$.ajax( {
				url: 'gerer_pronostiqueurs_effacement.php',
				type: 'POST',
				data:   {
							pronostiqueur: pronostiqueur,
              deplacement: deplacement
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Effacement du pronostiqueur effectué', 2000, '');
    // Rechargement de la page
		location.reload();
	});
}

// Gestion des pronostiqueurs - Réinscription d'un ancien pronostiqueur
function gererPronostiqueurs_reinscrirePronostiqueur(nomUtilisateur) {
	$.ajax(	{	url: 'gerer_pronostiqueurs_creation_pronostiqueur.php',
				type: 'POST',
				data:	{
							nom_utilisateur: nomUtilisateur,
              prenom: '',
              nom_famille: '',
              mot_de_passe: ''
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Réinscription du pronostiqueur effectuée avec succès', 2000, '');
    // Rechargement de la page
		location.reload();
	}).
  fail(function() {
    console.log('Fonction gererPronostiqueurs_reinscrirePronostiqueur : dans le fail');
  });
}

// Gestion des équipes - Engagement / désengagement à un championnat
function gererEquipe_modifierEngagement(elt, equipe, championnat, l1Europe) {
	var action = -1;
	if(elt.prop('checked'))
		action = 1;
	else if(!elt.prop('checked'))
		action = 0;

	$.ajax(	{	url: 'gerer_equipes_maj_engagement.php',
				type: 'POST',
				data:	{
							equipe: equipe,
							championnat: championnat,
							l1_europe: l1Europe,
							action: action
						}
			}
	).done(function(html) {
		if(action == 1)
			afficherMessageInformationBandeau('Engagement effectué', 1000, '');
		else if(action == 0)
			afficherMessageInformationBandeau('Désengagement effectué', 1000, '');
	});
}

// Gestion des équipes - Modification des données de l'équipe (nom, nom court, fanion)
function gererEquipe_modifierEquipe(elt, equipe, champ) {
	var valeur = elt.val();

	$.ajax(	{	url: 'gerer_equipes_maj_equipe.php',
				type: 'POST',
				data:	{
							equipe: equipe,
							valeur: valeur,
							champ: champ
						}
			}
	).done(function(html) {
		afficherMessageInformationBandeau('Modification effectuée', 1000, '');
	});
}

// Gestion des équipes - Recherche d'une équipe
function gererEquipe_rechercherEquipe(nom, elt) {
	$.ajax(	{	url: 'gerer_equipes_liste_equipes.php',
				type: 'POST',
				data:	{
							nom_equipe: nom,
							rafraichissement: 1
						}
			}
	).done(function(html) {
		$('#' + elt).empty().append(html);
	});
}

// Gestion des équipes - Création d'une nouvelle équipe
function gererEquipe_creationEquipe() {
	$.ajax( {
				url: 'gerer_equipes_creation_equipe.php',
				type: 'POST'
			}
    ).done(function(html) {
		if($('.creation-equipe').length == 0)
			$('body').append('<div class="creation-equipe"></div>');

		$('.creation-equipe').empty().append(html);
		$('.creation-equipe').dialog({
			autoOpen: false
			,width: 'auto'
			,height: 'auto'
			,modal: true
			,title: 'Création d\'équipe'
			,position: 'center'
			,buttons: {
				'Valider':  function() {
								$(this).dialog('close');

								// Lecture des champs saisis
								var nom = $('#txtEquipeNom').val();
								var nomCourt = $('#txtEquipeNomCourt').val();
								var fanion = $('#txtFanion').val();
								var l1 = $('#cbL1').prop('checked') == true ? 1 : 0;
								var l1Europe = $('#cbL1Europe').prop('checked') == true ? 1 : 0;
								var ldc = $('#cbLDC').prop('checked') == true ? 1 : 0;
								var el = $('#cbEL').prop('checked') == true ? 1 : 0;
								var barrages = $('#cbBarrages').prop('checked') == true ? 1 : 0;
								var cdf = $('#cbCDF').prop('checked') == true ? 1 : 0;


								$.ajax( {
											url: 'gerer_equipes_creation_equipe_bdd.php',
											type: 'POST',
											data:	{
														nom: nom,
														nom_court: nomCourt,
														fanion: fanion,
														l1: l1,
														l1_europe: l1Europe,
														ldc: ldc,
														el: el,
														barrages: barrages,
														cdf: cdf
													}
										}
								).done(function(html) {
                                    // Rechargement de la page
									location.reload();

								});
							},
				'Annuler':  function() {
								$(this).dialog('close');
							}
			}
		});
		$('.creation-equipe').dialog('open');
	});
}

// Gestion des équipes - Chargement du fanion
function gererEquipe_chargerFanion(fichier, numeroEquipe, champFanion) {
    if(fichier.length == 0)
        return;

    var nomFichier = fichier[0].name;
    var donnees = new FormData();
    donnees.append('fichier', fichier[0]);
    donnees.append('equipe', numeroEquipe);

    $.ajax(	{
                url: 'gerer_equipes_chargement_fanion.php'
                ,type: 'POST'
                ,processData: false
                ,contentType: false
                ,cache: false
                ,mimeType: 'multipart/form-data'
                ,data: donnees
                ,dataType: 'json'
            }

    ).done(function(html) {
        if(html.reussite != 1) {
            alert('Une erreur s\'est produite pendant le transfert du fichier !');
            return;
        }

        afficherMessageInformationBandeau('Fanion transféré avec succès', 2000, '');

        // Mise à jour du champ
        $('#' + champFanion).val(nomFichier);
    });
}

// Gestion des barèmes de bonus équipes - Modification d'un barème
function gererBaremeBonusEquipes_modifierBonus(elt, equipe, table) {
    // Le paramètre table indique quel est la table à modifier en base de données
    var bonus = $(elt).val();

    $.ajax( {
                url: 'gerer_bareme_bonus_equipes_modification_bonus.php'
                ,type: 'POST'
                ,data:  {
                            bonus: bonus
                            ,equipe: equipe
                            ,table: table
                        }
            }
    ).done(function(html) {
        afficherMessageInformationBandeau('Barème bonus modifié avec succès', 2000, '');
    });
}

// Gestion des barèmes de bonus équipes - Modification d'un bonus anticipé équipe championne / podium / relégation
function gererBaremeBonusEquipes_modifierBonusAnticipe(elt, table) {
    // Le paramètre table indique quel est la table à modifier en base de données
    var equipe = $(elt).val();
    var action = elt.checked ? 1 : 0;

    $.ajax( {
                url: 'gerer_bareme_bonus_equipes_modification_bonus_anticipe.php'
                ,type: 'POST'
                ,data:  {
                            equipe: equipe
                            ,table: table
                            ,action: action
                        }
            }
    ).done(function(html) {
        afficherMessageInformationBandeau('Bonus anticipé modifié avec succès', 2000, '');
    });
}

// Gestion des barèmes de bonus buteurs - Modification d'un barème
function gererBaremeBonusButeurs_modifierBonus(elt, joueur) {
    var bonus = $(elt).val();

    $.ajax( {
                url: 'gerer_bareme_bonus_buteurs_modification_bonus.php'
                ,type: 'POST'
                ,data:  {
                            bonus: bonus
                            ,joueur: joueur
                        }
            }
    ).done(function(html) {
        afficherMessageInformationBandeau('Barème bonus modifié avec succès', 2000, '');
    });
}

// Gestion des barèmes de bonus buteurs - Suppression d'un joueur
function gererBaremeBonusButeurs_supprimerJoueur(joueur) {
    if(!confirm('Etes-vous sûr de vouloir supprimer ce buteur de la liste des meilleurs buteurs ?'))
        return;

    $.ajax( {
                url: 'gerer_bareme_bonus_buteurs_suppression_joueur.php'
                ,type: 'POST'
                ,data:  {
                            joueur: joueur
                        }
            }
    ).done(function(html) {
        // Rechargement de la page
        location.reload();
    });
}

// Gestion des barèmes de bonus buteurs - Ajout d'un joueur
function gererBaremeBonusButeurs_ajouterJoueur(joueur) {
    var bonus = prompt('Bonus meilleur buteur');

    $.ajax( {
                url: 'gerer_bareme_bonus_buteurs_ajout_joueur.php'
                ,type: 'POST'
                ,data:  {
                            joueur: joueur
                            ,bonus: bonus
                        }
            }
    ).done(function(html) {
        // Rechargement de la page
        location.reload();
    });
}

// Gestion des meilleurs passeurs - Suppression d'un joueur
function gererMeilleursPasseurs_supprimerJoueur(joueur) {
    $.ajax( {
                url: 'gerer_meilleurs_passeurs_suppression_joueur.php'
                ,type: 'POST'
                ,data:  {
                            joueur: joueur
                        }
            }
    ).done(function(html) {
        // Rechargement de la page
        location.reload();
    });
}

// Gestion des meilleurs passeurs - Ajout d'un joueur
function gererMeilleursPasseurs_ajouterJoueur(joueur) {
    $.ajax( {
                url: 'gerer_meilleurs_passeurs_ajout_joueur.php'
                ,type: 'POST'
                ,data:  {
                            joueur: joueur
                            ,bonus: 100
                        }
            }
    ).done(function(html) {
        // Rechargement de la page
        location.reload();
    });
}
