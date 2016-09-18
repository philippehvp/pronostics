<?php
	function calculerCote($coefficient) {
		if($coefficient == null)
			return 0;

		if($coefficient < 2)
			$cote = 0;
		else
			$cote = (floor($coefficient) - 1) * 5;
		
		return $cote;
	}
	
	function jourSemaine($jour) {
		$jours = array	('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
		
		if($jour >= 0 && $jour <= 6)
			return $jours[$jour];
		else
			return '';
	}

	
	// Affiche le score du match selon le format traditionnel
	function formaterScoreMatch($scoreEquipeDomicile, $scoreAPEquipeDomicile, $scoreEquipeVisiteur, $scoreAPEquipeVisiteur, $vainqueur) {
		$scoreMatch = '';
		$separateur = ' - ';
		$ap = ' AP';
		$tab = ' TAB';
	
		if($vainqueur != null && $vainqueur != '' && $vainqueur != '0' && $vainqueur != '?') {
			if($vainqueur == 1) {
				$scoreMatch = $scoreAPEquipeDomicile . $tab . $separateur . $scoreAPEquipeVisiteur;
			}
			else if($vainqueur == 2) {
				$scoreMatch = $scoreAPEquipeDomicile . $separateur . $scoreAPEquipeVisiteur . $tab;
			}
			else {
				$scoreMatch = $scoreAPEquipeDomicile . $ap . $separateur . $scoreAPEquipeVisiteur . $ap;
			}
		}
		else {
			if($scoreAPEquipeDomicile != '?' && $scoreAPEquipeDomicile != '' && $scoreAPEquipeVisiteur != '?' && $scoreAPEquipeVisiteur != '') {
				$scoreMatch = $scoreAPEquipeDomicile . $ap . $separateur . $scoreAPEquipeVisiteur . $ap;
			}
			else {
				$scoreMatch = $scoreEquipeDomicile . $separateur . $scoreEquipeVisiteur;
			}
		}
		
		return $scoreMatch;
	}
	
	
?>