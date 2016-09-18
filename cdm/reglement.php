<?php
	include('commun.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include('commun_entete.php');
?>
</head>

<body>
	<?php
		$nomPage = 'reglement.php';
		include('bandeau.php');
	?>
	
	<div id="divEnteteReglement">
	</div>
	
	<div id="divReglement">
		Le nombre de points se calcule selon les règles suivantes :<br />

		<br />
		<b>Première phase :</b><br />
		<ul>
			<li>Score exact 10 pts</li>
			<li>Le gagnant + le nombre de buts du gagnant 8 pts</li>
			<li>Match nul sans le bon score 7 pts</li>
			<li>Le gagnant + la bonne différence de buts 7 pts</li>
			<li>Le gagnant + le nombre de buts du perdant 6 pts</li>
			<li>Seulement le gagnant 5 pts</li>
			<li>Seulement le nombre de buts du gagnant 3 pts</li>
			<li>Le nombre de but d'une équipe en cas de match nul 2 pts</li>
			<li>Seulement le nombre de buts du perdant 1 pt</li>
		</ul>

		<br />
		<b>Poule :</b>
		<ul>
			<li>Poule exacte 20 pts</li>
			<li>Les deux premiers dans le désordre 12 pts</li>
			<li>L'équipe qui termine première 10 pts</li>
			<li>L'équipe qui termine deuxième 6 pts</li>
			<li>L'équipe qui termine troisième 3 pts</li>
			<li>L'équipe qui termine quatrième 1 pt</li>
		</ul>

		<br />
		<b>Deuxième phase :</b>
		<ul>
			<li>10 pts pour avoir trouvé la prolongation qui a lieu</li>
			<li>5 pts pour avoir trouvé les tirs au but qui ont lieu</li>
			<li>-2 pts pour avoir pronostiqué la prolongation ou les tirs au but alors qu'ils n'ont pas eu lieu</li>
			<li>-2 pts pour ne pas avoir pronostiqué la prolongation ou les tirs au but alors qu'ils ont eu lieu</li>
			<li>15 pts pour avoir trouvé l'affiche exacte de chaque huitième de finale</li>
			<li>5 pts par équipe présente en huitièmes de finale si l'affiche n'est pas exacte</li>
			<li>Pour les huitièmes de finale, même barème que la phase de poule x2 seulement si l'affiche est exacte</li>
			<li>30 pts pour avoir trouvé l'affiche exacte de chaque quart de finale</li>
			<li>10 pts par équipe présente en quarts de finale si l'affiche n'est pas exacte</li>
			<li>Pour les quarts de finale, même barème que la phase de poule x3 seulement si l'affiche est exacte</li>
			<li>45 pts pour avoir trouvé l'affiche exacte des demi-finales</li>
			<li>15 pts par équipe présente en demi-finale si l'affiche n'est pas exacte</li>
			<li>Pour les demi-finales, même barème que la phase de poule x4 seulement si l'affiche est exacte</li>
			<li>60 pts pour avoir trouvé l'affiche exacte du match de la troisième place</li>
			<li>20 pts si une seule équipe sur les deux</li>
			<li>Pour le match de la troisième place, même bareme que la première phase x5 seulement si l'affiche est exacte</li>
			<li>75 pts pour avoir trouvé l'affiche exacte de la finale</li>
			<li>25 pts si une seule équipe sur les deux</li>
			<li>Pour la finale, même bareme que la phase de poule x6 seulement si l'affiche est exacte</li>
			<li>100 pts pour avoir trouvé le vainqueur de la Coupe du Monde</li>
			<li>50 pts pour avoir trouvé le finaliste</li>
			<li>30 pts pour avoir trouvé la troisième place</li>
			<li>60 pts pour avoir trouvé le meilleur buteur</li>
		</ul>

		<br />
		<b>Bonus obtenu si l'une des équipes ci-dessous sort des poules :</b>
		<table id="tableBonus">
			<tr>
				<td>Mexique, 20 pts</td>
				<td>Cameroun, 40 pts</td>
				<td>Croatie, 20 pts</td>
			</tr>
			<tr>
				<td>Chili, 10 pts</td>
				<td>Australie, 50 pts</td>
				<td>Côte d'Ivoire, 10 pts</td>
			</tr>
			<tr>
				<td>Japon, 10 pts</td>
				<td>Grèce, 20 pts</td>
				<td>Costa Rica, 50 pts</td>
			</tr>
			<tr>
				<td>Equateur, 10 pts</td>
				<td>Honduras, 50 pts</td>
				<td>Nigéria, 20 pts</td>
			</tr>
			<tr>
				<td>Iran, 50 pts</td>
				<td>Ghana, 30 pts</td>
				<td>Etats-Unis, 40 pts</td>
			</tr>
			<tr>
				<td>Corée du Sud, 20 pts</td>
				<td colspan="2">Algérie, 40 pts</td>
			</tr>
		</table>
		
		<br />
		<b>Egalité entre les joueurs à la fin du concours :</b>
		<br />
		Les joueurs concernés seront départagés par les critères suivants (par ordre décroissant d'importance) :
		<ul>
			<li>Le nombre de scores exacts trouvés</li>
			<li>Le nom de l'équipe championne du monde</li>
			<li>Le nom du finaliste</li>
			<li>Le nombre de demi-finalistes</li>
			<li>Etc.</li>
		</ul>
		
	</div>
	
	<script>
		$(function() {
			afficherTitrePage('divEnteteReglement', 'Règlement du concours');
		});
		
	</script>
	
</body>
</html>