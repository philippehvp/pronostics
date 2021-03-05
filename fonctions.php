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
	// Pour la CDF, il est possible que le score AP soit vide, même en cas de TAB
	function formaterScoreMatch($scoreEquipeDomicile, $scoreAPEquipeDomicile, $scoreEquipeVisiteur, $scoreAPEquipeVisiteur, $vainqueur) {
		$scoreMatch = '';
		$separateur = ' - ';
		$ap = ' AP';
		$tab = ' TAB';

		if($vainqueur != null && $vainqueur != '' && $vainqueur != '0' && $vainqueur != '?') {
			if($vainqueur == 1) {
				if ($scoreAPEquipeDomicile != null && $scoreAPEquipeVisiteur != null)
					$scoreMatch = $scoreAPEquipeDomicile . $tab . $separateur . $scoreAPEquipeVisiteur;
				else
					$scoreMatch = $scoreEquipeDomicile . $tab . $separateur . $scoreEquipeVisiteur;
			}
			else if($vainqueur == 2) {
				if ($scoreAPEquipeDomicile != null && $scoreAPEquipeVisiteur != null)
					$scoreMatch = $scoreAPEquipeDomicile . $separateur . $scoreAPEquipeVisiteur . $tab;
				else
					$scoreMatch = $scoreEquipeDomicile . $separateur . $scoreEquipeVisiteur . $tab;
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