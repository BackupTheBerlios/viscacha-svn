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

	const PAGES_NUM = 1;
	const PAGES_CURRENT = 2;
	const PAGES_SEPARATOR = 4;

	const CURRENCY_SYMBOL = 1;
	const CURRENCY_SYMBOL_HTML = 3;
	const CURRENCY_TEXT = 2;
	const CURRENCY_HIDE = 0;

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

	public static function getMonth($month) {
		$monthNames = array(
			1 => 'Januar',
			2 => 'Februar',
			3 => 'März',
			4 => 'April',
			5 => 'Mai',
			6 => 'Juni',
			7 => 'Juli',
			8 => 'August',
			9 => 'September',
			10 => 'Oktober',
			11 => 'November',
			12 => 'Dezember'
		);
		if (isset($monthNames[$month])) {
			return $monthNames[$month];
		}
		else {
			return $month;
		}
	}

	public static function leadingZero($int, $length = 2) {
		return sprintf("%0{$length}d", $int);
	}

	/**
	 * @todo Sinnvolleres abschneiden implementieren
	 */
	public static function cutText($text, $after = 200) {
		if (strlen($text) > $after) {
			$text = substr($text, 0, $after);
			$text .= '...';
		}
		return $text;
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

	/**
	 * Gives out html formatted page numbers.
	 *
	 * It uses the set of templates specified in the last parameter.
	 * The template sets are in the directory "main" and are prefixed with "pages".
	 * Example: the last parameter is "_small", the main template is "pages_small.html".
	 *
	 * @param int $anzposts Number of entries
	 * @param int $perpage Number of entries per page
	 * @param string $uri URL to the page with & or ? at the end (page=X will be appended)
	 * @param int $p The current page
	 * @param string $template Name of template set (see description)
	 * @return string HTML formatted page numbers and prefix
	 */
	public static function pagination($anzposts, $perpage, $uri, $p = 1, $template = '') {
		if (!is_id($anzposts)) {
			$anzposts = 1;
		}
		if (!is_id($perpage)) {
			$perpage = 10;
		}

		$query = parse_url($uri, PHP_URL_QUERY);
		if (empty($query)) {
			$uri .= '?';
		}
		else {
			$uri .= '&';
		}

		// Last page / Number of pages
		$anz = ceil($anzposts/$perpage);
		// Array with all page numbers
		$available_pages = range(1, $anz);
		// Page data for template
	    $pages = array();

		if ($anz > 10) {
			// What we want to be shown if available
			$show = array(
				1,
				2,
				$p-2,
				$p-1,
				$p,
				$p+1,
				$p+2,
				$anz-1,
				$anz
			);
			$show = array_unique($show);
			foreach ($show as $num) {
				if (in_array($num, $available_pages) == true) {
					if (in_array($num-1, $show) == false && $num > 1) { // Add separator when page numbers are missing
						$pages[$num-1] = array(
							'type' => self::PAGES_SEPARATOR,
							'url' => null,
							'separator' => false
						);
					}
					$pages[$num] = array(
						'type' => iif($num == $p, self::PAGES_CURRENT, self::PAGES_NUM),
						'url' => $uri.'page='.$num,
						'separator' => in_array($num+1, $show)
					);
				}
			}
		}
		else {
			for ($i = 1; $i <= $anz; $i++) {
				$pages[$i] = array(
					'type' => iif($i == $p, self::PAGES_CURRENT, self::PAGES_NUM),
					'url' => $uri.'page='.$i,
					'separator' => ($i != $anz)
				);
			}
		}

		ksort($pages);

		$tpl = Core::_(TPL);
		$tpl->assign('uri', $uri);
		$tpl->assign('anz', $anz);
		$tpl->assign('pages', $pages);
	    return $tpl->parse("bits/pages".$template);
	}

}
?>