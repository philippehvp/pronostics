<?php
	include('commun_administrateur.php');

	// Sauvegarde des informations d'un match
	
	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipeD = isset($_POST["equipeD"]) ? $_POST["equipeD"] : 0;
	$equipeV = isset($_POST["equipeV"]) ? $_POST["equipeV"] : 0;
	$coteEquipeD = isset($_POST["coteEquipeD"]) ? $_POST["coteEquipeD"] : 0;
	$coteNul = isset($_POST["coteNul"]) ? $_POST["coteNul"] : 0;
	$coteEquipeV = isset($_POST["coteEquipeV"]) ? $_POST["coteEquipeV"] : 0;
	$dateDebut = isset($_POST["dateDebut"]) ? $_POST["dateDebut"] : NULL;
	$heureDebut = isset($_POST["heureDebut"]) ? $_POST["heureDebut"] : 0;
	$minuteDebut = isset($_POST["minuteDebut"]) ? $_POST["minuteDebut"] : 0;
	$matchCanal = isset($_POST["matchCanal"]) ? $_POST["matchCanal"] : 0;
	$scoreEquipeD = isset($_POST["scoreEquipeD"]) ? $_POST["scoreEquipeD"] : NULL;
	$scoreEquipeV = isset($_POST["scoreEquipeV"]) ? $_POST["scoreEquipeV"] : NULL;
	$scoreAPEquipeD = isset($_POST["scoreAPEquipeD"]) ? $_POST["scoreAPEquipeD"] : NULL;
	$scoreAPEquipeV = isset($_POST["scoreAPEquipeV"]) ? $_POST["scoreAPEquipeV"] : NULL;
	$vainqueur = isset($_POST["vainqueur"]) ? $_POST["vainqueur"] : NULL;
	$coefficient = ($matchCanal == 1) ? 2 : 1;
	$report = isset($_POST["report"]) ? $_POST["report"] : 0;
	$matchCS = isset($_POST["matchCS"]) ? $_POST["matchCS"] : 0;
	$matchAP = isset($_POST["matchAP"]) ? $_POST["matchAP"] : NULL;
	$nomMatch = isset($_POST["nomMatch"]) ? $_POST["nomMatch"] : NULL;
	$pointsQualificationEquipeD = isset($_POST["pointsQualificationEquipeD"]) ? $_POST["pointsQualificationEquipeD"] : NULL;
	$pointsQualificationEquipeV = isset($_POST["pointsQualificationEquipeV"]) ? $_POST["pointsQualificationEquipeV"] : NULL;
	$matchDirect = isset($_POST["matchDirect"]) ? $_POST["matchDirect"] : 0;
	$matchLienPage = isset($_POST["matchLienPage"]) ? $_POST["matchLienPage"] : '';
	$matchLienPageComplementaire = isset($_POST["matchLienPageComplementaire"]) ? $_POST["matchLienPageComplementaire"] : '';
	$matchIgnore = isset($_POST["matchIgnore"]) ? $_POST["matchIgnore"] : 0;
	
	// L'heure max à laquelle un utilisateur peut faire ses pronostics dépend :
	// - du type de championnat
	// - du jour de la semaine (cas typique de ligue 1)
	
	$ordreSQL =		'	SELECT			Championnats_Regle' .
					'	FROM			championnats'.
					'	INNER JOIN		journees'.
					'					ON		championnats.Championnat = journees.Championnats_Championnat'.
					'	INNER JOIN		matches'.
					'					ON		journees.Journee = matches.Journees_Journee'.
					'	WHERE			matches.Match = ' . $match;
					
	$req = $bdd->query($ordreSQL);
	$donnees = $req->fetchAll();
	$regle = $donnees[0]["Championnats_Regle"];
	
	// Pour mise à jour du match avec tous les paramètres (même s'ils n'ont pas été modifiés)
	$ordreSQL =		'	UPDATE		matches' .
					'	SET			Equipes_EquipeDomicile = ' . $equipeD .
					'				,Equipes_EquipeVisiteur = ' . $equipeV .
					'				,Matches_CoteEquipeDomicile = ' . $coteEquipeD .
					'				,Matches_CoteNul = ' . $coteNul .
					'				,Matches_CoteEquipeVisiteur = ' . $coteEquipeV;
	
	// Cas spécial pour la date
	if($dateDebut != NULL)
		$ordreSQL .=	'			,Matches_Date = STR_TO_DATE(\'' . $dateDebut . ' ' . $heureDebut . ':' . $minuteDebut . '\', \'%d/%m/%Y %H:%i\') ';
	else
		$ordreSQL .=	'			,Matches_Date = NULL';
		
	$ordreSQL .=	'				,Matches_Coefficient = ' . $coefficient .
					'				,Matches_Report = ' . $report;
	
	// Cas spécial des scores des équipes (le fait que le score soit à NULL signifie que le match n'a pas encore été joué)
	if($scoreEquipeD != NULL && $scoreEquipeD != -1)
		$ordreSQL .=	'			,Matches_ScoreEquipeDomicile = ' . $scoreEquipeD;
	else
		$ordreSQL .=	'			,Matches_ScoreEquipeDomicile = NULL ';

	if($scoreEquipeV != NULL && $scoreEquipeV != -1)
		$ordreSQL .=	'			,Matches_ScoreEquipeVisiteur = ' . $scoreEquipeV;
	else
		$ordreSQL .=	'			,Matches_ScoreEquipeVisiteur = NULL ';
	
	// Score AP
	if($scoreAPEquipeD != NULL && $scoreAPEquipeD != -1)
		$ordreSQL .=	'			,Matches_ScoreAPEquipeDomicile = ' . $scoreAPEquipeD;
	else
		$ordreSQL .=	'			,Matches_ScoreAPEquipeDomicile = NULL ';

	if($scoreAPEquipeV != NULL && $scoreAPEquipeV != -1)
		$ordreSQL .=	'			,Matches_ScoreAPEquipeVisiteur = ' . $scoreAPEquipeV;
	else
		$ordreSQL .=	'			,Matches_ScoreAPEquipeVisiteur = NULL ';
	
	if($vainqueur != NULL && $vainqueur != -1)
		$ordreSQL .=	'			,Matches_Vainqueur = ' . $vainqueur;
	else
		$ordreSQL .=	'			,Matches_Vainqueur = NULL ';
		
	$ordreSQL .=		'			,Matches_MatchCS = ' . $matchCS;
					
	if($matchAP != NULL)
		$ordreSQL .=	'			,Matches_AvecProlongation = ' . $matchAP;

	if($nomMatch != NULL)
		$ordreSQL .=	'			,Matches_L1EuropeNom = ' . $bdd->quote($nomMatch);
	
	if($pointsQualificationEquipeD != NULL)
		$ordreSQL .=	'			,Matches_PointsQualificationEquipeDomicile = ' . $pointsQualificationEquipeD;
	else
		$ordreSQL .=	'			,Matches_PointsQualificationEquipeDomicile = NULL';
		
	if($pointsQualificationEquipeV != NULL)
		$ordreSQL .=	'			,Matches_PointsQualificationEquipeVisiteur = ' . $pointsQualificationEquipeV;
	else
		$ordreSQL .=	'			,Matches_PointsQualificationEquipeVisiteur = NULL';

	$ordreSQL .=	'				,Matches_Direct = ' . $matchDirect .
					'				,Matches_LienPage = \'' . $matchLienPage . '\'' .
					'				,Matches_LienPageComplementaire = \'' . $matchLienPageComplementaire . '\'' .
					'				,Matches_MatchIgnore = ' . $matchIgnore .
					'	WHERE		matches.Match = ' . $match;

	$bdd->exec($ordreSQL);


?>