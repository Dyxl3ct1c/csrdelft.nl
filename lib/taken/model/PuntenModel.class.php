<?php
namespace Taken\CRV;

require_once 'taken/model/VrijstellingenModel.class.php';

/**
 * PuntenModel.class.php	| 	P.W.G. Brussee (brussee@live.nl)
 * 
 */
class PuntenModel {

	public static function resetCorveejaar() {
		$aantal = 0;
		$errors = array();
		$vrijstellingen = VrijstellingenModel::getAlleVrijstellingen(true); // grouped by uid
		$matrix = self::loadPuntenTotaalVoorAlleLeden();
		foreach ($matrix as $uid => $totalen) {
			try {
				$lid = \LidCache::getLid($uid); // false if lid does not exist
				if (!$lid instanceof \Lid) {
					throw new \Exception('Reset corveejaar faalt: ongeldig lid');
				}
				$punten = $totalen['puntenTotaal'];
				$punten += $totalen['bonusTotaal'];
				$vrijstelling = null;
				if (array_key_exists($uid, $vrijstellingen) && time() > strtotime($vrijstellingen[$uid]->getEindDatum())) {
					$vrijstelling = $vrijstellingen[$uid];
					$punten += $vrijstelling->getPunten();
				}
				$punten -= intval($GLOBALS['corveepunten_per_jaar']);
				self::savePuntenVoorLid($lid, $punten, 0);
				if ($vrijstelling !== null) {
					VrijstellingenModel::verwijderVrijstelling($vrijstelling->getLidId());
					$aantal++;
				}
			}
			catch (\Exception $e) {
				$errors[] = $e;
			}
		}
		$taken = TakenModel::verwijderOudeTaken();
		return array($aantal, $taken, $errors);
	}
	
	public static function puntenToekennen($uid, $punten, $bonus_malus) {
		if (!is_int($punten) || !is_int($bonus_malus)) {
			throw new \Exception('Punten toekennen faalt: geen integer');
		}
		$lid = \LidCache::getLid($uid); // false if lid does not exist
		if (!$lid instanceof \Lid) {
			throw new \Exception('Punten toekennen faalt: ongeldig lid');
		}
		self::savePuntenVoorLid($lid, (int) $lid->getProperty('corvee_punten') + $punten, (int) $lid->getProperty('corvee_punten_bonus') + $bonus_malus);
	}
	
	public static function puntenIntrekken($uid, $punten, $bonus_malus) {
		if (!is_int($punten) || !is_int($bonus_malus)) {
			throw new \Exception('Punten intrekken faalt: geen integer');
		}
		$lid = \LidCache::getLid($uid); // false if lid does not exist
		if (!$lid instanceof \Lid) {
			throw new \Exception('Punten intrekken faalt: ongeldig lid');
		}
		self::savePuntenVoorLid($lid, (int) $lid->getProperty('corvee_punten') - $punten, (int) $lid->getProperty('corvee_punten_bonus') - $bonus_malus);
	}
	
	public static function savePuntenVoorLid(\Lid $lid, $punten=null, $bonus_malus=null) {
		if (!is_int($punten) && !is_int($bonus_malus)) {
			throw new \Exception('Save punten voor lid faalt: geen integer');
		}
		if (is_int($punten)) {
			$lid->setProperty('corvee_punten', $punten);
		}
		if (is_int($bonus_malus)) {
			$lid->setProperty('corvee_punten_bonus', $bonus_malus);
		}
		if (!$lid->save()) {
			throw new \Exception('Save punten voor lid faalt: opslaan mislukt');
		}
	}
	
	public static function loadPuntenTotaalVoorAlleLeden() {
		return self::loadPuntenTotaal('status IN("S_LID", "S_GASTLID", "S_NOVIET")');
	}
	
	private static function loadPuntenTotaal($where=null, $values=array(), $limit=null) {
		$sql = 'SELECT uid, corvee_punten, corvee_punten_bonus';
		$sql.= ' FROM lid';
		if ($where !== null) {
			$sql.= ' WHERE '. $where;
		}
		$sql.= ' ORDER BY achternaam, voornaam ASC';
		if (is_int($limit) && $limit > 0) {
			$sql.= ' LIMIT '. $limit;
		}
		$db = \CsrPdo::instance();
		$query = $db->prepare($sql, $values);
		$query->execute($values);
		$result = $query->fetchAll();
		$totalen = array();
		foreach ($result as $row) {
			$totalen[$row['uid']] = array(
				'puntenTotaal' => (int) $row['corvee_punten'],
				'bonusTotaal' => (int) $row['corvee_punten_bonus']
			);
		}
		return $totalen;
	}
	
