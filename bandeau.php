<?php

    // Lecture des options de menu pour savoir s'il faut les afficher ou non
    $ordreSQL =     '   SELECT      (SELECT Menus_Visible FROM menus WHERE Menu = 100) AS Menu100' .
                    '               ,(SELECT Menus_Visible FROM menus WHERE Menu = 110) AS Menu110' .
                    '               ,(SELECT Menus_Visible FROM menus WHERE Menu = 120) AS Menu120' .
                    '               ,(SELECT Menus_Visible FROM menus WHERE Menu = 130) AS Menu130' .
                    '               ,(SELECT Menus_Visible FROM menus WHERE Menu = 140) AS Menu140' .
                    '               ,(SELECT Menus_Visible FROM menus WHERE Menu = 150) AS Menu150';
    $req = $bdd->query($ordreSQL);
    $menus = $req->fetchAll();
    $menu100 = $menus[0]["Menu100"];
    $menu110 = $menus[0]["Menu110"];
    $menu120 = $menus[0]["Menu120"];
    $menu130 = $menus[0]["Menu130"];
    $menu140 = $menus[0]["Menu140"];
    $menu150 = $menus[0]["Menu150"];

    echo '<div class="bandeau">';
        echo '<div class="largeur-menu">';
            echo '<div class="colle-gauche gauche">';
                echo '<img src="images/poulpe.png" alt="" onclick="window.open(\'accueil.php\', \'_self\');" />';
            echo '</div>';
            echo '<div>';
                echo '<ul class="menu">';
                    echo '</li>';
                    if($nomPage == 'accueil.php') {
                        echo '<li class="menu--lien" id="liModules"><span class="modules-concours"></span><label class="menu-complementaire-lien">Widgets</label>';
                            echo '<div class="sous-menu-flottant menu-modules" style="background-image: url(\'images/fond_menu_widgets.png\'); background-repeat: no-repeat; background-position: 50% 0;"></div>';
                        echo '</li>';
                    }

                    echo '<li class="menu--lien" id="liTchat"><span class="bulle-tchat"></span><label class="menu-complementaire-lien">Tchat</label>';
                        echo '<div class="sous-menu-flottant" style="background-image: url(\'images/fond_menu_tchat.png\'); background-repeat: no-repeat; background-position: 50% 0;"></div>';
                    echo '</li>';

                    echo '<li class="menu--lien" id="liTchatGroupe"><span class="bulles-tchat"></span><label class="menu-complementaire-lien">Tchat de groupe</label>';
                        echo '<div class="sous-menu-flottant"></div>';
                    echo '</li>';


                    echo '<li class="menu--lien"><span class="profil"><img src="images/pronostiqueurs/' . $_SESSION["photo_pronostiqueur"] . '" alt=""/></span><label class="menu-complementaire-lien">' . $_SESSION["nom_pronostiqueur"] . '</label>';
                        echo '<div class="sous-menu-flottant" style="background-image: url(\'images/fond_menu_profil.png\'); background-repeat: no-repeat; background-position: 50% 0;">';
                            echo '<div>';
                                echo '<div class="groupe-menu colle-gauche gauche">';
                                    echo '<label class="titre">Thèmes</label>';
                                    // Lecture des thèmes du site et du thème actuellement sélectionné par l'utilisateur
                                    $ordreSQL =     '   SELECT      CASE' .
                                                    '                   WHEN    Theme = pronostiqueurs.Themes_Theme' .
                                                    '                   THEN    0' .
                                                    '                   ELSE    1' .
                                                    '               END AS Ordre' .
                                                    '               ,Theme, Themes_Nom, Themes_Chemin, pronostiqueurs.Themes_Theme AS Pronostiqueurs_Theme' .
                                                    '   FROM        themes' .
                                                    '   LEFT JOIN   (' .
                                                    '                   SELECT      Pronostiqueur, Themes_Theme' .
                                                    '                   FROM        pronostiqueurs' .
                                                    '                   WHERE       pronostiqueurs.Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
                                                    '               ) pronostiqueurs' .
                                                    '               ON      Theme = Themes_Theme' .
                                                    '   WHERE       Themes_Actif = 1' .
                                                    '   ORDER BY    Ordre, Theme';
                                    $req = $bdd->query($ordreSQL);
                                    $themes = $req->fetchAll();

                                    foreach($themes as $unTheme) {
                                        if($unTheme["Theme"] == $unTheme["Pronostiqueurs_Theme"])
                                            echo '<label style="text-decoration: underline;"><img src="' . $unTheme["Themes_Chemin"] . '" alt="" style="margin-right: 1em;" />' . $unTheme["Themes_Nom"] . '</label>';
                                        else
                                            echo '<label class="lien" onclick="changerTheme(' . $unTheme["Theme"] . ');"><img src="' . $unTheme["Themes_Chemin"] . '" alt="" style="margin-right: 1em;" />' . $unTheme["Themes_Nom"] . '</label>';
                                    }
                                echo '</div>';

                                echo '<div class="gauche">';
                                    echo '<label class="titre">Profil</label>';
                                    echo '<label class="lien" onclick="window.open(\'creer_fiche.php\', \'_self\');" title="Modification de la fiche d\'identité"><span>Fiche d\'identité</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'modifier_mot_de_passe.php\', \'_self\');" title="Modification du mot de passe"><span>Modification du mot de passe</span></label>';

                                    echo '<label class="titre espacement-haut">Déconnexion</label>';
                                    echo '<label class="lien" onclick="window.open(\'deconnexion.php\', \'_self\');" title="Déconnexion du site"><span>Déconnexion</span></label>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</li>';
                    echo '<li class="menu--lien" id="horloge">';
                    echo '</li>';

                echo '</ul>';
            echo '</div>';
            echo '<div class="colle-gauche gauche">';
                echo '<ul class="menu">';


                    if($administrateur == 1) {
                        // Recherche des journées en cours des 4 championnats et coupes
                        $ordreSQL =     '   SELECT      fn_recherchejourneeencours(1) AS Journee_L1' .
                                        '               ,fn_recherchejourneeencours(2) AS Journee_LDC' .
                                        '               ,fn_recherchejourneeencours(3) AS Journee_EL' .
                                        '               ,fn_recherchejourneeencours(4) AS Journee_Barr' .
                                        '               ,fn_recherchejourneeencours(5) AS Journee_CDF';

                        $req = $bdd->query($ordreSQL);
                        $journeesActives = $req->fetchAll();
                        $journeeL1 = $journeesActives[0]["Journee_L1"];
                        $journeeLDC = $journeesActives[0]["Journee_LDC"];
                        $journeeEL = $journeesActives[0]["Journee_EL"];
                        $journeeBarr = $journeesActives[0]["Journee_Barr"];
                        $journeeCDF = $journeesActives[0]["Journee_CDF"];

                        echo '<li class="menu--lien" id="menu-administration">Administration';
                            echo '<div class="sous-menu">';
                                echo '<div class="conteneur-sous-menu">';
                                    echo '<div class="groupe-menu colle-gauche gauche">';
                                        echo '<label class="titre">Gestion des matches</label>';
                                        echo '<label class="lien" title="Gestion des matches"><a href="creer_match.php?journee=' . $journeeL1 . '">L1</a> - <a href="creer_match.php?journee=' . $journeeBarr . '">Barr</a> - <a href="creer_match.php?journee=' . $journeeLDC . '">LDC</a> - <a href="creer_match.php?journee=' . $journeeEL . '">EL</a> - <a href="creer_match.php?journee=' . $journeeCDF . '">CDF</a></label>';

                                        echo '<label class="titre espacement-haut">Surveillance du direct</label>';
                                        echo '<label class="lien" onclick="window.open(\'creer_match_surveillance_direct.php\', \'_blank\');" title="Surveillance des compositions et du direct"><span>Surveillance sur Match en Direct</span></label>';
                                        echo '<label class="titre espacement-haut">Classements neutres</label>';
                                        echo '<label class="lien" onclick="window.open(\'classements_pronostiqueurs.php?neutre=1\', \'_self\');" title="Affichage neutre de la page des classements"><span>Classements neutres</span></label>';
                                        echo '<label class="titre espacement-haut">Gestion des effectifs</label>';
                                        echo '<label class="lien" onclick="window.open(\'gerer_effectif.php\', \'_self\');" title="Gestion de l\'effectif des équipes"><span>Gestion de l\'effectif</span></label>';
                                        echo '<label class="lien" onclick="window.open(\'gerer_effectif_creation_multiple.php\', \'_self\');" title="Création multiple de joueurs"><span>Création multiple</span></label>';
                                        echo '<label class="titre espacement-haut">Gestion des équipes</label>';
                                        echo '<label class="lien" onclick="window.open(\'gerer_equipes.php\', \'_self\');" title="Gestion des équipes"><span>Gestion des équipes</span></label>';

                                        echo '<label class="titre espacement-haut">Coupe de France</label>';
                                        echo '<label class="lien" onclick="window.open(\'gerer_cdf.php\', \'_self\');" title="Gérer la Coupe de France"><span>Gestion de la Coupe</span></label>';
                                        echo '<label class="lien" onclick="window.open(\'cdf_prec.php?saison=2019\', \'_self\');" title="Coupe de France 2019"><span>Coupe de France 2019</span></label>';

                                        echo '<label class="titre espacement-haut">Historique</label>';
                                        echo '<label class="lien" onclick="window.open(\'poulpe/index.php\', \'_blank\');" title="Historique"><span>Historique</span></label>';

                                        echo '<label class="titre espacement-haut">Ligue 1</label>';
                                        echo '<label class="lien" onclick="ligue1_initialiserJ1();" title="Initialiser classements J1"><span>Initialiser classements J1</span></label>';

                                    echo '</div>';
                                    echo '<div class="groupe-menu gauche">';
                                        echo '<label class="titre">Poules Coupes d\'Europe</label>';
                                        echo '<label class="lien" title="Gestion des poules">Poules <span onclick="window.open(\'gerer_poules.php?championnat=2\', \'_self\');">LDC</span> - <span onclick="window.open(\'gerer_poules.php?championnat=3\', \'_self\');">EL</span></label>';
                                        echo '<label class="lien" title="Gestion des qualifications">Qualifications <span onclick="window.open(\'gerer_qualification.php?championnat=2\', \'_self\');">LDC</span> - <span onclick="window.open(\'gerer_qualification.php?championnat=3\', \'_self\');">EL</span></label>';

                                        echo '<label class="titre espacement-haut">Divers</label>';

                                        if($menu100 == 1)               echo '<label class="lien" onclick="menu_basculerAffichage(100);" title="Masquer page de consultation des bonus"><span>Masquer "Consultation de bonus"</span></label>';
                                        else                            echo '<label class="lien" onclick="menu_basculerAffichage(100);" title="Afficher page de consultation des bonus"><span>Afficher "Consultation de bonus"</span></label>';

                                        if($menu110 == 1)               echo '<label class="lien" onclick="menu_basculerAffichage(110);" title="Masquer page de consultation des qualifications"><span>Masquer "Consulter les qualifications"</span></label>';
                                        else                            echo '<label class="lien" onclick="menu_basculerAffichage(110);" title="Afficher page de consultation des qualifications"><span>Afficher "Consulter les qualifications"</span></label>';

                                        if($menu120 == 1)               echo '<label class="lien" onclick="menu_basculerAffichage(120);" title="Masquer page de création des bonus"><span>Masquer "Création de bonus"</span></label>';
                                        else                            echo '<label class="lien" onclick="menu_basculerAffichage(120);" title="Afficher page de création des bonus"><span>Afficher "Création de bonus"</span></label>';

                                        if($menu130 == 1)               echo '<label class="lien" onclick="menu_basculerAffichage(130);" title="Masquer page de saisie des qualifications"><span>Masquer "Création des qualifications"</span></label>';
                                        else                            echo '<label class="lien" onclick="menu_basculerAffichage(130);" title="Afficher page de saisie des qualifications"><span>Afficher "Création des qualifications"</span></label>';

                                        if($menu140 == 1)               echo '<label class="lien" onclick="menu_basculerAffichage(140);" title="Masquer page de barème des bonus"><span>Masquer "Consultation des barèmes de bonus"</span></label>';
                                        else                            echo '<label class="lien" onclick="menu_basculerAffichage(140);" title="Afficher page de saisie des qualifications"><span>Afficher "Consultation des barèmes de bonus"</span></label>';

                                        if($menu150 == 1)               echo '<label class="lien" onclick="menu_basculerAffichage(150);" title="Masquer page de Coupe de France"><span>Masquer "Coupe de France"</label>';
                                        else                            echo '<label class="lien" onclick="menu_basculerAffichage(150);" title="Afficher page de Coupe de France"><span>Afficher "Coupe de France"</label>';

                                        echo '<label class="titre espacement-haut">Sauvegarde et pronostiqueurs</label>';
                                        echo '<label class="lien" onclick="window.open(\'gerer_site.php\', \'_self\');" title="Sauvegarde et gestion des données"><span>Sauvegarde et gestion des données</span></label>';
                                        echo '<label class="lien" onclick="window.open(\'gerer_pronostiqueurs.php\', \'_self\');" title="Gestion des pronostiqueurs"><span>Gestion des pronostiqueurs</span></label>';

                                        echo '<label class="titre espacement-haut">Bonus</label>';
                                        echo '<label class="lien" title="Barèmes">Barème <span onclick="window.open(\'gerer_bareme_bonus_equipes.php\', \'_self\');">équipe</span> - <span onclick="window.open(\'gerer_bareme_bonus_buteurs.php\', \'_self\');">buteur</span> - <span onclick="window.open(\'gerer_meilleurs_passeurs.php\', \'_self\');">passeur</span></label>';

                                        echo '<label class="titre espacement-haut">Règlements</label>';
                                        echo '<label class="lien" title="Règlements">Règlement <span onclick="window.open(\'reglement_edition.php\', \'_self\');">général</span> - <span onclick="window.open(\'reglement_ldc_edition.php\', \'_self\');">LDC</span> - <span onclick="window.open(\'reglement_el_edition.php\', \'_self\');">EL</span> - <span onclick="window.open(\'reglement_cdf_edition.php\', \'_self\');">CDF</span></label>';

                                        echo '<label class="titre espacement-haut">Compte-rendu</label>';
                                        echo '<label class="lien" onclick="window.open(\'creer_compte_rendu.php\', \'_self\');" title="Modèle du compte-rendu"><span>Modèle du compte-rendu</span></label>';

                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</li>';
                    }

                    echo '<li class="menu--lien" onclick="window.open(\'accueil.php\', \'_self\');">Accueil</li>';

                    echo '<li class="menu--lien">Pronostics+';
                        //echo '<div class="sous-menu" style="background-image: url(\'images/fond_menu_pronostics.png\'); background-repeat: no-repeat; background-position: 50% 50%;">';
                        echo '<div class="sous-menu">';
                            echo '<div class="conteneur-sous-menu">';
                                echo '<div class="groupe-menu colle-gauche gauche">';
                                    echo '<label class="titre">Pronostics</label>';
                                    echo '<label class="lien" onclick="window.open(\'creer_prono.php\', \'_self\');" title="Saisir les pronostics de la journée ou des journées en cours"><span>Journée(s) en cours</span></label>';

                                    if($pronostiqueur == 7) {
                                        echo '<label class="lien" onclick="window.open(\'http://lepoulpedor.com/parieurs/#/home?authId=' . $_SESSION["auth_pronostiqueur"] . '\', \'_blank\');" title="Parieurs (beta)"><span>Parieurs (beta)</span></label>';
                                    }

                                    if($menu100 == 1)
                                        echo '<label class="lien" onclick="window.open(\'consulter_bonus.php\', \'_self\');" title="Consulter les bonus de la Ligue 1 (meilleur buteur et passeur, équipes sur le podium...)"><span>Consulter les bonus</span></label>';

                                    if($menu140 == 1)
                                        echo '<label class="lien" onclick="window.open(\'consulter_bareme_bonus.php\', \'_self\');" title="Consulter les barèmes de bonus"><span>Consulter les barèmes de bonus</span></label>';

                                    if($menu110 == 1)
                                        echo '<label class="lien" onclick="window.open(\'consulter_qualification.php\', \'_self\');" title="Consulter les pronostics des classements de poule de votre championnat européen"><span>Consulter les classements de poule</span></label>';

                                    if($menu120 == 1)
                                        echo '<label class="lien" onclick="window.open(\'creer_bonus.php\', \'_self\');" title="Saisir les bonus de la Ligue 1 (meilleur buteur et passeur, équipes sur le podium...)"><span>Saisir les bonus de Ligue 1</span></label>';

                                    if($menu130 == 1)
                                        echo '<label class="lien" onclick="window.open(\'creer_qualification.php\', \'_self\');" title="Saisir les qualifications des poules européennes"><span>Saisir les qualifications des poules européennes</span></label>';

                                    echo '<label class="titre espacement-haut">Trophées</label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_trophees.php?championnat=1\', \'_self\');" title="Consulter les trophées de Ligue 1"><span>Ligue 1</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_trophees.php?championnat=2\', \'_self\');" title="Consulter les trophées de Ligue des Champions"><span>Ligue des Champions</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_trophees.php?championnat=3\', \'_self\');" title="Consulter les trophées d\'Europa League"><span>Europa League</span></label>';
                                    //echo '<label class="lien" onclick="window.open(\'consulter_trophees.php?championnat=4\', \'_self\');" title="Consulter les résultats des barrages LDC"><span>Barrages LDC</span></label>';

                                echo '</div>';

                                echo '<div class="gauche">';
                                    echo '<label class="titre">Résultats</label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_resultats.php?championnat=1\', \'_self\');" title="Résultats de la Ligue 1"><span>Ligue 1</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_resultats.php?championnat=2\', \'_self\');" title="Résultats de la Ligue des Champions"><span>Ligue des Champions</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_resultats.php?championnat=3\', \'_self\');" title="Résultats de l\'Europa League"><span>Europa League</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_resultats.php?championnat=4\', \'_self\');" title="Résultats des barrages de la LDC"><span>Barrages LDC</span></label>';

                                    echo '<label class="titre espacement-haut">Concours 2023-2024</label>';
                                    echo '<label class="lien" onclick="window.open(\'consulter_fiches.php\', \'_self\');" title="Joueurs du concours"><span>Les joueurs</span></label>';
                                    echo '<label class="lien" onclick="window.open(\'reglement.php\', \'_self\');" title="Règlement 2022-2023"><span>Le règlement</span></label>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</li>';

                    echo '<li class="menu--lien" onclick="window.open(\'classements_pronostiqueurs.php\', \'_self\');">Classements</li>';

                    echo '<li class="menu--lien" onclick="concoursCentre_afficherConcoursCentre();">Contest Centre</li>';

                    //echo '<li class="menu--lien" onclick="matchCentre_afficherMatchCentre();">Match Centre</li>';

                    if($menu150 == 1)
                        echo '<li class="menu--lien" onclick="window.open(\'cdf.php\', \'_self\');">Coupe de France</li>';
                echo '</ul>';
            echo '</div>';
        echo '</div>';
    echo '</div>';

    include_once('modules.php');
