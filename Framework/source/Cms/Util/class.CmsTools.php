<?php
/**
 * Some useful functions.
 *
 * @package		Cms
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class CmsTools {

	const COUNTRY_FILE = "data/countries.txt";

	public static function getCountries() {
		$file = new File(self::COUNTRY_FILE);
		$countries = $file->read(FILE_LINES_TRIM);
		if (is_array($countries)) {
			return $countries;
		}
		else {
			return array();
		}
	}

	public static function sendMail($to, $title, $message, $from = null, $fromName = null, $attachments = array()) {
		Core::loadClass('Core.Net.Mail.PHPMailer');
		$mail = new PHPMailer();
		$mail->AddAddress($to);
		$mail->Body = $message;
		$mail->CharSet = Config::get('intl.charset');
		$mail->Subject = $title;
		if ($from != null) {
			$mail->From = $from;
		}
		else {
			$mail->From = Config::get('general.email');
		}
		if ($fromName != null) {
			$mail->FromName = $fromName;
		}
		else {
			$mail->FromName = Config::get('general.title');
		}
		if (count($attachments) > 0) {
			foreach ($attachments as $file) {
				$mail->AddAttachment($file);
			}
		}
		if (Config::get('core.debug') == 1)  {
			var_dump($to, $title, $message, $from, $fromName, $attachments);
		}
		else {
			$mail->Send();
		}
	}

}
?>