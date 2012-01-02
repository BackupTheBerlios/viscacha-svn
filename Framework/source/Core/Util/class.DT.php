<?php
/*
 * PHP DateTime class extended by out special needs.
 *
 * @package		Core
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class DT extends DateTime {

	public static function fromTimestamp($ts) {
		return new DT('@' . $ts);
	}

	public function  __construct($time = 'now', DateTimeZone $timezone = null) {
		if ($timezone == null) {
			$timezone = new DateTimeZone(Config::get('intl.timezone'));
		}
		parent::__construct($time, $timezone);
	}

	public function date() {
		return $this->format(Config::get('intl.date_format'));
	}

	public function time() {
		return $this->format(Config::get('intl.time_format'));
	}

	public function dateTime() {
		return $this->format(Config::get('intl.datetime_format'));
	}

}
?>