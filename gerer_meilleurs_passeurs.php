<?php
	include_once('commun_administrateur.php');
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
		$nomPage = 'gerer_meilleurs_passeurs.php';
		include_once('bandeau.php');
		
		echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
		
		// Gestion des meilleurs passeurs
		
		// Lecture des meilleurs passeurs
		$ordreSQL =		'	SELECT      joueurs.Joueur, IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) AS Joueurs_NomFamille' .
                        '               ,bonus_meilleur_passeur.Bonus_Points' .
                        '   FROM        joueurs' .
                        '   JOIN        bonus_meilleur_passeur' .
                        '               ON      joueurs.Joueur = bonus_meilleur_passeur.Joueurs_Joueur' .
                        '   ORDER BY    IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille)';

		$req = $bdd->query($ordreSQL);
		$baremesPasseurs = $req->fetchAll();
        $nombreJoueurs = count($baremesPasseurs);

		echo '<div id="divBonus" class="contenu-page">';
            // Tableau des passeurs
            echo '<table class="tableau--bonus">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>Supprimer</th>';
                        echo '<th>Joueurs</th>';
                        echo '<th>Bonus meilleur passeur</th>';
                    echo '</tr>';
                echo '</thead>';
                
                echo '<tbody>';
                    for($i = 0; $i < $nombreJoueurs; $i++) {
                        echo '<tr>';
                            echo '<td><label onclick="gererMeilleursPasseurs_supprimerJoueur(' . $baremesPasseurs[$i]["Joueur"] . ');">Supprimer</label></td>';
                            echo '<td>' . $baremesPasseurs[$i]["Joueurs_NomFamille"]. '</td>';
                            echo '<td>' . $baremesPasseurs[$i]["Bonus_Points"] . '</td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';
            
            // Liste de tous les joueurs des équipes de Ligue 1, classés par équipe
            $ordreSQL =		'	SELECT      equipes.Equipe, IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom) AS Equipes_NomCourt' .
                            '   FROM        equipes' .
                            '   JOIN        engagements' .
                            '               ON      equipes.Equipe = engagements.Equipes_Equipe' .
                            '   WHERE       engagements.Championnats_Championnat = 1' .
                            '               AND     equipes.Equipes_L1Europe IS NULL' .
                            '   ORDER BY    IFNULL(equipes.Equipes_NomCourt, equipes.Equipes_Nom)';
            $req = $bdd->query($ordreSQL);
            $equipes = $req->fetchAll();
            
            // Parcours des équipes et de leurs joueurs
            foreach($equipes as $uneEquipe) {
                $ordreSQL =     '   SELECT      joueurs.Joueur, IFNULL(joueurs.Joueurs_NomCourt, joueurs.Joueurs_NomFamille) AS Joueurs_NomFamille' .
                                '   FROM        joueurs' .
                                '   JOIN        joueurs_equipes' .
                                '               ON      joueurs.Joueur = joueurs_equipes.Joueurs_Joueur' .
                                '   LEFT JOIN   bonus_meilleur_passeur' .
                                '               ON      joueurs.Joueur = bonus_meilleur_passeur.Joueurs_Joueur' .
                                '   WHERE       joueurs_equipes.Equipes_Equipe = ' . $uneEquipe["Equipe"] .
                                '               AND     bonus_meilleur_passeur.Joueurs_Joueur IS NULL' .
                                '               AND     (' .
                                '                           joueurs_equipes.JoueursEquipes_Fin IS NULL' .
                                '                           OR      joueurs_equipes.JoueursEquipes_Fin >= NOW()' .
                                '                       )';
                $req = $bdd->query($ordreSQL);
                $joueurs = $req->fetchAll();
                
                echo '<h2>' . $uneEquipe["Equipes_NomCourt"] . '</h2>';
                foreach($joueurs as $unJoueur) {
                    echo '<label onclick="gererMeilleursPasseurs_ajouterJoueur(' . $unJoueur["Joueur"] . ')">' . $unJoueur["Joueurs_NomFamille"] . ' - </label>';
                }
            }
            
            
		echo '</div>';

	?>
	
	<script>
		$(function() {
			afficherTitrePage('divBonus', 'Gérer le barème des bonus passeurs');
            
            $('.tableau--bonus').dataTable({"bPaginate": false, "bFilter": false, "bInfo": false});
		});
	</script>
</body>
</html>