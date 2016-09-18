/*
	Lors de l'inscription d'un pronostiqueur, voici la liste des tables dont il faut créer les lignes :
	- cdm_pronostics_poule
	- cdm_pronostics_sequencement
	- cdm_pronostics_buteur
*/

/* Pronostics des matches de poule */
CREATE TABLE IF NOT EXISTS `cdm_pronostics_poule` (
  `Pronostiqueurs_Pronostiqueur` int(11) NOT NULL,
  `Matches_Match` int(11) NOT NULL,
  `Equipes_Equipe` int(11) NOT NULL,
  `PronosticsPoule_Score` int(11) DEFAULT NULL,
  PRIMARY KEY (`Pronostiqueurs_Pronostiqueur`,`Matches_Match`,`Equipes_Equipe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
TRUNCATE TABLE cdm_pronostics_poule;
insert into cdm_pronostics_poule(Pronostiqueurs_Pronostiqueur, Matches_Match, Equipes_Equipe)
select		cdm_pronostiqueurs_poule.Pronostiqueurs_Pronostiqueur, cdm_pronostiqueurs_poule.Matches_Match, Equipes_Equipe
from		(
				select		distinct *
				from		(
								select		cdm_pronostiqueurs_poule.Pronostiqueur as Pronostiqueurs_Pronostiqueur, cdm_pronostiqueurs_poule.Match as Matches_Match, Equipes_EquipeA AS Equipes_Equipe
								from		(
												select		*
												from		cdm_pronostiqueurs
												full join	cdm_matches_poule
											) cdm_pronostiqueurs_poule
								union all
								select		cdm_pronostiqueurs_poule.Pronostiqueur as Pronostiqueurs_Pronostiqueur, cdm_pronostiqueurs_poule.Match as Matches_Match, Equipes_EquipeB AS Equipes_Equipe
								from		(
												select		*
												from		cdm_pronostiqueurs
												full join	cdm_matches_poule
											) cdm_pronostiqueurs_poule
							) tout
			) cdm_pronostiqueurs_poule
;

/* Pronostics de la phase finale (numéros des équipes pour chaque match de la phase finale) */
CREATE TABLE IF NOT EXISTS `cdm_pronostics_sequencement` (
  `Pronostiqueurs_Pronostiqueur` int(11) NOT NULL,
  `Matches_Match` int(11) NOT NULL,
  `Equipes_Equipe` int(11) NULL,
  `PronosticsSequencement_Branche` int(11) NULL,
  PRIMARY KEY (`Pronostiqueurs_Pronostiqueur`,`Matches_Match`,`Equipes_Equipe`, `PronosticsSequencement_Branche`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
TRUNCATE TABLE cdm_pronostics_sequencement;
insert into cdm_pronostics_sequencement(Pronostiqueurs_Pronostiqueur, Matches_Match, PronosticsSequencement_EquipeAB)
select		cdm_pronostiqueurs_poule.Pronostiqueur as Pronostiqueurs_Pronostiqueur, Matches_Match, MatchesSequencement_EquipeAB
from		(
				select		*
				from		cdm_pronostiqueurs
				full join	cdm_matches_sequencement
			) cdm_pronostiqueurs_poule
;

/* Pronostics du meilleur buteur */
CREATE TABLE IF NOT EXISTS `cdm_pronostics_buteur` (
  `Pronostiqueurs_Pronostiqueur` int(11) NOT NULL,
  `Joueurs_Joueur` int(11) NULL,
  PRIMARY KEY (`Pronostiqueurs_Pronostiqueur`,`Joueurs_Joueur`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
TRUNCATE TABLE cdm_pronostics_buteur;
insert into cdm_pronostics_buteur(Pronostiqueurs_Pronostiqueur, Joueurs_Joueur)
select		Pronostiqueur as Pronostiqueurs_Pronostiqueur, NULL AS Joueurs_Joueur
from		cdm_pronostiqueurs;
;



/* Matches de phase finale (ne pas oublier le match de la troisième place) */
CREATE TABLE IF NOT EXISTS `cdm_matches_phase_finale` (
  `Match` int(11) NOT NULL AUTO_INCREMENT,
  `Equipes_EquipeA` int(11) NOT NULL,
  `Equipes_EquipeB` int(11) NOT NULL,
  `Matches_ScoreEquipeA` int(11) DEFAULT NULL,
  `Matches_ScoreEquipeB` int(11) DEFAULT NULL,
  `Matches_ScoreAPEquipeA` int(11) DEFAULT NULL,
  `Matches_ScoreAPEquipeB` int(11) DEFAULT NULL,
  `Matches_Vainqueur` int(11) DEFAULT NULL,
  PRIMARY KEY (`Match`,`Equipes_EquipeA`,`Equipes_EquipeB`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
TRUNCATE TABLE cdm_matches_phase_finale;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;
INSERT INTO cdm_matches_phase_finale(Equipes_EquipeA, Equipes_EquipeB, Matches_ScoreEquipeA, Matches_ScoreEquipeB, Matches_ScoreAPEquipeA, Matches_ScoreAPEquipeB, Matches_Vainqueur)
SELECT NULL, NULL, NULL, NULL, NULL, NULL, NULL;


/* Qualifications des poules (permet de savoir quels matches vont jouer les premier et deuxième de chaque poule */
CREATE TABLE IF NOT EXISTS `cdm_poules_qualifications` (
  `Poules_Poule` int(11) NOT NULL,
  `PoulesQualifications_Premier` int(11) DEFAULT NULL,
  `PoulesQualifications_Deuxieme` int(11) DEFAULT NULL,
  PRIMARY KEY (`Poules_Poule`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
TRUNCATE TABLE cdm_poules_qualifications;
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(1, 1, 2);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(2, 2, 1);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(3, 3, 4);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(4, 4, 3);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(5, 5, 6);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(6, 6, 5);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(7, 7, 8);
INSERT INTO cdm_poules_qualifications(Poules_Poule, PoulesQualifications_Premier, PoulesQualifications_Deuxieme) VALUES(8, 8, 7);


/* Séquencement des matches (permet de savoir le prochain match que va jouer le vainqueur d'un match) */
CREATE TABLE IF NOT EXISTS `cdm_matches_sequencement` (
  `Matches_Match` int(11) NOT NULL DEFAULT '0',
  `MatchesSequencement_Match` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Matches_Match`,`MatchesSequencement_Match`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
TRUNCATE TABLE cdm_matches_sequencement;
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(1, 9);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(2, 10);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(3, 9);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(4, 10);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(5, 11);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(6, 12);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(7, 11);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(8, 12);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(9, 13);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(10, 14);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(11, 13);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(12, 14);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(13, 15);
INSERT INTO cdm_matches_sequencement(Matches_Match, MatchesSequencement_Match) VALUES(14, 15);