?>

<script>
    $('.menu > li, .menu-modules').click(function(e) {
        // Un cas particulier pour le menu modules qui ne se ferme pas lorsque l'on clique dessus
        if($(this).hasClass('menu-modules')) {
        }
        else {
            if($(this).hasClass('selectionne')) {
                $('.menu .selectionne .sous-menu').css('display', 'none');
                $('.menu .selectionne .sous-menu-flottant').css('display', 'none');
                $('.menu .selectionne').removeClass('selectionne');
            }
            else {
                $('.menu .selectionne .sous-menu').css('display', 'none');
                $('.menu .selectionne .sous-menu-flottant').css('display', 'none');

                $('.menu .selectionne').removeClass('selectionne');

                if($(this).find('.sous-menu').length) {
                    $(this).addClass('selectionne');
                    $(this).find('.sous-menu').css('display', 'block');
                }

                if($(this).find('.sous-menu-flottant').length) {
                    $(this).addClass('selectionne');
                    $(this).find('.sous-menu-flottant').css('display', 'block');
                }

                if($(this).attr('id') != null) {
                    var lienClique = $(this);
                    $(lienClique).addClass('selectionne');

                    var page = '';
                    var nomMenu = '';
                    // Action spécifique pour le menu tchat
                    switch(lienClique.attr('id')) {
                        case 'liModules':
                            page = 'bandeau_menu_modules.php';
                        break;
                        case 'liTchat':
                            page = 'bandeau_menu_tchat.php';
                            nomMenu = 'liTchat';
                        break;
                        case 'liTchatGroupe':
                            page = 'bandeau_menu_tchat_groupe.php';
                            nomMenu = 'liTchatGroupe';
                        break;
                        case 'liParametres':
                            page = 'bandeau_menu_parametres.php';
                            nomMenu = 'liParametres';
                        break;
                    }

                    // Rechargement du sous-menu de tchat avant affichage
                    $.ajax({
                                url: page,
                                type: 'POST',
                                nomMenu: nomMenu
                            }
                    ).done(function(html) {
                        $(lienClique).find('.sous-menu-flottant').empty().append(html);
                        $(lienClique).find('.sous-menu-flottant').css('display', 'block');

                    });
                }
                else {
                    $(this).addClass('selectionne');
                    $(this).find('.sous-menu').css('display', 'block');
                }
            }
        }
        e.stopPropagation();
    });

    $('body').click(function() {

        $('.menu .selectionne .sous-menu').css('display', 'none');
        $('.menu .selectionne .sous-menu-flottant').css('display', 'none');
        $('.menu .selectionne').removeClass('selectionne');

    });


    function afficherHeure() {
        var heureActuelle = new Date();
        var heures = heureActuelle.getHours();
        var minutes = heureActuelle.getMinutes();
        var secondes = heureActuelle.getSeconds();

        // Ajout d'un 0 si les minutes et secondes sont inférieures à 10
        minutes = (minutes < 10 ? '0' : '') + minutes;
        secondes = (secondes < 10 ? '0' : '') + secondes;
        heures = (heures < 10 ? '0' : '') + heures;

        // Fabrique la chaîne à afficher
        var heureAffichee = heures + ':' + minutes + ':' + secondes;

        $('#horloge').empty().append(heureAffichee);
    }

    $(function() {
       setInterval('afficherHeure()', 1000);
    });

</script>
