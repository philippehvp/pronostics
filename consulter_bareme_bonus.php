<?php
	include_once('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>

	<script type="text/javascript" src="js/datatables/jquery.dataTables.js"></script>
	<script type="text/javascript" src="js/datatables/extensions/dataTables.fixedColumns.min.js"></script>
</head>

<body>
	<?php
		$nomPage = 'consulter_bareme_bonus.php';
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';

		// Consultation du barème des bonus
        echo '<div class="conteneur">';
			include_once('bandeau.php');
			echo '<div id="divBaremeBonus" class="contenu-page">';
				echo '<ul class="ulNavigation"></ul>';

                echo '<div id="divBaremeBonus_1" class="bareme" title="Barème des équipes">';
                    // Lecture des équipes et des barèmes
                    $ordreSQL =		'	SELECT      equipes.Equipe, IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom) AS Equipes_NomCourt, equipes.Equipes_Fanion' .
                                    '               ,IFNULL(bonus_equipe_championne.Bonus_Points, 0) AS Bonus_Championne' .
                                    '               ,IFNULL(bonus_equipes_podium.Bonus_Points, 0) AS Bonus_Podium' .
                                    '               ,IFNULL(bonus_equipes_relegation.Bonus_Points, 0) AS Bonus_Relegation' .
                                    '   FROM        equipes' .
                                    '   JOIN        engagements' .
                                    '               ON      equipes.Equipe = engagements.Equipes_Equipe' .
                                    '   LEFT JOIN   bonus_equipe_championne' .
                                    '               ON      equipes.Equipe = bonus_equipe_championne.Equipes_Equipe' .
                                    '   LEFT JOIN   bonus_equipes_podium' .
                                    '               ON      equipes.Equipe = bonus_equipes_podium.Equipes_Equipe' .
                                    '   LEFT JOIN   bonus_equipes_relegation' .
                                    '               ON      equipes.Equipe = bonus_equipes_relegation.Equipes_Equipe' .
                                    '   WHERE       engagements.Championnats_Championnat = 1' .
                                    '               AND     equipes.Equipes_L1Europe IS NULL' .
                                    '   ORDER BY    IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom)';

                    $req = $bdd->query($ordreSQL);
                    $baremesBonus = $req->fetchAll();
                    $nombreEquipes = count($baremesBonus);

                    // Tableau des équipes
                    echo '<table class="tableau--bonus">';
                        echo '<thead>';
                            echo '<tr>';
                                echo '<th>&nbsp;</th>';
                                echo '<th>Equipes</th>';
                                echo '<th>Championnes de Ligue 1</th>';
                                echo '<th>Sur le podium</th>';
                                echo '<th>Reléguées en L2</th>';
                            echo '</tr>';
                        echo '</thead>';

                        echo '<tbody>';
                            for($i = 0; $i < $nombreEquipes; $i++) {
                                echo '<tr>';
                                    echo '<td><img src="images/equipes/' . $baremesBonus[$i]["Equipes_Fanion"] . '" alt="Fanion" class="fanion" />';
                                    echo '<td>' . $baremesBonus[$i]["Equipes_NomCourt"]. '</td>';
                                    echo '<td>' . $baremesBonus[$i]["Bonus_Championne"]. '</td>';
                                    echo '<td>' . $baremesBonus[$i]["Bonus_Podium"]. '</td>';
                                    echo '<td>' . $baremesBonus[$i]["Bonus_Relegation"]. '</td>';
                                echo '</tr>';
                            }
                        echo '</tbody>';
                    echo '</table>';
                echo '</div>';

                echo '<div id="divBaremeBonus_2" class="bareme" title="Barème du meilleur buteur">';
                    // Lecture des joueurs et des bonus

                    $ordreSQL =		'	SELECT      joueurs.Joueur, IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) AS Joueurs_NomFamille' .
                                    '               ,bonus_meilleur_buteur.Bonus_Points' .
                                    '   FROM        joueurs' .
                                    '   JOIN        bonus_meilleur_buteur' .
                                    '               ON      joueurs.Joueur = bonus_meilleur_buteur.Joueurs_Joueur' .
                                    '   ORDER BY    IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille)';

                    $req = $bdd->query($ordreSQL);
                    $baremesButeurs = $req->fetchAll();
                    $nombreJoueurs = count($baremesButeurs);


                    // Tableau des buteurs
                    echo '<table class="tableau--bonus">';
                        echo '<thead>';
                            echo '<tr>';
                                echo '<th>Joueurs</th>';
                                echo '<th>Bonus meilleur buteur</th>';
                            echo '</tr>';
                        echo '</thead>';

                        echo '<tbody>';
                            for($i = 0; $i < $nombreJoueurs; $i++) {
                                echo '<tr>';
                                    echo '<td>' . $baremesButeurs[$i]["Joueurs_NomFamille"]. '</td>';
                                    echo '<td><label>' . $baremesButeurs[$i]["Bonus_Points"]. '</label></td>';
                                echo '</tr>';
                            }
                        echo '</tbody>';
                    echo '</table>';
                echo '</div>';
            echo '</div>';
	?>

	<script>
		$(function() {
			afficherTitrePage('divBaremeBonus', 'Barème des bonus');

            $('.tableau--bonus').dataTable({"bPaginate": false, "bFilter": false, "bInfo": false});

            $('.bareme').each(function() {
				$('.ulNavigation').append('<li><a href="#' + $(this).attr('id') + '">' + $(this).attr('title') + '</a></li>');
			});
            $('#divBaremeBonus').tabs();

            $('.ui-tabs-anchor').prepend('<em class="icones icones-grandes">&#10150;</em>');
		});
	</script>
</body>
</html>