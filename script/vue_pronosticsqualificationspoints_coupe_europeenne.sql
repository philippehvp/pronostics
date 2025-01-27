SELECT          pronostics_phase.Pronostiqueurs_Pronostiqueur,
                pronostics_phase.Championnats_Championnat,
                SUM(pronostics_phase.PronosticsPhase_Points) AS PronosticsQualificationsPoints_Points
FROM            pronostics_phase
GROUP BY        pronostics_phase.Pronostiqueurs_Pronostiqueur,
                pronostics_phase.Championnats_Championnat 