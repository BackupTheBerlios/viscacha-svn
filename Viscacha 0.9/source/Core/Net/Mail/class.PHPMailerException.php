<?php
/**
 * PHPMailer - PHP email class
 *
 * Copyright (c) 2004-2009, Andy Prevost. All Rights Reserved.
 * Copyright (c) 2001-2003, Brent R. Matzelle
 *
 * Software: PHPMailer - PHP email class
 * Version: 5.1
 * Contact: via sourceforge.net support pages (also www.codeworxtech.com)
 * Info: http://phpmailer.sourceforge.net
 * Support: http://sourceforge.net/projects/phpmailer/
 * Admin: Andy Prevost (project admininistrator)
 * Author: Andy Prevost (codeworxtech) codeworxtech@users.sourceforge.net
 * Author: Marcus Bointon (coolbru) coolbru@users.sourceforge.net
 * Founder: Brent R. Matzelle (original founder)

 * License: Distributed under the Lesser General Public License (LGPL)
 *          http://www.gnu.org/copyleft/lesser.html
 * This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author		Marcus Bointon (coolbru) <coolbru@users.sourceforge.net>
 * @author		Brent R. Matzelle (original founder)
 * @copyright	Copyright (c) 2004-2009, Andy Prevost. All Rights Reserved.
 * @copyright	Copyright (c) 2001-2003, Brent R. Matzelle
 * @version		5.1
 * @link		http://phpmailer.sourceforge.net
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

/**
 * PHPMailer - PHP email transport class
 *
 * @package		Core
 * @subpackage	Net
 * @author		Andy Prevost
 * @author		Marcus Bointon
 * @copyright	2004 - 2009 Andy Prevost
 * @since		0.8
 * @version		$Id: class.phpmailer.php 447 2009-05-25 01:36:38Z codeworxtech $
 */
class PHPMailerException extends Exception {
	public function errorMessage() {
		return $this->getMessage();
	}
}
?>