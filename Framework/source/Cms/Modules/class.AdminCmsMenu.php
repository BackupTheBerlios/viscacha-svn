<?php
/**
 * This are the default pages of our lovely admin control panel.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class AdminCmsMenu extends AdminMenuObject {
	public function getHeader($class) {
		switch ($class) {
			case 'Cms.Modules.AdminDocPages':
				return 'Seiten';
			case 'Cms.Modules.AdminMemberPages':
				return 'Mitglieder';
			default:
				return 'Hauptmenü';
		}
	}

	public function getMenu($class) {
		switch ($class) {
			case 'Cms.Modules.AdminDocPages':
				return array(
					URI::build('cms/admin/documents') => 'Übersicht',
					URI::build('cms/admin/documents/write') => 'Hinzufügen'
				);
			case 'Cms.Modules.AdminMemberPages':
				return array(
					URI::build('cms/admin/members') => 'Übersicht',
					URI::build('cms/admin/members/emailexport') => 'E-Mail-Adressen exportieren'
				);
			default:
				return array(
					URI::build('cms/admin/sys') => 'Startseite',
					URI::build('cms/admin/sys/serverinfo') => 'Server-Info'
				);
		}
	}

}
?>