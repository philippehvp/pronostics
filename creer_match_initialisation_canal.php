<?php
	include_once('commun_administrateur.php');
	
	// Initialisation du match Canal pour les pronostiquers pour une journée
	
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	
	$ordreSQL =		'	REPLACE INTO	journees_pronostiqueurs_canal' .
					'	SELECT			' . $journee . ', pronostiqueurs.Pronostiqueur' .
					'					,(' .
					'						SELECT		matches.Match' .
					'						FROM		matches' .
					'						WHERE		matches.Journees_Journee = ' . $journee .
					'									AND		matches.Matches_Coefficient = 2' .
					'					) AS Matches_Match' .
					'	FROM			pronostiqueurs' .
					'	LEFT JOIN		journees_pronostiqueurs_canal' .
					'					ON		journees_pronostiqueurs_canal.Journees_Journee = ' . $journee .
					'							AND		pronostiqueurs.Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur' .
					'	WHERE			journees_pronostiqueurs_canal.Journees_Journee IS NULL';
	$bdd->exec($ordreSQL);

	/*
	replace into journees_pronostiqueurs_canal(Journees_Journee, Pronostiqueurs_Pronostiqueur, Matches_Match)
select 1, pronostiqueurs.Pronostiqueur, (select matches.Match from matches where matches.Journees_Journee = 1 and matches.Matches_Coefficient = 2) as Matches_Match
from pronostiqueurs
left join journees_pronostiqueurs_canal
on journees_pronostiqueurs_canal.Journees_Journee = 1
and pronostiqueurs.Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur
where journees_pronostiqueurs_canal.Journees_Journee is null;
	*/
?>

