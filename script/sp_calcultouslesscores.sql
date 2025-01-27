
DROP PROCEDURE IF EXISTS sp_calcultouslesscores;
DELIMITER $$
CREATE DEFINER=`lepoulpeg`@`%` PROCEDURE `sp_calcultouslesscores`(IN `p_Journee` INT)
    MODIFIES SQL DATA
BEGIN
    /* sp_calcultouslesscores */
    DECLARE l_Date DATETIME;
    DECLARE l_Championnat INT;
    DECLARE l_JourneeNumeroInterne INT;

    SELECT      journees.Championnats_Championnat, journees.Journees_NumeroInterne
    INTO        l_Championnat, l_JourneeNumeroInterne
    FROM        journees
    WHERE       Journee = p_Journee;

    INSERT INTO     scores(Pronostiqueurs_Pronostiqueur, Matches_Match)
    SELECT          matches_et_pronostics.Pronostiqueurs_Pronostiqueur, matches_et_pronostics.Matches_Match
    FROM            scores
    RIGHT JOIN      (
                        SELECT      p.Matches_Match, p.Pronostiqueurs_Pronostiqueur
                        FROM        matches m
                        JOIN        journees j
                                    ON      m.Journees_Journee = j.Journee
                        JOIN        pronostics p
                                    ON      m.Match = p.Matches_Match
                        WHERE       m.Matches_ScoreEquipeDomicile IS NOT NULL
                                    AND     m.Matches_ScoreEquipeVisiteur IS NOT NULL
                                    AND     p.Pronostics_ScoreEquipeDomicile IS NOT NULL
                                    AND     p.Pronostics_ScoreEquipeVisiteur IS NOT NULL
                                    AND     j.Journee = p_Journee
                    ) matches_et_pronostics
                    ON      scores.Matches_Match = matches_et_pronostics.Matches_Match
                            AND     scores.Pronostiqueurs_Pronostiqueur = matches_et_pronostics.Pronostiqueurs_Pronostiqueur
    WHERE           scores.Matches_Match IS NULL;

    INSERT INTO     scores(Pronostiqueurs_Pronostiqueur, Matches_Match, Scores_ScoreMatch, Scores_ScoreButeur, Scores_ScoreBonus)
    SELECT          PronostiqueursEtMatches.Pronostiqueur, PronostiqueursEtMatches.Match, 0 AS Scores_ScoreMatch, 0 AS Scores_ScoreButeur, 0 AS Scores_ScoreBonus
    FROM            (
                        SELECT      Pronostiqueur, `Match`
                        FROM        pronostiqueurs
                        FULL JOIN   matches
                        JOIN        journees
                                    ON      Journees_Journee = Journee
                        JOIN        inscriptions
                                    ON      journees.Championnats_Championnat = inscriptions.Championnats_Championnat
                                            AND     Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
                        WHERE       matches.Journees_Journee = p_Journee
                    ) PronostiqueursEtMatches
    LEFT JOIN       (
                        SELECT      pronostiqueurs.Pronostiqueur, scores.Matches_Match
                        FROM        pronostiqueurs
                        LEFT JOIN   scores
                                    ON      pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
                        JOIN        matches
                                    ON      scores.Matches_Match = matches.Match
                        WHERE       matches.Journees_Journee = p_Journee
                    ) PronostiqueursEtScores
                    ON      PronostiqueursEtMatches.Pronostiqueur = PronostiqueursEtScores.Pronostiqueur
                            AND     PronostiqueursEtMatches.Match = PronostiqueursEtScores.Matches_Match
    WHERE           PronostiqueursEtScores.Matches_Match IS NULL;

    DELETE          scores
    FROM            scores
    LEFT JOIN       matches
                    ON      scores.Matches_Match = matches.Match
    LEFT JOIN       journees
                    ON      matches.Journees_Journee = journees.Journee
    LEFT JOIN       inscriptions
                    ON      scores.Pronostiqueurs_Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
                            AND     journees.Championnats_Championnat = inscriptions.Championnats_Championnat
    WHERE           inscriptions.Pronostiqueurs_Pronostiqueur IS NULL
                    AND     journees.Journee = p_Journee;
    UPDATE          scores
    JOIN            matches
                    ON      scores.Matches_Match = matches.Match
    JOIN            journees
                    ON      matches.Journees_Journee = journees.Journee
    JOIN            pronostics
                    ON      scores.Matches_Match = pronostics.Matches_Match
                    AND     scores.Pronostiqueurs_Pronostiqueur = pronostics.Pronostiqueurs_Pronostiqueur
    LEFT JOIN       matches MatchesAller
                    ON      matches.Matches_MatchLie = MatchesAller.Match
    LEFT JOIN       pronostics PronosticsAller
                    ON      matches.Matches_MatchLie = PronosticsAller.Matches_Match
                    AND     pronostics.Pronostiqueurs_Pronostiqueur = PronosticsAller.Pronostiqueurs_Pronostiqueur
    LEFT JOIN       journees_pronostiqueurs_canal
                    ON      matches.Journees_Journee = journees_pronostiqueurs_canal.Journees_Journee
                            AND     pronostics.Pronostiqueurs_Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur
    SET             Scores_ScoreMatch =
                        CASE
                            WHEN    IFNULL(matches.Matches_MatchHorsPronostic, 0) = 1
                            THEN    0
                            ELSE    fn_calculscorematch(
                                        CASE
                                            WHEN    matches.Matches_MatchCS = 1
                                            THEN    5
                                            WHEN    matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) = 0
                                            THEN    1
                                            WHEN    matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) <> 0
                                            THEN    2
                                            WHEN    matches.Matches_AvecProlongation = 1 AND IFNULL(matches.Matches_MatchLie, 0) <> 0
                                            THEN    3
                                            ELSE    4
                                        END,
                                        matches.Matches_ScoreEquipeDomicile,
                                        matches.Matches_ScoreEquipeVisiteur,
                                        matches.Matches_ScoreAPEquipeDomicile,
                                        matches.Matches_ScoreAPEquipeVisiteur,
                                        matches.Matches_AvecProlongation,
                                        matches.Matches_Vainqueur,
                                        matches.Matches_MatchCS,
                                        matches.Matches_MatchLie,
                                        matches.Matches_CoteEquipeDomicile,
                                        matches.Matches_CoteNul,
                                        matches.Matches_CoteEquipeVisiteur,
                                        CASE
                                            WHEN    matches.Match = journees_pronostiqueurs_canal.Matches_Match
                                            THEN    2
                                            ELSE    1
                                        END  ,
                                        pronostics.Pronostics_ScoreEquipeDomicile,
                                        pronostics.Pronostics_ScoreEquipeVisiteur,
                                        pronostics.Pronostics_ScoreAPEquipeDomicile,
                                        pronostics.Pronostics_ScoreAPEquipeVisiteur,
                                        pronostics.Pronostics_Vainqueur
                                    )
                        END
                    ,Scores_ScoreBonus =
                        CASE
                            WHEN    IFNULL(matches.Matches_MatchHorsPronostic, 0) = 1
                            THEN    0
                            ELSE    fn_calculscorebonus(
                                        scores.Matches_Match,
                                        CASE
                                            WHEN    matches.Matches_MatchCS = 1
                                            THEN    5
                                            WHEN    matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) = 0
                                            THEN    1
                                            WHEN    matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) <> 0
                                            THEN    2
                                            WHEN    matches.Matches_AvecProlongation = 1 AND IFNULL(matches.Matches_MatchLie, 0) <> 0
                                            THEN    3
                                            ELSE    4
                                        END,
                                        matches.Matches_ScoreEquipeDomicile,
                                        matches.Matches_ScoreEquipeVisiteur,
                                        matches.Matches_ScoreAPEquipeDomicile,
                                        matches.Matches_ScoreAPEquipeVisiteur,
                                        matches.Matches_AvecProlongation,
                                        matches.Matches_Vainqueur,
                                        matches.Matches_MatchCS,
                                        matches.Matches_MatchLie,
                                        matches.Matches_CoteEquipeDomicile,
                                        matches.Matches_CoteNul,
                                        matches.Matches_CoteEquipeVisiteur,
                                        CASE
                                            WHEN    matches.Match = journees_pronostiqueurs_canal.Matches_Match
                                            THEN    2
                                            ELSE    1
                                        END  ,
                                        matches.Matches_PointsQualificationEquipeDomicile,
                                        matches.Matches_PointsQualificationEquipeVisiteur,
                                        matches.Matches_FinaleEuropeenne,
                                        pronostics.Pronostics_ScoreEquipeDomicile,
                                        pronostics.Pronostics_ScoreEquipeVisiteur,
                                        pronostics.Pronostics_ScoreAPEquipeDomicile,
                                        pronostics.Pronostics_ScoreAPEquipeVisiteur,
                                        pronostics.Pronostics_Vainqueur,
                                        MatchesAller.Matches_ScoreEquipeDomicile,
                                        MatchesAller.Matches_ScoreEquipeVisiteur,
                                        PronosticsAller.Pronostics_ScoreEquipeDomicile,
                                        PronosticsAller.Pronostics_ScoreEquipeVisiteur,
                                        CASE
                                            WHEN    journees.Championnats_Championnat = 5
                                            THEN    1
                                            ELSE    0
                                        END
                                    )
                        END
                    ,Scores_ScoreQualification =
                        CASE
                            WHEN    IFNULL(matches.Matches_MatchHorsPronostic, 0) = 1
                            THEN    0
                            ELSE    fn_calculpointsqualification(
                                        CASE
                                            WHEN    matches.Matches_MatchCS = 1
                                            THEN    5
                                            WHEN    matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) = 0
                                            THEN    1
                                            WHEN    matches.Matches_AvecProlongation = 0 AND IFNULL(matches.Matches_MatchLie, 0) <> 0
                                            THEN    2
                                            WHEN    matches.Matches_AvecProlongation = 1 AND IFNULL(matches.Matches_MatchLie, 0) <> 0
                                            THEN    3
                                            ELSE    4
                                        END,
                                        matches.Matches_ScoreEquipeDomicile,
                                        matches.Matches_ScoreEquipeVisiteur,
                                        matches.Matches_ScoreAPEquipeDomicile,
                                        matches.Matches_ScoreAPEquipeVisiteur,
                                        matches.Matches_Vainqueur,
                                        CASE
                                            WHEN    matches.Match = journees_pronostiqueurs_canal.Matches_Match
                                            THEN    2
                                            ELSE    1
                                        END  ,
                                        matches.Matches_PointsQualificationEquipeDomicile,
                                        matches.Matches_PointsQualificationEquipeVisiteur,
                                        matches.Matches_CoteEquipeDomicile,
                                        matches.Matches_CoteEquipeVisiteur,
                                        matches.Matches_FinaleEuropeenne,
                                        pronostics.Pronostics_ScoreEquipeDomicile,
                                        pronostics.Pronostics_ScoreEquipeVisiteur,
                                        pronostics.Pronostics_ScoreAPEquipeDomicile,
                                        pronostics.Pronostics_ScoreAPEquipeVisiteur,
                                        pronostics.Pronostics_Vainqueur,
                                        MatchesAller.Matches_ScoreEquipeDomicile,
                                        MatchesAller.Matches_ScoreEquipeVisiteur,
                                        PronosticsAller.Pronostics_ScoreEquipeDomicile,
                                        PronosticsAller.Pronostics_ScoreEquipeVisiteur
                                    )
                        END
    WHERE           matches.Matches_ScoreEquipeDomicile IS NOT NULL
                    AND     matches.Matches_ScoreEquipeVisiteur IS NOT NULL
                    AND     pronostics.Pronostics_ScoreEquipeDomicile IS NOT NULL
                    AND     pronostics.Pronostics_ScoreEquipeVisiteur IS NOT NULL
                    AND     Journee = p_Journee;
    UPDATE          scores
    JOIN            matches
                    ON      scores.Matches_Match = matches.Match
    JOIN            journees
                    ON      matches.Journees_Journee = journees.Journee
    JOIN            pronostics
                    ON      scores.Matches_Match = pronostics.Matches_Match
                    AND     scores.Pronostiqueurs_Pronostiqueur = pronostics.Pronostiqueurs_Pronostiqueur
    LEFT JOIN       journees_pronostiqueurs_canal
                    ON      matches.Journees_Journee = journees_pronostiqueurs_canal.Journees_Journee
                            AND     pronostics.Pronostiqueurs_Pronostiqueur = journees_pronostiqueurs_canal.Pronostiqueurs_Pronostiqueur
    SET             Scores_ScoreButeur =
                        CASE
                            WHEN    IFNULL(matches.Matches_MatchHorsPronostic, 0) = 1
                            THEN    0
                            ELSE    fn_calculscorebuteur(
                                                            scores.Matches_Match,
                                                            scores.Pronostiqueurs_Pronostiqueur,
                                                            CASE
                                                                WHEN    matches.Match = journees_pronostiqueurs_canal.Matches_Match
                                                                THEN    2
                                                                ELSE    1
                                                            END  ,
                                                            IFNULL(Scores_ScoreMatch, 0) + IFNULL(Scores_ScoreBonus, 0)
                                    )
                        END
    WHERE           Matches_ScoreEquipeDomicile IS NOT NULL
                    AND     Matches_ScoreEquipeVisiteur IS NOT NULL
                    AND     Journee = p_Journee;

    
     IF l_Championnat = 1 AND l_JourneeNumeroInterne = 34 THEN
        TRUNCATE TABLE  pronostics_barragesl1;
        INSERT INTO     pronostics_barragesl1(Matches_Match, PronosticsBarrageL1_Coefficient, Pronostiqueurs_Pronostiqueur)
        SELECT          matches.Match,
                        CASE
                            WHEN        pronostics_bonus.Pronostiqueurs_Pronostiqueur IS NULL
                            THEN        0
                            ELSE        1
                        END AS PronosticsBarrageL1_Coefficient,
                        inscriptions.Pronostiqueurs_Pronostiqueur
        FROM            matches
        JOIN            journees
                        ON      matches.Journees_Journee = journees.Journee
        JOIN            inscriptions
                        ON      journees.Championnats_Championnat = inscriptions.Championnats_Championnat
        JOIN            classements_equipes
        LEFT JOIN       pronostics_bonus
                        ON      inscriptions.Pronostiqueurs_Pronostiqueur = pronostics_bonus.Pronostiqueurs_Pronostiqueur
                                AND     classements_equipes.Equipes_Equipe = pronostics_bonus.PronosticsBonus_EquipeReleguee1
        WHERE           journees.Championnats_Championnat = 1
                        AND     journees.Journees_NumeroInterne = 35
                        AND     classements_equipes.ClassementsEquipes_Classement = 16;
    END IF;

    
    

    
    IF  (l_Championnat = 2 AND l_JourneeNumeroInterne = 8) OR
        (l_Championnat = 3 AND l_JourneeNumeroInterne = 8)
        l_Championnat = 4 THEN
        CALL sp_calculpointsqualifications_coupe_europeenne(l_Championnat);
    END IF;

    IF  l_Championnat = 4 THEN
        CALL sp_calculpointsqualifications(l_Championnat);
    END IF;

    IF  (l_Championnat = 2 AND l_JourneeNumeroInterne >= 9) OR
        (l_Championnat = 3 AND l_JourneeNumeroInterne >= 9) THEN

        IF  (l_Championnat = 2 AND l_JourneeNumeroInterne = 10) OR
            (l_Championnat = 3 AND l_JourneeNumeroInterne = 10) THEN
            CALL sp_calculcarrefinal(p_Journee + 1, 0);
        ELSEIF  (l_Championnat = 2 AND l_JourneeNumeroInterne = 11) OR
                (l_Championnat = 3 AND l_JourneeNumeroInterne = 11) THEN
    		CALL sp_calculcarrefinal(p_Journee + 1, 1);
		END IF;

        IF  (l_Championnat = 2 AND l_JourneeNumeroInterne >= 11) OR
            (l_Championnat = 3 AND l_JourneeNumeroInterne >= 11) THEN
			UPDATE      scores
			JOIN        pronostics_carrefinal
						ON      scores.Matches_Match = pronostics_carrefinal.Matches_Match
								AND     scores.Pronostiqueurs_Pronostiqueur = pronostics_carrefinal.Pronostiqueurs_Pronostiqueur
			JOIN        matches
						ON      pronostics_carrefinal.Matches_Match = matches.Match
			SET         Scores_ScoreCarreFinalCoefficient = pronostics_carrefinal.PronosticsCarreFinal_Coefficient
			WHERE       matches.Journees_Journee = p_Journee;
		END IF;
	END IF;

    IF(l_Championnat <> 5) THEN
        CALL sp_calcultouslesclassements(p_Journee);
        CALL sp_calcultrophees(p_Journee);
    	CALL sp_calculclassementdelta(p_Journee);
    ELSE
        
        CALL sp_calculvainqueursconfrontations(p_Journee);
    END IF;

    SELECT  NOW()
    INTO    l_Date;

    UPDATE      journees
    SET         Journees_DateMAJ = l_Date
    WHERE       Journee = p_Journee;

    UPDATE      modules
    JOIN        journees
                ON      modules.Modules_Parametre = journees.Championnats_Championnat
    SET         Modules_CritereRafraichissement = l_Date
    WHERE       journees.Journee = p_Journee
                AND     Modules_Type IN (1, 2);

    IF l_Championnat = 1 AND l_JourneeNumeroInterne <= 34 THEN
        CALL sp_calculclassementvirtuelequipes();
    END IF;
END$$
DELIMITER ;