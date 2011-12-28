<?php
Core::loadClass('Core.Util.Template');
Core::loadClass('Cms.Util.CmsTools');
Core::loadClass('Cms.Util.Breadcrumb');
Core::loadClass('Cms.Auth.Session');

/**
 * This is a general Cms module object. All Cms modules should extend it.
 *
 * @package		Cms
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class CmsModuleObject extends CoreModuleObject {

	protected $breadcrumb;
	protected $tpl;
	protected $cssFiles;
	protected $scriptFiles;

	public function __construct() {
		parent::__construct("Cms");

		$cache = Core::getObject('Core.Cache.CacheServer');
		$cache->setSourceDir('Cms.Cache.Items');

		// URL => content for media-attribute
		$this->cssFiles = array(
			URI::build('client/styles/stylesheet.css') => 'all'
		);

		// URL => content for type-attribute
		$this->scriptFiles = array();

		$this->tpl = new Template($this->package);
		Core::storeNObject($this->tpl, 'TPL');

		Session::getObject(); // Init session

		$this->breadcrumb = new Breadcrumb();
		$this->breadcrumb->add(Config::get('general.title'), URI::frontPage());
	}

	public function __destruct() {
		try {
			Session::getObject()->update();
		} catch (QueryException $e) {
			Core::_(DB)->getDebug()->add($e);
			throw $e;
		}
		parent::__destruct();
	}

	protected function header() {
		$this->tpl->assign('breadcrumb', $this->breadcrumb);
		$this->tpl->assign('cssFiles', $this->cssFiles);
		$this->tpl->assign('scriptFiles', $this->scriptFiles);
		$this->tpl->output('header');
	}

	protected function footer() {
		$this->tpl->assign('breadcrumb', $this->breadcrumb);
		$this->tpl->output('footer');
	}

	protected function error($message, $url = null) {
		if (!is_array($message)) {
			$message = array($message);
		}
		$this->tpl->assign('url', $url);
		$this->tpl->assign('message', $message);
		$this->tpl->output('error');
	}

	protected function yesNo($question, $yesUrl, $noUrl) {
		$this->tpl->assign('yesUrl', $yesUrl);
		$this->tpl->assign('noUrl', $noUrl);
		$this->tpl->assign('question', $question);
		$this->tpl->output('yes_no');
	}

	protected function ok($message, $url = null) {
		if (!is_array($message)) {
			$message = array($message);
		}
		$this->tpl->assign('url', $url);
		$this->tpl->assign('message', $message);
		$this->tpl->output('ok');
	}

}
?>
