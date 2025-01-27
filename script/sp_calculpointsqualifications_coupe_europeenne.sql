DROP PROCEDURE IF EXISTS sp_calculpointsqualifications_coupe_europeenne;
DELIMITER $$
CREATE DEFINER=`lepoulpeg`@`%` PROCEDURE `sp_calculpointsqualifications_coupe_europeenne`(IN `p_Championnat` INT)
BEGIN
    /* sp_calculpointsqualifications_coupe_europeenne */

    /* Les équipes rapportent des points si elles finissent dans des "zones" dans lesquelles elles ne sont pas attendues */
    /* Equipes du chapeau 1 devant faire les barrages (20 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 1
                                AND     equipes_groupes.EquipesGroupes_Phase = 2
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 20;

    /* Equipes du chapeau 1 éliminée (40 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 1
                                AND     equipes_groupes.EquipesGroupes_Phase = 3
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 40;

    /* Equipes du chapeau 2 en phase finale (20 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 2
                                AND     equipes_groupes.EquipesGroupes_Phase = 1
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 20;
    
    /* Equipes du chapeau 2 éliminée (20 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 2
                                AND     equipes_groupes.EquipesGroupes_Phase = 3
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 20;

    /* Equipes du chapeau 3 en phase finale (40 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 3
                                AND     equipes_groupes.EquipesGroupes_Phase = 1
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 40;
    
    /* Equipes du chapeau 3 éliminée (10 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 3
                                AND     equipes_groupes.EquipesGroupes_Phase = 3
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 10;

    /* Equipes du chapeau 4 en phase finale (60 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 4
                                AND     equipes_groupes.EquipesGroupes_Phase = 1
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 60;
    
    /* Equipes du chapeau 4 éliminée (20 points) */
    UPDATE      pronostics_phase
    JOIN        (
                    SELECT      equipes_groupes.Equipes_Equipe, equipes_groupes.EquipesGroupes_Phase
                    FROM        equipes_groupes
                    WHERE       equipes_groupes.EquipesGroupes_Chapeau = 4
                                AND     equipes_groupes.EquipesGroupes_Phase = 2
                ) chapeau1_barrage
                ON      pronostics_phase.Equipes_Equipe = chapeau1_barrage.Equipes_Equipe
                        AND     pronostics_phase.PronosticsPhase_Qualification = chapeau1_barrage.EquipesGroupes_Phase
    SET         pronostics_phase.PronosticsPhase_Points = 20;

	
END$$
DELIMITER ;