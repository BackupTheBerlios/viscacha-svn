<?php
/**
 * Simple star rating implementation for custom fields.
 *
 * @package		Cms
 * @subpackage	DataFields
 * @author		Matthias Mohr
 * @since 		1.0
 */

class CustomRating extends CustomField {

	public function getTypeName() {
		return 'Bewertung';
	}
	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomRating';
	}
	public function getDbDataType() {
		return 'TINYINT(2)';
	}
	public function getDefaultData() {
		return 0;
	}
	public function getRange() {
		$range = array();
		$middle = ($this->params['max']+1)/2;
		for($i = 1; $i <= $this->params['max']; $i++) {
			$add = '';
			if ($i == 1) {
				$add = 'Sehr schlecht';
			} else if ($i == $this->params['max']) {
				$add = 'Sehr gut';
			} else if ($i == $middle) {
				$add = 'Durchschnitt';
			}
			$range[$i] = $i . iif(!empty($add), " ({$add})");
		}
		return $range;
	}
	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/rating/input', $data, array('range' => $this->getRange()));
	}
	public function getOutputCode($data = null) {
		return self::getStarOutputCode($data, 0, $this->params['max']);
	}
	public function getValidation() {
		return array(
			Validator::MESSAGE => 'Die angegebene Bewertung im Feld "'.$this->getName().'" ist ungültig.',
			Validator::MIN_VALUE => 1,
			Validator::MAX_VALUE => $this->params['max'],
			Validator::OPTIONAL => $this->params['optional']
		);
	}

	public function getParamNames($add = false) {
		return array('max', 'optional');
	}
	public function getParamsCode($add = false) {
		return $this->getCodeImpl('/Cms/bits/rating/params', compact("add"));
	}
	public function getValidationParams($add = false) {
		return array(
			'optional' => array(
				Validator::VAR_TYPE => VAR_INT
			),
			'max' => array(
				Validator::MESSAGE => 'Die Anzahl der Bewertungsstufen darf nur zwischen 2 und 99 liegen.',
				Validator::VAR_TYPE => VAR_INT,
				Validator::MIN_VALUE => 2,
				Validator::MAX_VALUE => 99
			)
		);
	}

	public static function findNearestStep($value, $min = 0, $max = 5, $step = 0.5) {
		if ($step <= 0) {
			Core::throwError("Step has to be > 0, given {$step}.", INTERNAL_ERROR);
		}
		if ($value <= $min) {
			return $$min;
		}
		else if ($value >= $max) {
			return $max;
		}
		else {
			$margin = $step / 2;
			for($i = $min; $i < $max;$i += $step) {
				if ($value > ($i - $margin) && $value <= ($i + $margin)) {
					return $i;
				}
			}
			return $max;
		}
	}
	
	public static function getStarOutputCode($data, $min = 0, $max = 5, $step = 0.5) {
		$nearest = self::findNearestStep($data, $min, $max, $step);
		$fullStars = intval($nearest);
		$halfStar = ($nearest != $fullStars);
		$tpl = Response::getObject()->getTemplate('/Cms/bits/rating/output');
		$tpl->assignMultiple(compact("fullStars", "halfStar", "data", "min", "max"));
		return $tpl->parse();
		
	}

}
?>