<?php
/**
 * general page blocks like error, ok, not found etc.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */

abstract class CmsPage {

	public static function error($message, $url = null) {
		if (!is_array($message)) {
			$message = array($message);
		}
		$tpl = Response::getObject()->appendTemplate('/Cms/error');
		$tpl->assign('url', $url);
		$tpl->assign('message', $message);
		$tpl->output();
	}

	public static function notFoundError() {
		self::error('Die angeforderte Seite konnte leider nicht gefunden werden!');
	}

	public static function yesNo($question, $yesUrl, $noUrl) {
		$tpl = Response::getObject()->appendTemplate('/Cms/yes_no');
		$tpl->assign('yesUrl', $yesUrl);
		$tpl->assign('noUrl', $noUrl);
		$tpl->assign('question', $question);
		$tpl->output();
	}

	public static function notice($message) {
		$tpl = Response::getObject()->appendTemplate('/Cms/notice');
		$tpl->assign('message', $message);
		$tpl->output();
	}

	public static function ok($message, $url = null) {
		if (!is_array($message)) {
			$message = array($message);
		}
		$tpl = Response::getObject()->appendTemplate('/Cms/ok');
		$tpl->assign('url', $url);
		$tpl->assign('message', $message);
		$tpl->output();
	}

}
?>