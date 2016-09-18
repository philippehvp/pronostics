<?php
	// Lecture des remplacements de joueurs, buteurs, scores (90', AP et fin de TAB) et fin des matches en direct
	
	// La page peut être appelée de deux manières :
	// - soit par une inclusion
	// - soit par un rafraîchissement (Ajax)
	
	$rafraichissement = isset($_POST["rafraichissement"]) ? $_POST["rafraichissement"] : 0;

	if($rafraichissement) {
		include_once('../commun_administrateur.php');
		include_once('../creer_match_fonctions.php');
		include_once('../envoyer_mail.php');
	}
	else {
		include_once('commun_administrateur.php');
		include_once('creer_match_fonctions.php');
		include_once('envoyer_mail.php');
	}

	$ordreSQL =		'	SELECT		matches.Match, Journees_Journee, Matches_Date' .
					'				,IFNULL(Matches_LienPageComplementaire, \'\') AS Matches_LienPageComplementaire' .
					'				,Equipes_EquipeDomicile, Equipes_EquipeVisiteur' .
					'				,equipes_domicile.Equipes_NomCourt AS EquipesDomicile_NomCourt, equipes_visiteur.Equipes_NomCourt AS EquipesVisiteur_NomCourt' .
					'	FROM		matches' .
					'	JOIN		matches_direct' .
					'				ON		matches.Match = matches_direct.Matches_Match' .
					'	JOIN		equipes equipes_domicile' .
					'				ON		Equipes_EquipeDomicile = equipes_domicile.Equipe' .
					'	JOIN		equipes equipes_visiteur' .
					'				ON		Equipes_EquipeVisiteur = equipes_visiteur.Equipe';

	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();
	
	// On détermine si à la fin de la lecture des informations, un nouvel événement important a eu lieu, auquel cas il faudra relancer les calculs
	$lancementCalcul = false;
	
	// La lecture du direct s'appuie sur deux pages différentes :
	// - une pour la composition et les changements de joueur
	// - une pour les buts
	// En effet, la page de composition et de changements des joueurs contient des informations sur les buteurs, mais pas pour la prolongation
	// L'idée est donc de lire les informations dans deux pages différentes
	// La fin de l'adresse des pages est la même. Cette information est stockée en base
	
	$adresseComposition = 'http://www.scorespro.com/soccer/ajax-matchcenter.php?link=';
	$adresseButeur = 'http://www.scorespro.com/soccer/livescore/';
	
	foreach($matches as $unMatch) {
		$documentComposition = new DOMDocument();
		@$documentComposition->loadHTMLFile($adresseComposition . $unMatch["Matches_LienPageComplementaire"]);
		
		$documentButeur = new DOMDocument();
		@$documentButeur->loadHTMLFile($adresseButeur . $unMatch["Matches_LienPageComplementaire"]);
		
		$xpathComposition = new DOMXpath($documentComposition);
		$xpathButeur = new DOMXpath($documentButeur);
		
		$match = $unMatch["Match"];
		$dateMatch = $unMatch["Matches_Date"];
		$equipeDomicile = $unMatch["Equipes_EquipeDomicile"];
		$equipeVisiteur = $unMatch["Equipes_EquipeVisiteur"];
		$nomEquipeDomicile = $unMatch["EquipesDomicile_NomCourt"];
		$nomEquipeVisiteur = $unMatch["EquipesVisiteur_NomCourt"];

		// Le match a-t-il débuté ?
		$baliseScore = $xpathButeur->query('//td[@class="score_t"]');
		if($baliseScore->length == 0 || trim($baliseScore->item(0)->textContent) == ':') {
			continue;
		}

		// Lancement du match si ce n'est déjà fait
		if(ajouterEvenement($bdd, $match, 0, 1, 0, 1) == 1) {
			initialiserMatch($bdd, $match);

			// Lancement du calcul
			$lancementCalcul = true;
		}
		
		// Sorties et entrées de joueurs (sorties normales et expulsions)
		// Lecture des joueurs de l'équipe domicile
		$baliseCompo1 = $xpathComposition->query('//td[@class="h_player"]');
		foreach($baliseCompo1 as $uneLigneDeCompo) {
			$joueurs = $uneLigneDeCompo->getElementsByTagName('span');
			foreach($joueurs as $unJoueur) {
				$class = $unJoueur->getAttribute('class');
				
				if($class == '')
					$nomJoueur = trim($unJoueur->textContent);
				else {
					$title = $unJoueur->getAttribute('title');
					$ajoutTableParticipants = false;
					$codeEvenement = 0;
					if(preg_match("/\bred\b/", $class))
						$codeEvenement = 25;
					else if(preg_match("/\breplace\b/", $class) && preg_match("/\bReplaced by\b/", $title))
					{
						$codeEvenement = 21;
					}
					else if(preg_match("/\breplace\b/", $class) && preg_match("/\bReplaced\b/", $title)) {
						$codeEvenement = 22;
						$ajoutTableParticipants = true;
					}
						
					if($codeEvenement == 0)
						continue;
					
					$joueur = rechercherJoueur($bdd, $nomJoueur, $equipeDomicile, $dateMatch, 2);
					
					// Recherche de la minute, contenue dans l'attribut title (dernier mot)
					$mots = explode(' ', $title);
					$datation = str_replace('.', '', str_replace('\'', '', end($mots)));
					$minute = substr($datation, 0, strpos($datation, '+') === FALSE ? strlen($datation) : strpos($datation, '+'));
					
					if($joueur > 0) {
						// Ajout de l'événement
						// On sait si l'événement avait déjà été répertorié ou non
						if(ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 1) == 1 && $ajoutTableParticipants == true) {
							// Ajout du joueur dans la table matches_participants s'il s'agit de l'entrée d'un joueur
							ajouterParticipant($bdd, $match, $joueur, $equipeDomicile);
							
							// Dans ce cas précis, il est nécessaire de relancer les calculs (en toute rigueur non, uniquement si le nouvel entrant a été pronostiqué comme
							// buteur par au moins un pronostiqueur)
							$lancementCalcul = true;
						}
					}
					else {
						$message = 'Match ' . $match . ' ' . $nomEquipeDomicile . '-' . $nomEquipeVisiteur . ' - Equipe domicile - Joueur ' . $nomJoueur . ' inconnu';
						if($nomJoueur != '') {
							if(ajouterErreur($bdd, $match, $message, $minute) == 1) {
								//envoyerMail('Le Poulpe d\'Or - Surveillance du direct', $message);
							}
						}
					}
				}
			}
		}

		// Lecture des joueurs de l'équipe visiteur
		$baliseCompo2 = $xpathComposition->query('//td[@class="a_player"]');
		foreach($baliseCompo2 as $uneLigneDeCompo) {
			$joueurs = $uneLigneDeCompo->getElementsByTagName('span');
			foreach($joueurs as $unJoueur) {
				$class = $unJoueur->getAttribute('class');
				
				if($class == '')
					$nomJoueur = trim($unJoueur->textContent);
				else {
					$title = $unJoueur->getAttribute('title');
					$ajoutTableParticipants = false;
					$codeEvenement = 0;
					if(preg_match("/\bred\b/", $class))
						$codeEvenement = 26;
					else if(preg_match("/\breplace\b/", $class) && preg_match("/\bReplaced by\b/", $title))
					{
						$codeEvenement = 23;
					}
					else if(preg_match("/\breplace\b/", $class) && preg_match("/\bReplaced\b/", $title)) {
						$codeEvenement = 24;
						$ajoutTableParticipants = true;
					}
						
					if($codeEvenement == 0)
						continue;
					
					$joueur = rechercherJoueur($bdd, $nomJoueur, $equipeVisiteur, $dateMatch, 2);
					
					// Recherche de la minute, contenue dans l'attribut title (dernier mot)
					$mots = explode(' ', $title);
					$datation = str_replace('.', '', str_replace('\'', '', end($mots)));
					$minute = substr($datation, 0, strpos($datation, '+') === FALSE ? strlen($datation) : strpos($datation, '+'));
					
					if($joueur > 0) {
						// Ajout de l'événement
						// On sait si l'événement avait déjà été répertorié ou non
						if(ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 1) == 1 && $ajoutTableParticipants == true) {
							// Ajout du joueur dans la table matches_participants s'il s'agit de l'entrée d'un joueur
							ajouterParticipant($bdd, $match, $joueur, $equipeVisiteur);
							
							// Dans ce cas précis, il est nécessaire de relancer les calculs (en toute rigueur non, uniquement si le nouvel entrant a été pronostiqué comme
							// buteur par au moins un pronostiqueur)
							$lancementCalcul = true;
						}
					}
					else {
						$message = 'Match ' . $match . ' ' . $nomEquipeDomicile . '-' . $nomEquipeVisiteur . ' - Equipe visiteur - Joueur ' . $nomJoueur . ' inconnu';
						if($nomJoueur != '') {
							if(ajouterErreur($bdd, $match, $message, $minute) == 1) {
								//envoyerMail('Le Poulpe d\'Or - Surveillance du direct', $message);
							}
						}
					}
				}
			}
		}

		// Lecture des buts
		// Effacement des événements de but de la table matches_evenements
		effacerEvenementsScore($bdd, $match);

		// Recherche des buteurs de l'équipe domicile
		$baliseButeursDomicile = $xpathButeur->query('//td[@class="home"]');
		foreach($baliseButeursDomicile as $uneLigneDeButeur) {
			$buteurs = $uneLigneDeButeur->getElementsByTagName('span');
			foreach($buteurs as $unButeur) {
				$class = $unButeur->getAttribute('class');
				if($class == 'slball') {
					// Extraction des minutes
					// Les TAB ne comportent pas d'information sur les minutes
					// On va donc exclure les buts de ce type
					if(strpos($uneLigneDeButeur->textContent, '\'') === FALSE)
						continue;

					// Les minutes sont à gauche
					// Attention, 90+2 n'est pas la même chose que 92
					$mots = explode(' ', $uneLigneDeButeur->textContent);
					$premier = reset($mots);
					$datation = str_replace('.', '', str_replace('\'', '', reset($mots)));
					$minute = substr($datation, 0, strpos($datation, '+') === FALSE ? strlen($datation) : strpos($datation, '+'));

					// Nom du joueur dont il faut supprimer le dernier caractère (un point)
					$nomJoueur = trim(str_replace($premier . ' ', '', str_replace('(pen)', '', $uneLigneDeButeur->textContent)));
					
					// Recherche des buts normaux et buts CSC
					if(strpos($uneLigneDeButeur->textContent, '(o.g.)') === FALSE) {
						$codeEvenement = 31;
						$equipe = $equipeDomicile;
					}
					else {
						$codeEvenement = 34;
						$equipe = $equipeVisiteur;
						$nomJoueur = trim(str_replace('(o.g.)', '', $nomJoueur));
					}
					
					$nomJoueur = rtrim($nomJoueur, '.');
					
					$joueur = rechercherJoueurInitialePrenomInverse($bdd, $nomJoueur, $equipe, $dateMatch, 2);
					if($joueur <= 0) {
						$joueur = rechercherJoueur($bdd, $nomJoueur, $equipe, $dateMatch, 2);
					}
					
					// Ecriture de l'événement dans la table des événements
					if($joueur > 0) {
						ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 0);
						mettreAJourJournee($bdd, $match);
					}
					else {
						$message = 'Match ' . $match . ' ' . $nomEquipeDomicile . '-' . $nomEquipeVisiteur . ' - Buteur ' . $nomJoueur . ' inconnu';
						if($nomJoueur != '') {
							if(ajouterErreur($bdd, $match, $message, $minute) == 1) {
								//envoyerMail('Le Poulpe d\'Or - Surveillance du direct', $message);
							}
						}
					}
					
				}
			}
		}
		
		// Recherche des buteurs de l'équipe visiteur
		$baliseButeursVisiteur = $xpathButeur->query('//td[@class="away"]');
		foreach($baliseButeursVisiteur as $uneLigneDeButeur) {
			$buteurs = $uneLigneDeButeur->getElementsByTagName('span');
			foreach($buteurs as $unButeur) {
				$class = $unButeur->getAttribute('class');
				if($class == 'slball') {
					// Extraction des minutes
					// Les TAB ne comportent pas d'information sur les minutes
					// On va donc exclure les buts de ce type
					if(strpos($uneLigneDeButeur->textContent, '\'') === FALSE)
						continue;

					// Les minutes sont à droite
					// Attention, 90+2 n'est pas la même chose que 92
					$mots = explode(' ', $uneLigneDeButeur->textContent);
					$dernier = end($mots);
					$datation = str_replace('.', '', str_replace('\'', '', end($mots)));
					$minute = substr($datation, 0, strpos($datation, '+') === FALSE ? strlen($datation) : strpos($datation, '+'));
										
					// Nom du joueur dont il faut supprimer le dernier caractère (un point)
					$nomJoueur = trim(str_replace(' ' . $dernier, '', str_replace('(pen)', '', $uneLigneDeButeur->textContent)));
					
					// Recherche des buts normaux et buts CSC
					if(strpos($uneLigneDeButeur->textContent, '(o.g.)') === FALSE) {
						$codeEvenement = 32;
						$equipe = $equipeVisiteur;
					}
					else {
						$codeEvenement = 33;
						$equipe = $equipeDomicile;
						$nomJoueur = trim(str_replace('(o.g.)', '', $nomJoueur));
					}
					
					$nomJoueur = rtrim($nomJoueur, '.');
					
					$joueur = rechercherJoueurInitialePrenomInverse($bdd, $nomJoueur, $equipe, $dateMatch, 2);
					if($joueur <= 0)
						rechercherJoueur($bdd, $nomJoueur, $equipe, $dateMatch, 2);
					
					// Ecriture de l'événement dans la table des événements
					if($joueur > 0) {
						ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 0);
						mettreAJourJournee($bdd, $match);
					}
					else {
						$message = 'Match ' . $match . ' ' . $nomEquipeDomicile . '-' . $nomEquipeVisiteur . ' - Buteur ' . $nomJoueur . ' inconnu';
						if($nomJoueur != '') {
							if(ajouterErreur($bdd, $match, $message, $minute) == 1) {
								//envoyerMail('Le Poulpe d\'Or - Surveillance du direct', $message);
							}
						}
					}
				}
			}
		}
		
		// Lecture du score
		// La lecture du score n'étant pas fiable, il est préférable de compter le nombre de buts de chaque période et pour chaque équipe
		// Deux lectures (fiables) sont indispensables à faire :
		// - voir si des prolongations ont lieu (pour mettre à jour les scores AP des équipes)
		// - voir le vainqueur des TAB s'ils ont eu lieu

		// Recherche d'une éventuelle prolongation
		$baliseProlongation = $xpathButeur->query('//td[text()="Extra Time"]');
		$prolongation = 0;
		
		if($baliseProlongation->length)
			$prolongation = 1;
		
		// Arrivé ici, on compare la signature des buteurs de la table des événements et on met à jour, le cas échéant, la table des buteurs
		if(synchroniserEvenementsScore($bdd, $match, $prolongation) == 1) {
			$lancementCalcul = true;
		}
		
		// Recherche de TAB et du score
		$baliseTAB = $xpathButeur->query('//td[contains(text(), "Penalties Shoot Out")]');
		if($baliseTAB->length) {
			
			// Le score des TAB n'apparaît qu'à la fin de la séance
			// On connaît donc le vainqueur
			$baliseScoreTAB = $xpathButeur->query('//span[@class="after_pen"]');
			if($baliseScoreTAB->length > 0) {
				$scoreTAB = explode('-', str_replace('(', '', str_replace(')', '', $baliseScoreTAB->item(0)->textContent)));
				$scoreTABEquipeDomicile = intval(trim(str_replace(' ', '', $scoreTAB[0])));
				$scoreTABEquipeVisiteur = intval(trim(str_replace(' ', '', $scoreTAB[1])));
				
				if($scoreTABEquipeDomicile > $scoreTABEquipeVisiteur)
					ecrireVainqueurTAB($bdd, $match, 1);
				else if($scoreTABEquipeDomicile < $scoreTABEquipeVisiteur)
					ecrireVainqueurTAB($bdd, $match, 2);
			}
		}
		
		// Arrivé ici, on regarde si le match est terminé, auquel cas on doit le supprimer de la liste des matches en direct
		$matchTermine = $xpathButeur->query('//td[contains(@class, "synopsis") and starts-with(text(), "Finished")]');
		if($matchTermine->length) {
			ajouterEvenement($bdd, $match, 0, 9, 0, 1);
			supprimerMatchDuDirect($bdd, $match);
			
		}
	}
	
	// S'il s'agit d'un nouvel événement et d'une entrée de joueur, il est nécessaire de relancer les calculs
	// En toute rigueur, ce lancement de calcul ne doit se faire qu'à partir du moment où le nouvel entrant a été pronostiqué par au moins un
	// pronostiqueur car ce serait inutile dans le cas inverse
	if($lancementCalcul == true)
		lancerCalcul($bdd, $unMatch["Journees_Journee"]);

?>