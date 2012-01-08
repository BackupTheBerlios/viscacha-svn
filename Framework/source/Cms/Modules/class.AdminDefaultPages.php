<?php
/**
 * This are the default pages of our lovely admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminDefaultPages extends AdminModuleObject {

	public function __construct() {
		parent::__construct();
	}

	public function main(){
		$this->header();
		$this->tpl->assign('mysql_version', Core::_(DB)->version());
		$this->tpl->output("admin/default");
		$this->footer();
	}

	public function serverinfo() {
		ob_start();
		phpinfo();
		preg_match("~<body.*?>(.+?)</body>~is", ob_get_contents(), $match_body);
		ob_end_clean();

		$this->breadcrumb->Add("Serverinfo");
		$this->header();
		$this->tpl->assign('content', $match_body[0]);
		echo $this->tpl->parse("admin/serverinfo");
		$this->footer();
	}

}
?>
