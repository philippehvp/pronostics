UPDATE		pronostics
JOIN		matches
			ON		pronostics.Matches_Match = matches.Match
JOIN		journees
			ON		matches.Journees_Journee = journees.Journee
SET			Pronostics_ScoreAPEquipeDomicile = NULL
			,Pronostics_ScoreAPEquipeVisiteur = NULL
			,Pronostics_Vainqueur = NULL
WHERE		Pronostics_ScoreEquipeDomicile <> Pronostics_ScoreEquipeVisiteur
			AND		(	Pronostics_ScoreAPEquipeDomicile IS NOT NULL
						OR		Pronostics_ScoreAPEquipeVisiteur IS NOT NULL
						OR		Pronostics_Vainqueur IS NOT NULL
					)
			AND		journees.Championnats_Championnat = 1;