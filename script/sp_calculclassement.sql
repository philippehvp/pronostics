DROP PROCEDURE IF EXISTS sp_calculclassement;
DELIMITER $$
CREATE DEFINER=`lepoulpeg`@`%` PROCEDURE `sp_calculclassement`(IN `p_Journee` INT, IN `p_DateReference` DATE)
BEGIN
    
	DECLARE l_DateReference DATETIME;
    DECLARE l_Championnat INT;
    DECLARE l_JourneeNumeroInterne INT;
    DECLARE l_DerniereJournee INT;

    SELECT      journees.Journees_NumeroInterne, journees.Championnats_Championnat
    INTO        l_JourneeNumeroInterne, l_Championnat
    FROM        journees
    WHERE       Journee = p_Journee;

    SELECT      championnats.Championnats_DerniereJournee
    INTO        l_DerniereJournee
    FROM        championnats
    WHERE       championnats.Championnat = l_Championnat;

	IF p_DateReference IS NULL THEN
		SELECT		MAX(Matches_Date) AS Matches_Date
		INTO		l_DateReference
		FROM		matches
		WHERE		Journees_Journee = p_Journee
					AND		matches.Matches_Date <= NOW();
	ELSE
		SET l_DateReference = p_DateReference;
	END IF;

	IF p_DateReference IS NOT NULL THEN
		DELETE FROM classements WHERE Journees_Journee = p_Journee AND Classements_DateReference = p_DateReference;
	ELSE
        DELETE FROM classements WHERE Journees_Journee = p_Journee;
	END IF;

    INSERT INTO	classements(Pronostiqueurs_Pronostiqueur, Journees_Journee, Classements_DateReference)
    SELECT		  DISTINCT Pronostiqueurs_Pronostiqueur, p_Journee, l_DateReference
    FROM		    inscriptions
    WHERE		    inscriptions.Championnats_Championnat = l_Championnat;

	TRUNCATE TABLE classements_points_differents;

	INSERT INTO	classements_points_differents(Classements_Points, Classements_PointsSecondaires, Classements_Nombre)
	SELECT		classement.Scores_ScoreTotal, classement.Scores_ScoreButeur, COUNT(*) AS Nombre
	FROM		(
					SELECT		SUM(IFNULL(Scores_ScoreMatch * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ SUM(IFNULL(Scores_ScoreBonus * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ CASE
										  WHEN	(l_Championnat = 2 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                (l_Championnat = 3 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                (l_Championnat = 4 AND l_JourneeNumeroInterne = 1) OR
												(l_Championnat = 1 AND l_JourneeNumeroInterne = 35)
										  THEN	SUM(IFNULL(Scores_ScoreQualification * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
										  ELSE	0
									  END
									+ IFNULL(JourneesRattrapage_Points, 0)
									+ CASE
										WHEN	(l_Championnat = 2 AND l_JourneeNumeroInterne IN (8, 9, 10, 11, 12)) OR
                                                (l_Championnat = 3 AND l_JourneeNumeroInterne IN (8, 9, 10, 11, 12))
										THEN	IFNULL(vue_pronosticsqualificationspoints_coupe_europeenne.PronosticsQualificationsPoints_Points, 0)
										ELSE	0
									  END
                                    + CASE
										WHEN	l_Championnat = 4 AND l_JourneeNumeroInterne = 1
										THEN	IFNULL(vue_pronosticsqualificationspoints.PronosticsQualificationsPoints_Points, 0)
										ELSE	0
									  END
									+ CASE
										WHEN	(l_Championnat = 1 AND l_JourneeNumeroInterne < l_DerniereJournee)
  										THEN	IFNULL(vue_pronosticsbonuspointsanticipes.PronosticsBonusPointsAnticipes_Points, 0)
	  									WHEN	(l_Championnat = 1 AND l_JourneeNumeroInterne >= l_DerniereJournee)
		  								THEN	IFNULL(vue_pronosticsbonuspoints.PronosticsBonusPoints_Points, 0)
			  							ELSE	0
				  					END
									+ IFNULL(ScoresForces_PointsGeneralMatch, 0)
								AS Scores_ScoreTotal
								,SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ IFNULL(ScoresForces_PointsGeneralButeur, 0)
								AS Scores_ScoreButeur
					FROM		pronostiqueurs
					JOIN		inscriptions
								ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
										AND		inscriptions.Championnats_Championnat = l_Championnat
					LEFT JOIN	scores
								ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
					JOIN		matches
								ON		scores.Matches_Match = matches.Match
					JOIN		journees
								ON		matches.Journees_Journee = journees.Journee
					LEFT JOIN	(
									SELECT		Pronostiqueurs_Pronostiqueur, SUM(JourneesRattrapage_Points) AS JourneesRattrapage_Points
									FROM		journees_rattrapage
									JOIN		journees
												ON		journees_rattrapage.Journees_Journee = journees.Journee
														AND		journees.Championnats_Championnat = l_Championnat
									JOIN		(
													SELECT		DISTINCT Journees_Journee
													FROM		classements
													JOIN		journees
																ON		classements.Journees_Journee = journees.Journee
													WHERE		journees.Championnats_Championnat = l_Championnat
																AND		Classements_DateReference <= l_DateReference
												) journees_integrees
												ON		journees_rattrapage.Journees_Journee = journees_integrees.Journees_Journee
									GROUP BY	Pronostiqueurs_Pronostiqueur
								) journees_rattrapage
								ON		scores.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur
					LEFT JOIN	vue_pronosticsqualificationspoints
								ON		pronostiqueurs.Pronostiqueur = vue_pronosticsqualificationspoints.Pronostiqueurs_Pronostiqueur
										AND		journees.Championnats_Championnat = vue_pronosticsqualificationspoints.Championnats_Championnat
					LEFT JOIN	vue_pronosticsbonuspoints
								ON		pronostiqueurs.Pronostiqueur = vue_pronosticsbonuspoints.Pronostiqueurs_Pronostiqueur
										AND		journees.Championnats_Championnat = vue_pronosticsbonuspoints.Championnats_Championnat
					LEFT JOIN	vue_pronosticsbonuspointsanticipes
								ON		pronostiqueurs.Pronostiqueur = vue_pronosticsbonuspointsanticipes.Pronostiqueurs_Pronostiqueur
										AND		journees.Championnats_Championnat = vue_pronosticsbonuspointsanticipes.Championnats_Championnat
					LEFT JOIN	scores_forces
								ON		pronostiqueurs.Pronostiqueur = scores_forces.Pronostiqueurs_Pronostiqueur
										AND		scores_forces.Championnats_Championnat = l_Championnat
										AND		scores_forces.ScoresForces_DateDebut <= l_DateReference
					WHERE		matches.Matches_Date <= l_DateReference
								AND		journees.Championnats_Championnat = l_Championnat
								AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= l_DateReference
								AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > l_DateReference)
					GROUP BY	pronostiqueurs.Pronostiqueur
				) classement
	GROUP BY	Scores_ScoreTotal, Scores_ScoreButeur;

	UPDATE	classements
	JOIN		(
					SELECT	classement.Pronostiqueur, classement.Scores_ScoreTotal, classement.Scores_ScoreButeur
					FROM		(
  									SELECT		Pronostiqueur
      												,SUM(IFNULL(Scores_ScoreMatch * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
      													+ SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
      													+ SUM(IFNULL(Scores_ScoreBonus * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
      													+ CASE
      														WHEN	(l_Championnat = 2 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                                    (l_Championnat = 3 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                                    (l_Championnat = 4 AND l_JourneeNumeroInterne = 1) OR
																	(l_Championnat = 1 AND l_JourneeNumeroInterne = 35)
      														THEN	SUM(IFNULL(Scores_ScoreQualification * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
      														ELSE	0
      													END
      													+ IFNULL(JourneesRattrapage_Points, 0)
      													+ CASE
                                                            WHEN	(l_Championnat = 2 AND l_JourneeNumeroInterne IN (8, 9, 10, 11, 12)) OR
                                                                    (l_Championnat = 3 AND l_JourneeNumeroInterne IN (8, 9, 10, 11, 12))
                                                            THEN	IFNULL(vue_pronosticsqualificationspoints_coupe_europeenne.PronosticsQualificationsPoints_Points, 0)
                                                            ELSE	0
                                                        END
                                                        + CASE
                                                            WHEN	l_Championnat = 4 AND l_JourneeNumeroInterne = 1
                                                            THEN	IFNULL(vue_pronosticsqualificationspoints.PronosticsQualificationsPoints_Points, 0)
                                                            ELSE	0
                                                        END
      													+ CASE
      														WHEN	(l_Championnat = 1 AND l_JourneeNumeroInterne < l_DerniereJournee)
      														THEN	IFNULL(vue_pronosticsbonuspointsanticipes.PronosticsBonusPointsAnticipes_Points, 0)
      														WHEN	(l_Championnat = 1 AND l_JourneeNumeroInterne >= l_DerniereJournee)
      														THEN	IFNULL(vue_pronosticsbonuspoints.PronosticsBonusPoints_Points, 0)
      														ELSE	0
      													END
      													+ IFNULL(ScoresForces_PointsGeneralMatch, 0)
      												AS Scores_ScoreTotal
      												,SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
      													+ IFNULL(ScoresForces_PointsGeneralButeur, 0)
      												AS Scores_ScoreButeur
  									FROM		  pronostiqueurs
  									JOIN		  inscriptions
  												    ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
  													    	  AND		inscriptions.Championnats_Championnat = l_Championnat
  									LEFT JOIN	scores
  												    ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
  									JOIN		  matches
  												    ON		scores.Matches_Match = matches.Match
  									JOIN		  journees
  												    ON		matches.Journees_Journee = journees.Journee
  									LEFT JOIN	(
      													SELECT		Pronostiqueurs_Pronostiqueur, SUM(JourneesRattrapage_Points) AS JourneesRattrapage_Points
      													FROM		journees_rattrapage
      													JOIN		journees
      																ON		journees_rattrapage.Journees_Journee = journees.Journee
      																		AND		journees.Championnats_Championnat = l_Championnat
      													JOIN		(
      																	SELECT		DISTINCT Journees_Journee
      																	FROM		classements
      																	JOIN		journees
      																				ON		classements.Journees_Journee = journees.Journee
      																	WHERE		journees.Championnats_Championnat = l_Championnat
      																				AND		Classements_DateReference <= l_DateReference
      																) journees_integrees
      																ON		journees_rattrapage.Journees_Journee = journees_integrees.Journees_Journee
      													GROUP BY	Pronostiqueurs_Pronostiqueur
      												) journees_rattrapage
      												ON		scores.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur
  									LEFT JOIN	vue_pronosticsqualificationspoints
  	    											ON		pronostiqueurs.Pronostiqueur = vue_pronosticsqualificationspoints.Pronostiqueurs_Pronostiqueur
  			      											AND		journees.Championnats_Championnat = vue_pronosticsqualificationspoints.Championnats_Championnat
  									LEFT JOIN	vue_pronosticsbonuspoints
  						    						ON		pronostiqueurs.Pronostiqueur = vue_pronosticsbonuspoints.Pronostiqueurs_Pronostiqueur
  								      						AND		journees.Championnats_Championnat = vue_pronosticsbonuspoints.Championnats_Championnat
  									LEFT JOIN	vue_pronosticsbonuspointsanticipes
  											    	ON		pronostiqueurs.Pronostiqueur = vue_pronosticsbonuspointsanticipes.Pronostiqueurs_Pronostiqueur
  													      	AND		journees.Championnats_Championnat = vue_pronosticsbonuspointsanticipes.Championnats_Championnat
  									LEFT JOIN	scores_forces
  							    					ON		pronostiqueurs.Pronostiqueur = scores_forces.Pronostiqueurs_Pronostiqueur
  									      					AND		scores_forces.Championnats_Championnat = l_Championnat
  												      		AND		scores_forces.ScoresForces_DateDebut <= l_DateReference
  									WHERE	  	matches.Matches_Date <= l_DateReference
      												AND		journees.Championnats_Championnat = l_Championnat
      												AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= l_DateReference
      												AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > l_DateReference)
  									GROUP BY	pronostiqueurs.Pronostiqueur
  								) classement
				) ClassementGeneralMatch
				ON		classements.Pronostiqueurs_Pronostiqueur = ClassementGeneralMatch.Pronostiqueur
						AND		classements.Journees_Journee = p_Journee
						AND		classements.Classements_DateReference = l_DateReference
	SET		classements.Classements_PointsGeneralMatch = ClassementGeneralMatch.Scores_ScoreTotal
				,classements.Classements_PointsGeneralButeur = ClassementGeneralMatch.Scores_ScoreButeur;

    UPDATE		classements
	JOIN		(
					SELECT		COUNT(c1.Pronostiqueurs_Pronostiqueur) AS Classement, c1.Journees_Journee, c1.Pronostiqueurs_Pronostiqueur, c1.Classements_PointsGeneralMatch, c1.Classements_PointsGeneralButeur
					FROM		classements AS c1
					JOIN		classements AS c2
								ON		(
											c1.Classements_PointsGeneralMatch < c2.Classements_PointsGeneralMatch
											OR
											(
												c1.Classements_PointsGeneralMatch = c2.Classements_PointsGeneralMatch
												AND		c1.Classements_PointsGeneralButeur >= c2.Classements_PointsGeneralButeur
											)
										)
										AND		c1.Journees_Journee = c2.Journees_Journee
										AND		c1.Classements_DateReference = c2.Classements_DateReference
					WHERE		c1.Journees_Journee = p_Journee
								AND		c1.Classements_DateReference = l_DateReference
					GROUP BY	c1.Pronostiqueurs_Pronostiqueur
				) classements_recalcules
				ON		classements.Pronostiqueurs_Pronostiqueur = classements_recalcules.Pronostiqueurs_Pronostiqueur
						AND		classements.Journees_Journee = classements_recalcules.Journees_Journee
						AND		classements.Classements_DateReference = l_DateReference
	JOIN		classements_points_differents
				ON		classements.Classements_PointsGeneralMatch = classements_points_differents.Classements_Points
						AND		classements.Classements_PointsGeneralButeur = classements_points_differents.Classements_PointsSecondaires
	SET			classements.Classements_ClassementGeneralMatch = classements_recalcules.Classement - classements_points_differents.Classements_Nombre + 1;

    TRUNCATE TABLE classements_points_differents;

	INSERT INTO	classements_points_differents(Classements_Points, Classements_Nombre)
	SELECT	classement.Scores_ScoreButeur, COUNT(*)
	FROM		(
  					SELECT		SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
  									  + IFNULL(ScoresForces_PointsGeneralButeur, 0)
  								    AS Scores_ScoreButeur
  					FROM		  pronostiqueurs
  					JOIN		  inscriptions
  								    ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
  									      AND		inscriptions.Championnats_Championnat = l_Championnat
  					LEFT JOIN	scores
  								    ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
  					JOIN		  matches
  								    ON		scores.Matches_Match = matches.Match
  					JOIN		  journees
  								    ON		matches.Journees_Journee = journees.Journee
  					LEFT JOIN	journees_rattrapage
  								    ON		scores.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur
  								          AND		journees.Journee = journees_rattrapage.Journees_Journee
  					LEFT JOIN	vue_pronosticsqualificationspoints
  								    ON		pronostiqueurs.Pronostiqueur = vue_pronosticsqualificationspoints.Pronostiqueurs_Pronostiqueur
  										      AND		journees.Championnats_Championnat = vue_pronosticsqualificationspoints.Championnats_Championnat
  					LEFT JOIN	vue_pronosticsbonuspoints
  								    ON		pronostiqueurs.Pronostiqueur = vue_pronosticsbonuspoints.Pronostiqueurs_Pronostiqueur
  										      AND		journees.Championnats_Championnat = vue_pronosticsbonuspoints.Championnats_Championnat
  					LEFT JOIN	scores_forces
  								    ON		pronostiqueurs.Pronostiqueur = scores_forces.Pronostiqueurs_Pronostiqueur
  										      AND		scores_forces.Championnats_Championnat = l_Championnat
  										      AND		scores_forces.ScoresForces_DateDebut <= l_DateReference
  					WHERE		  matches.Matches_Date <= l_DateReference
      								AND		journees.Championnats_Championnat = l_Championnat
      								AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= l_DateReference
      								AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > l_DateReference)
  					GROUP BY	pronostiqueurs.Pronostiqueur
				) classement
	GROUP BY	Scores_ScoreButeur;

	UPDATE	classements
	JOIN		(
					SELECT		COUNT(c1.Pronostiqueurs_Pronostiqueur) AS Classement, c1.Journees_Journee, c1.Pronostiqueurs_Pronostiqueur, c1.Classements_PointsGeneralButeur
					FROM		classements AS c1
					JOIN		classements AS c2
								ON		c1.Classements_PointsGeneralButeur <= c2.Classements_PointsGeneralButeur
										AND		c1.Journees_Journee = c2.Journees_Journee
										AND		c1.Classements_DateReference = c2.Classements_DateReference
					WHERE		c1.Journees_Journee = p_Journee
								AND		c1.Classements_DateReference = l_DateReference
					GROUP BY	c1.Pronostiqueurs_Pronostiqueur
				) classements_recalcules
				ON		classements.Pronostiqueurs_Pronostiqueur = classements_recalcules.Pronostiqueurs_Pronostiqueur
						AND		classements.Journees_Journee = classements_recalcules.Journees_Journee
						AND		classements.Classements_DateReference = l_DateReference
	JOIN		classements_points_differents
				ON		classements.Classements_PointsGeneralButeur = classements_points_differents.Classements_Points
	SET			classements.Classements_ClassementGeneralButeur = classements_recalcules.Classement - classements_points_differents.Classements_Nombre + 1;

	TRUNCATE TABLE classements_points_differents;

	INSERT INTO	classements_points_differents(Classements_Points, Classements_PointsSecondaires, Classements_Nombre)
	SELECT		classement.Scores_ScoreTotal, classement.Scores_ScoreButeur, COUNT(*)
	FROM		(
					SELECT		SUM(IFNULL(Scores_ScoreMatch * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ SUM(IFNULL(Scores_ScoreBonus * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
									+ CASE
										WHEN	(l_Championnat = 2 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                (l_Championnat = 3 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                (l_Championnat = 4 AND l_JourneeNumeroInterne = 1) OR
												(l_Championnat = 1 AND l_JourneeNumeroInterne = 35)
										THEN	SUM(IFNULL(Scores_ScoreQualification * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
										ELSE	0
									END
									+ IFNULL(JourneesRattrapage_Points, 0)
								AS Scores_ScoreTotal
								,SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0)) AS Scores_ScoreButeur
					FROM		pronostiqueurs
					JOIN		inscriptions
								ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
										AND		inscriptions.Championnats_Championnat = l_Championnat
					LEFT JOIN	scores
								ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
					JOIN		matches
								ON		scores.Matches_Match = matches.Match
					JOIN		journees
								ON		matches.Journees_Journee = journees.Journee
					LEFT JOIN	(
									SELECT		Pronostiqueurs_Pronostiqueur, SUM(JourneesRattrapage_Points) AS JourneesRattrapage_Points
									FROM		journees_rattrapage
									JOIN		journees
												ON		journees_rattrapage.Journees_Journee = journees.Journee
														AND		journees.Championnats_Championnat = l_Championnat
														AND		journees.Journee = p_Journee
									GROUP BY	Pronostiqueurs_Pronostiqueur
								) journees_rattrapage
								ON		scores.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur
					WHERE		matches.Matches_Date <= l_DateReference
								AND		journees.Journee = p_Journee
								AND		journees.Championnats_Championnat = l_Championnat
								AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= l_DateReference
								AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > l_DateReference)
					GROUP BY	pronostiqueurs.Pronostiqueur
				) classement
	GROUP BY	Scores_ScoreTotal, Scores_ScoreButeur;

    UPDATE		classements
	JOIN		(
					SELECT		classement.Pronostiqueur, classement.Scores_ScoreTotal, classement.Scores_ScoreButeur
					FROM		(
									SELECT		Pronostiqueur
												,SUM(IFNULL(Scores_ScoreMatch * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
													+ SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
													+ SUM(IFNULL(Scores_ScoreBonus * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
													+ CASE
														WHEN	(l_Championnat = 2 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                                (l_Championnat = 3 AND l_JourneeNumeroInterne IN (9, 10, 11, 12)) OR
                                                                (l_Championnat = 4 AND l_JourneeNumeroInterne = 1) OR
																(l_Championnat = 1 AND l_JourneeNumeroInterne = 35)
														THEN	SUM(IFNULL(Scores_ScoreQualification * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0))
														ELSE	0
													  END
													+ IFNULL(JourneesRattrapage_Points, 0)
												AS Scores_ScoreTotal
												,SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0)) AS Scores_ScoreButeur
									FROM		pronostiqueurs
									JOIN		inscriptions
												ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
														AND		inscriptions.Championnats_Championnat = l_Championnat
									LEFT JOIN	scores
												ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
									JOIN		matches
												ON		scores.Matches_Match = matches.Match
									JOIN		journees
												ON		matches.Journees_Journee = journees.Journee
									LEFT JOIN	(
													SELECT		Pronostiqueurs_Pronostiqueur, SUM(JourneesRattrapage_Points) AS JourneesRattrapage_Points
													FROM		journees_rattrapage
													JOIN		journees
																ON		journees_rattrapage.Journees_Journee = journees.Journee
																		AND		journees.Championnats_Championnat = l_Championnat
																		AND		journees.Journee = p_Journee
													GROUP BY	Pronostiqueurs_Pronostiqueur
												) journees_rattrapage
												ON		scores.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur
									WHERE		matches.Matches_Date <= l_DateReference
												AND		journees.Journee = p_Journee
												AND		journees.Championnats_Championnat = l_Championnat
												AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= l_DateReference
												AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > l_DateReference)
									GROUP BY	pronostiqueurs.Pronostiqueur
								) classement
				) ClassementJourneeMatch
				ON		classements.Pronostiqueurs_Pronostiqueur = ClassementJourneeMatch.Pronostiqueur
	SET			classements.Classements_PointsJourneeMatch = ClassementJourneeMatch.Scores_ScoreTotal
				,classements.Classements_PointsJourneeButeur = ClassementJourneeMatch.Scores_ScoreButeur
	WHERE		classements.Journees_Journee = p_Journee
				AND		classements.Classements_DateReference = l_DateReference;
    UPDATE		classements
	JOIN		(
					SELECT		COUNT(c1.Pronostiqueurs_Pronostiqueur) AS Classement, c1.Journees_Journee, c1.Pronostiqueurs_Pronostiqueur, c1.Classements_PointsJourneeMatch, c1.Classements_PointsJourneeButeur
					FROM		classements AS c1
					JOIN		classements AS c2
								ON		(
											c1.Classements_PointsJourneeMatch < c2.Classements_PointsJourneeMatch
											OR
											(
												c1.Classements_PointsJourneeMatch = c2.Classements_PointsJourneeMatch
												AND		c1.Classements_PointsJourneeButeur >= c2.Classements_PointsJourneeButeur
											)
										)
										AND		c1.Journees_Journee = c2.Journees_Journee
										AND		c1.Classements_DateReference = c2.Classements_DateReference
					WHERE		c1.Journees_Journee = p_Journee
								AND		c1.Classements_DateReference = l_DateReference
					GROUP BY	c1.Pronostiqueurs_Pronostiqueur
				) classements_recalcules
				ON		classements.Pronostiqueurs_Pronostiqueur = classements_recalcules.Pronostiqueurs_Pronostiqueur
						AND		classements.Journees_Journee = classements_recalcules.Journees_Journee
						AND		classements.Classements_DateReference = l_DateReference
	JOIN		classements_points_differents
				ON		classements.Classements_PointsJourneeMatch = classements_points_differents.Classements_Points
						AND		classements.Classements_PointsJourneeButeur = classements_points_differents.Classements_PointsSecondaires
	SET			classements.Classements_ClassementJourneeMatch = classements_recalcules.Classement - classements_points_differents.Classements_Nombre + 1;
	TRUNCATE TABLE classements_points_differents;
	INSERT INTO	classements_points_differents(Classements_Points, Classements_Nombre)
	SELECT		classement.Scores_ScoreButeur, COUNT(*)
	FROM		(
					SELECT		SUM(IFNULL(Scores_ScoreButeur * IFNULL(Scores_ScoreCarreFinalCoefficient, 1), 0)) AS Scores_ScoreButeur
					FROM		pronostiqueurs
					JOIN		inscriptions
								ON		pronostiqueurs.Pronostiqueur = inscriptions.Pronostiqueurs_Pronostiqueur
										AND		inscriptions.Championnats_Championnat = l_Championnat
					LEFT JOIN	scores
								ON		pronostiqueurs.Pronostiqueur = scores.Pronostiqueurs_Pronostiqueur
					JOIN		matches
								ON		scores.Matches_Match = matches.Match
					JOIN		journees
								ON		matches.Journees_Journee = journees.Journee
					LEFT JOIN	journees_rattrapage
								ON		scores.Pronostiqueurs_Pronostiqueur = journees_rattrapage.Pronostiqueurs_Pronostiqueur
								AND		journees.Journee = journees_rattrapage.Journees_Journee
					LEFT JOIN	vue_pronosticsqualificationspoints
								ON		pronostiqueurs.Pronostiqueur = vue_pronosticsqualificationspoints.Pronostiqueurs_Pronostiqueur
										AND		journees.Championnats_Championnat = vue_pronosticsqualificationspoints.Championnats_Championnat
					LEFT JOIN	vue_pronosticsbonuspoints
								ON		pronostiqueurs.Pronostiqueur = vue_pronosticsbonuspoints.Pronostiqueurs_Pronostiqueur
										AND		journees.Championnats_Championnat = vue_pronosticsbonuspoints.Championnats_Championnat
					WHERE		matches.Matches_Date <= l_DateReference
								AND		journees.Journee = p_Journee
								AND		journees.Championnats_Championnat = l_Championnat
								AND		pronostiqueurs.Pronostiqueurs_DateDebutPresence <= l_DateReference
								AND		(pronostiqueurs.Pronostiqueurs_DateFinPresence IS NULL OR pronostiqueurs.Pronostiqueurs_DateFinPresence > l_DateReference)
					GROUP BY	pronostiqueurs.Pronostiqueur
				) classement
	GROUP BY	Scores_ScoreButeur;
    UPDATE		classements
	JOIN		(
					SELECT		COUNT(c1.Pronostiqueurs_Pronostiqueur) AS Classement, c1.Journees_Journee, c1.Pronostiqueurs_Pronostiqueur, c1.Classements_PointsJourneeButeur
					FROM		classements AS c1
					JOIN		classements AS c2
								ON		c1.Classements_PointsJourneeButeur <= c2.Classements_PointsJourneeButeur
										AND		c1.Journees_Journee = c2.Journees_Journee
										AND		c1.Classements_DateReference = c2.Classements_DateReference
					WHERE		c1.Journees_Journee = p_Journee
								AND		c1.Classements_DateReference = l_DateReference
					GROUP BY	c1.Pronostiqueurs_Pronostiqueur
				) classements_recalcules
				ON		classements.Pronostiqueurs_Pronostiqueur = classements_recalcules.Pronostiqueurs_Pronostiqueur
						AND		classements.Journees_Journee = classements_recalcules.Journees_Journee
						AND		classements.Classements_DateReference = l_DateReference
	JOIN		classements_points_differents
				ON		classements.Classements_PointsJourneeButeur = classements_points_differents.Classements_Points
	SET			classements.Classements_ClassementJourneeButeur = classements_recalcules.Classement - classements_points_differents.Classements_Nombre + 1;
    CALL sp_calculclassement_sb(p_Journee, p_DateReference);
    IF	l_Championnat = 1 THEN
		CALL sp_calculclassementvirtuel(p_Journee, l_Championnat, l_DateReference);
	END IF;
END$$
DELIMITER ;