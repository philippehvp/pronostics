<?php
	// Lecture des remplacements de joueurs, buteurs, scores (90', AP et fin de TAB) et fin des matches en direct

	// La page peut être appelée de deux manières :
	// - soit par une inclusion
	// - soit par un rafraîchissement (Ajax)

	$rafraichissement = isset($_POST["rafraichissement"]) ? $_POST["rafraichissement"] : 0;

	if($rafraichissement) {
		include_once('../commun_administrateur.php');
		include_once('../creer_match_fonctions.php');
	}
	else {
		include_once('commun_administrateur.php');
		include_once('creer_match_fonctions.php');
	}

	$ordreSQL =		'	SELECT		matches.Match, matches.Journees_Journee, matches.Matches_Date, matches.Matches_LienPage' .
					'				,matches.Equipes_EquipeDomicile, matches.Equipes_EquipeVisiteur, matches.Matches_SansButeur, IFNULL(matches.Matches_MatchIgnore, 0) AS Matches_MatchIgnore' .
					'	FROM		matches' .
					'	JOIN		matches_direct' .
					'				ON		matches.Match = matches_direct.Matches_Match';
	$req = $bdd->query($ordreSQL);
	$matches = $req->fetchAll();

	// On détermine si à la fin de la lecture des informations, un nouvel événement important a eu lieu, auquel cas il faudra relancer les calculs
	$lancementCalcul = false;

	foreach($matches as $unMatch) {
		if(strcmp($unMatch["Matches_LienPage"], '') == 0)
			continue;

		$document = new DOMDocument();
		@$document->loadHTMLFile($unMatch["Matches_LienPage"]);

		$xpath = new DOMXpath($document);

		$match = $unMatch["Match"];

		// Le match a-t-il débuté ?
		$baliseMatchDetail = $xpath->query('//div[@id="ajax-match-detail-1"]');
		if(!$baliseMatchDetail) {
			continue;
		}

		$baliseScore = $xpath->query('//span[contains(@class, "score")]', $baliseMatchDetail->item(0));

		if(!$baliseScore) {
			continue;
		}

		$score = explode(" - ", $baliseScore->item(0)->textContent);

		$scoreEquipeDomicile = trim($score[0]);
		$scoreEquipeVisiteur = trim($score[1]);

		if($scoreEquipeDomicile == "" || $scoreEquipeVisiteur == "") {
			continue;
		}

		// Le match a débuté
		if(ajouterEvenement($bdd, $match, 0, 1, 0, 1) == 1) {
			initialiserMatch($bdd, $match);

			// Lancement du calcul
			$lancementCalcul = true;
		}

		// Sorties et entrées de joueurs (sorties normales et expulsions)
		$remplacants = $xpath->query('//span[contains(@class, "ico_evenement91") or contains(@class, "ico_evenement92") or contains(@class, "ico_evenement81") or contains(@class, "ico_evenement82")]');
		
		foreach($remplacants as $unRemplacant) {
			$classeEvenement = $unRemplacant->getAttribute('class');

			$codeClasseEvenement = 0;
			if(strpos($classeEvenement, "ico_evenement91"))
				$codeClasseEvenement = 91;
			else if(strpos($classeEvenement, "ico_evenement92"))
				$codeClasseEvenement = 92;
			else if(strpos($classeEvenement, "ico_evenement81"))
				$codeClasseEvenement = 81;
			else if(strpos($classeEvenement, "ico_evenement82"))
				$codeClasseEvenement = 82;

			$ajoutTableParticipants = false;

			switch($codeClasseEvenement) {
				case 81: $codeEvenement = 21; $equipe = $unMatch["Equipes_EquipeDomicile"]; break;
				case 82: $codeEvenement = 23; $equipe = $unMatch["Equipes_EquipeVisiteur"]; break;
				case 91: $codeEvenement = 22; $equipe = $unMatch["Equipes_EquipeDomicile"]; $ajoutTableParticipants = true; break;
				case 92: $codeEvenement = 24; $equipe = $unMatch["Equipes_EquipeVisiteur"]; $ajoutTableParticipants = true; break;
			}

			$nomJoueur = remplacerCaracteres(my_utf8_decode(trim($unRemplacant->parentNode->textContent)));
			$joueur = rechercherJoueur($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);

			if($joueur <= 0) {
				$joueur = rechercherJoueurInitialePrenom($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);
			}

			// Ecriture de l'événement dans la table des événements
			if($joueur > 0) {
				// Recherche de la minute
				$noeuds = $xpath->query('td[@class="c2"]', $unRemplacant->parentNode->parentNode);
				$minute = -1;
				foreach($noeuds as $unNoeud)
					$minute = str_replace('\'', '', $unNoeud->textContent);

				if($minute != -1) {
					// Ajout de l'événement
					// On sait si l'événement avait déjà été répertorié ou non

					if(ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 1) == 1 && $ajoutTableParticipants == true) {
						// Ajout du joueur dans la table matches_participants s'il s'agit de l'entrée d'un joueur
						ajouterParticipant($bdd, $match, $joueur, $equipe);

						// Dans ce cas précis, il est nécessaire de relancer les calculs (en toute rigueur non, uniquement si le nouvel entrant a été pronostiqué comme
						// buteur par au moins un pronostiqueur)
						$lancementCalcul = true;
					}
				}
			}
			else {
				ajouterErreur($bdd, $match, 'Equipe ' . $equipe . ' - Joueur remplaçant ' . $nomJoueur . ' inconnu', 0);
			}
		}

		// Expulsions de joueurs (expulsion directe ou après deux cartons jaunes)
		$expulses = $xpath->query('//span[contains(@class, "ico_evenement3") or contains(@class, "ico_evenement5")]');

		foreach($expulses as $unExpulse) {
			$attributs = $unExpulse->parentNode->attributes;
			foreach($attributs as $unAttribut) {
				if($unAttribut->nodeValue == 'c1') {
					// Expulsion d'un joueur de l'équipe domicile
					$codeEvenement = 25;
					$equipe = $unMatch["Equipes_EquipeDomicile"];
				} else if($unAttribut->nodeValue == 'c3') {
					// Expulsion d'un joueur de l'équipe visiteur
					$codeEvenement = 26;
					$equipe = $unMatch["Equipes_EquipeVisiteur"];
				}

				$nomJoueur = remplacerCaracteres(my_utf8_decode(trim($unExpulse->parentNode->textContent)));
				$joueur = rechercherJoueur($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);
				if($joueur <= 0)
					$joueur = rechercherJoueurInitialePrenom($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);

				// Ecriture de l'événement dans la table des événements des joueurs
				if($joueur > 0) {
					// Recherche de la minute
					$noeuds = $xpath->query('td[@class="c2"]', $unExpulse->parentNode->parentNode);
					$minute = -1;
					foreach($noeuds as $unNoeud)
						$minute = str_replace('\'', '', $unNoeud->textContent);

					if($minute != -1)
						ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 1);
				} else {
					ajouterErreur($bdd, $match, 'Equipe ' . $equipe . ' - Expulsion ' . $nomJoueur . ' inconnu', 0);
				}
			}
		}

		if($unMatch["Matches_MatchIgnore"] == 0) {
			// Lecture des buts
			// Une distinction est faite entre :
			// - les buts "normaux" (identifiés par la balise de classe ico_evenement1)
			// - les buts sur pénalty (identifiés par la balise de classe ico_evenement2)
			// - les buts CSC (identifiés par la balise de classe ico_evenement7)

			// Pour un but donné, on détermine s'il s'agit d'un but de l'équipe domicile ou visiteur en regardant la classe du noeud parent
			// But domicile : class="c1"
			// But visiteur : class="c3"

			// Dans la réalité, un but attribué à un joueur peut lui être désattribué 10 minutes après (visionnage)
			// Pire, les buts marqués dans le temps additionnel ont la même minute (45', 90, 105', 120')
			// On ne peut donc pas se fier au temps pour voir si un événement est unique ou non
			// L'algorithme est le suivant :
			// - effacement de tous les événements de but de la table matches_evenements
			// - ajout des buts
			// - comparaison de la somme des buteurs de la table matches_evenements et de la somme des buteurs de la table matches_buteurs
			// - si une différence est constatée :
			//   * supprimer tous les buteurs de la table matches_buteurs
			//   * reconstruire la table matches_buteurs avec la table matches_evenements

			// Effacement des événements de but de la table matches_evenements
			effacerEvenementsScore($bdd, $match);

			$buteurs = $xpath->query('//span[contains(@class, "ico_evenement1") or contains(@class, "ico_evenement2")]');
			foreach($buteurs as $unButeur) {
				$attributs = $unButeur->parentNode->attributes;
				foreach($attributs as $unAttribut) {
					$nomJoueur = str_replace('(Pénalty)', '', remplacerCaracteres(my_utf8_decode(trim($unButeur->parentNode->textContent))));
					if($unAttribut->nodeValue == 'c1') {
						$codeEvenement = 31;
						$equipe = $unMatch["Equipes_EquipeDomicile"];
					}
					else if($unAttribut->nodeValue == 'c3') {
						$codeEvenement = 32;
						$equipe = $unMatch["Equipes_EquipeVisiteur"];
					}

					// Dans le cas des matches sans buteur (Coupe de France), certaines équipes ne comportent pas de joueurs
					// La recherche sera donc infructueuse
					// On décide alors de ne pas se fier au nom du joueur mais de simplement ajouter les événements de but et de mettre à jour le score
					if($unMatch["Matches_SansButeur"] == 1) {
						$joueur = 999999;
					}
					else {
						$joueur = rechercherJoueur($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);
						if($joueur <= 0)
							$joueur = rechercherJoueurInitialePrenom($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);
					}

					// Ecriture de l'événement dans la table des événements
					if($joueur > 0) {
						// Recherche de la minute
						$noeuds = $xpath->query('td[@class="c2"]', $unButeur->parentNode->parentNode);
						$minute = -1;
						foreach($noeuds as $unNoeud)
							$minute = str_replace('\'', '', $unNoeud->textContent);

						if($minute != -1) {
							ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 0);
							mettreAJourJournee($bdd, $match);
						}
					}
					else
						ajouterErreur($bdd, $match, 'Equipe ' . $equipe . ' - Buteur ' . $nomJoueur . ' inconnu', 0);
				}
			}

			// Buts CSC (ico_evenement7)
			// Le but est du bon côté, mais le nom du buteur doit être recherché dans l'équipe adverse
			$buteurs = $xpath->query('//span[contains(@class, "ico_evenement7")]');
			foreach($buteurs as $unButeur) {
				$attributs = $unButeur->parentNode->attributes;
				foreach($attributs as $unAttribut) {
					$nomJoueur = str_replace('(Contre son camps)', '', remplacerCaracteres(my_utf8_decode(trim($unButeur->parentNode->textContent))));
					if($unAttribut->nodeValue == 'c3') {
						$codeEvenement = 33;
						$equipe = $unMatch["Equipes_EquipeDomicile"];
					}
					else if($unAttribut->nodeValue == 'c1') {
						$codeEvenement = 34;
						$equipe = $unMatch["Equipes_EquipeVisiteur"];
					}

					// Même remarque pour les matches sans buteur
					if($unMatch["Matches_SansButeur"] == 1) {
						$joueur = 999999;
					}
					else {
						$joueur = rechercherJoueur($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);

						if($joueur <= 0)
							$joueur = rechercherJoueurInitialePrenom($bdd, $nomJoueur, $equipe, $unMatch["Matches_Date"], 1);
					}

					// Ecriture de l'événement dans la table des événements
					if($joueur > 0) {
						// Recherche de la minute
						$noeuds = $xpath->query('td[@class="c2"]', $unButeur->parentNode->parentNode);
						$minute = -1;
						foreach($noeuds as $unNoeud)
							$minute = str_replace('\'', '', $unNoeud->textContent);

						if($minute != -1) {
							ajouterEvenement($bdd, $match, $joueur, $codeEvenement, $minute, 0);
							mettreAJourJournee($bdd, $match);
						}
					}
					else
						ajouterErreur($bdd, $match, 'Equipe ' . $equipe . ' - Buteur CSC ' . $nomJoueur . ' inconnu', 0);
				}
			}

			// Lecture du score
			// La lecture du score n'étant pas fiable, il est préférable de compter le nombre de buts de chaque période et pour chaque équipe
			// Deux lectures (fiables) sont indispensables à faire :
			// - voir si des prolongations ont lieu (pour mettre à jour les scores AP des équipes)
			// - voir le vainqueur des TAB s'ils ont eu lieu

			// Recherche d'une éventuelle prolongation
			// Il existe deux méthodes pour chercher si une prolongation a lieu :
			// - recherche du texte "Score après prolongation"
			// - lecture du chrono et détection si l'on est à 91 ou plus
			$prolongation = 0;

			$texteScoreApresProlongation = "Score apr";
			$baliseProlongation = $xpath->query('//td[contains(text(), "' . my_utf8_decode($texteScoreApresProlongation) . '")]');
			$baliseEntete = $xpath->query('//div[@id="match_entete_2"]');

			if($baliseProlongation->length) {
				$prolongation = 1;
			}

			if($baliseEntete->length) {
				if(is_numeric($baliseEntete->item(0)->textContent)) {
					$minuteMatch = (int) $baliseEntete->item(0)->textContent;
					if($minuteMatch >= 91)
						$prolongation = 1;
				}
			}

			// Arrivé ici, on compare la signature des buteurs de la table des événements et on met à jour, le cas échéant, la table des buteurs
			if(synchroniserEvenementsScore($bdd, $match, $prolongation) == 1) {
				$lancementCalcul = true;
			}

			// Recherche de TAB et du score
			$baliseTAB = $xpath->query('//td[text()="Tirs au but"]');
			if($baliseTAB->length) {
				// Le score des TAB n'apparaît qu'à la fin de la séance
				// On connaît donc le vainqueur
				$baliseScoreTAB = $xpath->query('//td[text()="Tirs au but"]/following-sibling::*[1]');
				if($baliseScoreTAB->length > 0) {
					$scoreTAB = explode('-', $baliseScoreTAB->item(0)->nodeValue);
					$scoreTABEquipeDomicile = intval(trim(str_replace(' ', '', $scoreTAB[0])));
					$scoreTABEquipeVisiteur = intval(trim(str_replace(' ', '', $scoreTAB[1])));

					if($scoreTABEquipeDomicile > $scoreTABEquipeVisiteur)
						ecrireVainqueurTAB($bdd, $match, 1);
					else if($scoreTABEquipeDomicile < $scoreTABEquipeVisiteur)
						ecrireVainqueurTAB($bdd, $match, 2);
				}
			}
		}

		// Arrivé ici, on regarde si le match est terminé pour indiquer qu'il n'est plus en direct
		$texteMatchTermine = "Aujourd'hui";
		$baliseMatchTermine = $xpath->query('//div[contains(text(), "' . my_utf8_decode($texteMatchTermine) . '") and contains(@class, "status")]');
		if($baliseMatchTermine->length) {
			ajouterEvenement($bdd, $match, 0, 9, 0, 1);
			supprimerMatchDuDirect($bdd, $match);
		}

		// S'il s'agit d'un nouvel événement et d'une entrée de joueur, il est nécessaire de relancer les calculs
		// En toute rigueur, ce lancement de calcul ne doit se faire qu'à partir du moment où le nouvel entrant a été pronostiqué par au moins un
		// pronostiqueur car ce serait inutile dans le cas inverse
		if($lancementCalcul == true) {
			lancerCalcul($bdd, $unMatch["Journees_Journee"]);
		}
	}
?>