	public static function loadPuntenVoorAlleLeden($functies=null) {
		$taken = TakenModel::getAlleTaken(true); // grouped by uid
		$vrijstellingen = VrijstellingenModel::getAlleVrijstellingen(true); // grouped by uid
		$matrix = self::loadPuntenTotaalVoorAlleLeden();
		foreach ($matrix as $uid => $totalen) {
			$lid = \LidCache::getLid($uid); // false if lid does not exist
			if (!$lid instanceof \Lid) {
				throw new \Exception('Load punten per functie faalt: ongeldig lid');
			}
			$lidtaken = array();
			if (array_key_exists($uid, $taken)) {
				$lidtaken = $taken[$uid];
			}
			$vrijstelling = null;
			if (array_key_exists($uid, $vrijstellingen)) {
				$vrijstelling = $vrijstellingen[$uid];
			}
			$matrix[$uid] = self::loadPuntenVoorLid($lid, $functies, $lidtaken, $vrijstelling);
		}
		return $matrix;
	}
	
	public static function loadPuntenVoorLid(\Lid $lid, $functies=null, $lidtaken=null, $vrijstelling=null) {
		if ($lidtaken === null) {
			$lidtaken = TakenModel::getTakenVoorLid($lid->getUid());
			$vrijstelling = VrijstellingenModel::getVrijstelling($lid->getUid());
		}
		if ($functies === null) { // niet per functie sommeren
			$lijst = array();
			$lijst['prognose'] = 0;
			foreach ($lidtaken as $taak) {
				$lijst['prognose'] += $taak->getPuntenPrognose();
			}
		}
		else {
			$lijst = self::sumPuntenPerFunctie($functies, $lidtaken);
		}
		if ($vrijstelling !== null) {
			$lijst['prognose'] += $vrijstelling->getPunten();
		}
		$lijst['lid'] = $lid;
		$lijst['puntenTotaal'] = (int) $lid->getProperty('corvee_punten');
		$lijst['bonusTotaal'] = (int) $lid->getProperty('corvee_punten_bonus');
		$lijst['prognose'] += $lijst['puntenTotaal'] + $lijst['bonusTotaal'];
		$lijst['prognoseColor'] = self::rgbCalculate($lijst['prognose']);
		return $lijst;
	}
	
	private static function sumPuntenPerFunctie($functies, $taken) {
		$sumAantal = array();
		$sumPunten = array();
		$sumBonus = array();
		$sumPrognose = 0;
		foreach ($functies as $fid => $functie) {
			$sumAantal[$fid] = 0; 
			$sumPunten[$fid] = 0;
			$sumBonus[$fid] = 0;
		}
		foreach ($taken as $taak) {
			$fid = $taak->getFunctieId();
			if (array_key_exists($fid, $functies)) {
				$sumAantal[$fid] += 1;
				$sumPunten[$fid] += $taak->getPuntenToegekend();
				$sumBonus[$fid] += $taak->getBonusToegekend();
			}
			$sumPrognose += $taak->getPuntenPrognose();
		}
		return array('aantal' => $sumAantal, 'punten' => $sumPunten, 'bonus' => $sumBonus, 'prognose' => $sumPrognose, 'prognoseColor' => self::rgbCalculate($sumPrognose));
	}
	
	/**
	 * RGB kleurovergang berekenen
	 */
	private static function rgbCalculate($punten) {
		$perjaar = intval($GLOBALS['corveepunten_per_jaar']);
		$verhouding = ($perjaar - $punten) / $perjaar;
		
		$r = 2 * $verhouding;
		$g = 2 * (1 - $verhouding);
		
		if ($r < 0) $r = 0; if ($r > 1) $r = 1;
		if ($g < 0) $g = 0; if ($g > 1) $g = 1;
		
		return dechex(8 + round($r * 6)).dechex(8 + round($g * 6)).dechex(8);
	}
}

?>