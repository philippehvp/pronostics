<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	include_once('commun_entete.php');
?>
</head>


<body>
<?php
    $nomPage = 'gerer_site.php';
    include_once('bandeau.php');
    
    echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
    
    // Page de gestion des données du site
    
    echo '<div id="divSauvegarde" class="contenu-page">';
        // Sauvegarde de la base de données
        // Réinitialisation de la saison
        $annee = date("Y");
        echo '<label>Saison à sauvegarder : </label>';
        echo '<input type="text" id="txtSaison" value="' . $annee . '">';
        echo '<br />';
        echo '<label class="bouton" onclick="gererSite_sauvegarderDonnees();">Sauvegarder</label>';

        echo '<br /><br />';
        // Date max de saisie des bonus de fin de saison
		$ordreSQL =		'	SELECT				bonus_date_max.Bonus_Date_Max AS Bonus_Date_Max_Date, HOUR(bonus_date_max.Bonus_Date_Max) AS Bonus_Date_Max_Heure, MINUTE(bonus_date_max.Bonus_Date_Max) AS Bonus_Date_Max_Minute' .
						'	FROM				bonus_date_max';
		$req = $bdd->query($ordreSQL);
		$bonus = $req->fetchAll();

        $bonusDateMaxDate = $bonus[0]["Bonus_Date_Max_Date"] != null ? date("d/m/Y", strtotime($bonus[0]["Bonus_Date_Max_Date"])) : date("d/m/Y");
        $bonusDateMaxHeure = $bonus[0]["Bonus_Date_Max_Heure"] != null ? $bonus[0]["Bonus_Date_Max_Heure"] : 0;
        $bonusDateMaxMinute = $bonus[0]["Bonus_Date_Max_Minute"] != null ? $bonus[0]["Bonus_Date_Max_Minute"] : 0;
        echo '<h2>Saisie des bonus</h2>';
        echo '<label>Date max : </label>';
        echo '<input class="date" id="bonusDateMaxDate" type="text" value="' . $bonusDateMaxDate . '" onchange="gererSite_modifierDateBonus();"/> à ';
        echo '<select id="bonusDateMaxHeure" onchange="gererSite_modifierDateBonus();">';
            for($j = 0; $j <= 23; $j++) {
                $heures = sprintf('%02u', $j);
                $selected = $bonusDateMaxHeure == $j ? ' selected="selected"' : '';
                echo '<option' . $selected . ' value="' . $j . '">' . $heures . '</option>';
            }
        echo '</select>';

        echo '<select id="bonusDateMaxMinute" onchange="gererSite_modifierDateBonus();">';
            for($j = 0; $j <= 55; $j += 5) {
                $minutes = sprintf('%02u', $j);
                $selected = $bonusDateMaxMinute == $j ? ' selected="selected"' : '';
                echo '<option' . $selected . ' value="' . $j . '">' . $minutes . '</option>';
            }
        echo '</select>';

        echo '<br /><br />';
        // Date max de saisie des qualifications LDC
		$ordreSQL =		'	SELECT				qualifications_date_max.Qualifications_Date_Max AS Qualifications_LDC_Date_Max_Date' .
                        '                       ,HOUR(qualifications_date_max.Qualifications_Date_Max) AS Qualifications_LDC_Date_Max_Heure' .
                        '                       ,MINUTE(qualifications_date_max.Qualifications_Date_Max) AS Qualifications_LDC_Date_Max_Minute' .
						'	FROM				qualifications_date_max' .
                        '   WHERE               Championnats_Championnat = 2';
		$req = $bdd->query($ordreSQL);
		$qualificationsLDC = $req->fetchAll();

        $qualificationsLDCDateMaxDate = $qualificationsLDC[0]["Qualifications_LDC_Date_Max_Date"] != null ? date("d/m/Y", strtotime($qualificationsLDC[0]["Qualifications_LDC_Date_Max_Date"])) : date("d/m/Y");
        $qualificationsLDCDateMaxHeure = $qualificationsLDC[0]["Qualifications_LDC_Date_Max_Heure"] != null ? $qualificationsLDC[0]["Qualifications_LDC_Date_Max_Heure"] : 0;
        $qualificationsLDCDateMaxMinute = $qualificationsLDC[0]["Qualifications_LDC_Date_Max_Minute"] != null ? $qualificationsLDC[0]["Qualifications_LDC_Date_Max_Minute"] : 0;
        echo '<h2>Qualifications LDC</h2>';
        echo '<label>Date max : </label>';
        echo '<input class="date" id="qualificationsLDCDateMaxDate" type="text" value="' . $qualificationsLDCDateMaxDate . '" onchange="gererSite_modifierDateQualificationsLDC();"/> à ';
        echo '<select id="qualificationsLDCDateMaxHeure" onchange="gererSite_modifierDateQualificationsLDC();">';
            for($j = 0; $j <= 23; $j++) {
                $heures = sprintf('%02u', $j);
                $selected = $qualificationsLDCDateMaxHeure == $j ? ' selected="selected"' : '';
                echo '<option' . $selected . ' value="' . $j . '">' . $heures . '</option>';
            }
        echo '</select>';

        echo '<select id="qualificationsLDCDateMaxMinute" onchange="gererSite_modifierDateQualificationsLDC();">';
            for($j = 0; $j <= 55; $j += 5) {
                $minutes = sprintf('%02u', $j);
                $selected = $qualificationsLDCDateMaxMinute == $j ? ' selected="selected"' : '';
                echo '<option' . $selected . ' value="' . $j . '">' . $minutes . '</option>';
            }
        echo '</select>';        
        
        echo '<br /><br />';
        // Date max de saisie des qualifications EL
		$ordreSQL =		'	SELECT				qualifications_date_max.Qualifications_Date_Max AS Qualifications_EL_Date_Max_Date' .
                        '                       ,HOUR(qualifications_date_max.Qualifications_Date_Max) AS Qualifications_EL_Date_Max_Heure' .
                        '                       ,MINUTE(qualifications_date_max.Qualifications_Date_Max) AS Qualifications_EL_Date_Max_Minute' .
						'	FROM				qualifications_date_max' .
                        '   WHERE               Championnats_Championnat = 3';
		$req = $bdd->query($ordreSQL);
		$qualificationsEL = $req->fetchAll();

        $qualificationsELDateMaxDate = $qualificationsEL[0]["Qualifications_EL_Date_Max_Date"] != null ? date("d/m/Y", strtotime($qualificationsEL[0]["Qualifications_EL_Date_Max_Date"])) : date("d/m/Y");
        $qualificationsELDateMaxHeure = $qualificationsEL[0]["Qualifications_EL_Date_Max_Heure"] != null ? $qualificationsEL[0]["Qualifications_EL_Date_Max_Heure"] : 0;
        $qualificationsELDateMaxMinute = $qualificationsEL[0]["Qualifications_EL_Date_Max_Minute"] != null ? $qualificationsEL[0]["Qualifications_EL_Date_Max_Minute"] : 0;
        echo '<h2>Qualifications EL</h2>';
        echo '<label>Date max : </label>';
        echo '<input class="date" id="qualificationsELDateMaxDate" type="text" value="' . $qualificationsELDateMaxDate . '" onchange="gererSite_modifierDateQualificationsEL();"/> à ';
        echo '<select id="qualificationsELDateMaxHeure" onchange="gererSite_modifierDateQualificationsEL();">';
            for($j = 0; $j <= 23; $j++) {
                $heures = sprintf('%02u', $j);
                $selected = $qualificationsELDateMaxHeure == $j ? ' selected="selected"' : '';
                echo '<option' . $selected . ' value="' . $j . '">' . $heures . '</option>';
            }
        echo '</select>';

        echo '<select id="qualificationsELDateMaxMinute" onchange="gererSite_modifierDateQualificationsEL();">';
            for($j = 0; $j <= 55; $j += 5) {
                $minutes = sprintf('%02u', $j);
                $selected = $qualificationsELDateMaxMinute == $j ? ' selected="selected"' : '';
                echo '<option' . $selected . ' value="' . $j . '">' . $minutes . '</option>';
            }
        echo '</select>';

        
        
        
        echo '<br /><br />';
        echo '<h2 class="texte-rouge">Réinitialisation de la saison</h2>';
        echo '<label class="bouton" onclick="gererSite_reinitialiserDonnees();">Réinitialiser la saison</label>';
        
        
    echo '</div>';
    
?>

	<script>
		$(function() {
            $('.date').datepicker({dateFormat: 'dd/mm/yy'});
            
			afficherTitrePage('divSauvegarde', 'Sauvegarde et gestion des données');
			retournerHautPage();
		});
	</script>
	
</body>
</html>