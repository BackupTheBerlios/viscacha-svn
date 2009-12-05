<?php
/**
 * Viscacha CMS - A website management solution for managing your content easily.
 * Copyright (C) 2004-2008  Viscacha.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package		Viscacha
 * @subpackage 	System
 */

/**
 * This is the error handling class.
 *
 * Exceptions and errors will be caught and presented here.
 *
 * @package		Viscacha
 * @subpackage 	System
 * @author		Matthias Mohr
 * @copyright	Viscacha.org Team
 * @since 		0.9
 */
class ErrorHandling {

	private $depth;

	/**
	 * Sets the error handler and the exception handler.
	 *
	 * @param boolean TRUE to set error handler, FALSE to use PHP error handler
	 * @param boolean TRUE to set exception handler, FALSE to use PHP exception handler
	 */
	public function __construct($error = true, $exception = true) {
		if ($error == true) {
			//set_error_handler(array($this, 'errorHandler'));
		}
		if ($exception == true) {
			set_exception_handler(array($this, 'exceptionHandler'));
		}
		$this->depth = 0;
	}

	/**
	 * Handles the PHP errors and outputs xHTML-Code with debugging information.
	 *
	 * Each time this method is called the error details will be passed to the ErrrorHandling::log()-method.
	 *
	 * @see ErrrorHandling::log()
	 * @param int Error Number
	 * @param string Error Text
	 * @param string Filename
	 * @param int Line number
	 **/
	public function errorHandler($errno, $errtext, $errfile, $errline) {
		// Fifth parameter $errcontext can not be used with OOP.
		$this->depth++;

		$errdate = gmdate("r");

		$errortype = array (
			E_ERROR				=> "PHP Error",
			E_RECOVERABLE_ERROR => "PHP Error",
			E_WARNING			=> "PHP Warning",
			E_NOTICE			=> "PHP Notice",
			E_USER_ERROR		=> "Viscacha Error",
			E_USER_WARNING		=> "Viscacha Warning",
			E_USER_NOTICE		=> "Viscacha Notice",
			E_STRICT			=> "PHP5 Strict Notice"
		);

		switch ($errno) {
			case E_WARNING:
			case E_NOTICE:
			case E_USER_WARNING:
			case E_USER_NOTICE:
				echo "{$errortype[$errno]}: {$errtext} (File {$errfile} on line {$errline})";
				$this->log("{$errortype[$errno]} [{$errdate}]: {$errtext} (File {$errfile} on line {$errline})");
				$this->depth--;
				return false;
			break;
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:

				if ($this->depth > 1) {
					restore_error_handler();
				}

				if (function_exists('ob_clean') && @ob_get_level() != 0) {
					@ob_clean();
				}
				?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
		<head>
			<meta http-equiv="content-type" content="text/html; charset=urf-8" />
			<title>Viscacha <?php echo Core::version(); ?> &raquo; Error</title>
			<link rel="stylesheet" type="text/css" href="client/error.css" />
		</head>
		<body>
			<h1>Error</h1>
			<p class="center">
				[<a href="<?php echo Config::get('general.url'); ?>index.php">Return to Index</a>]
				<?php if (empty($_SERVER['HTTP_REFERER']) == false && Validator::checkURL($_SERVER['HTTP_REFERER']) == true) { ?>
				&nbsp;&nbsp;[<a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>">Return to last Page</a>]
				<?php } ?>
			</p>
			<h3>Error Message</h3>
			<p><strong><?php echo $errortype[$errno]; ?></strong>: <?php echo $errtext; ?></p>
			<h3>Error Details</h3>
			<p>
				File: <?php echo $errfile; ?><br />
				Line: <?php echo $errline; ?><br />
				Date: <?php echo $errdate; ?><br />
			</p>
			<h3>Code Snippet</h3>
			<?php echo $this->getCodeSnippet($errfile, $errline); ?>
			<h3>Backtrace</h3>
			<?php echo $this->getBacktrace(); ?>
			<?php echo $this->exposePHP(); ?>
			<h3>Contact</h3>
			<p>Please notify the board administrator: <a href="mailto:<?php echo Config::get('general.email'); ?>"><?php echo Config::get('general.email'); ?></a></p>
			<h3>Copyright</h3>
			<p>
				<strong><a href="http://www.viscacha.org" target="_blank">Viscacha <?php echo Core::version(); ?></a></strong><br />
				Copyright &copy; by Viscacha.org Team
			</p>
		</body>
	</html>
				<?php
				$this->log("{$errortype[$errno]} [{$errdate}]: {$errtext} (File {$errfile} on line {$errline})");
				exit;
			break;
		}
	}

	/**
	 * Handles the PHP exceptions and outputs xHTML-Code with debugging information.
	 *
	 * Each time this method is called the error details will be passed to the ErrrorHandling::log()-method.
	 *
	 * @see ErrrorHandling::log()
	 * @param mixed Exception
	 **/
	public function exceptionHandler($exception) {
		if ($this->depth > 0) {
			restore_exception_handler();
		}
		$this->depth++;

		$errdate = gmdate("r");

		if (function_exists('ob_clean') && ob_get_level() != 0) {
			ob_clean();
		}

		$name = get_class($exception);
		$number = $exception->getCode();

		$data = array();
		if (function_exists('class_implements') == true) {
			$interfaces = class_implements($exception);
			if (is_array($interfaces) == true) {
				$data = $exception->getData();
			}
		}

		?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
		<head>
			<meta http-equiv="content-type" content="text/html; charset=urf-8" />
			<title>Viscacha <?php echo Core::version(); ?> &raquo; Exception</title>
			<link rel="stylesheet" type="text/css" href="client/error.css" />
		</head>
		<body>
			<h1>Exception</h1>
			<p class="center">
				[<a href="<?php echo Config::get('general.url'); ?>index.php">Return to Index</a>]
				<?php if (empty($_SERVER['HTTP_REFERER']) == false && Validator::checkURL($_SERVER['HTTP_REFERER']) == true) { ?>
				&nbsp;&nbsp;[<a href="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>">Return to last Page</a>]
				<?php } ?>
			</p>
			<h3>Error Message</h3>
			<p><strong><?php echo $name.iif($number != 0, " #{$number}"); ?></strong>: <?php echo $exception->getMessage(); ?></p>
			<h3>Error Details</h3>
			<p>
				File: <?php echo $exception->getFile(); ?><br />
				Line: <?php echo $exception->getLine(); ?><br />
				Date: <?php echo $errdate; ?>
				<?php foreach ($data as $title => $value) { ?>
				<br /><?php echo $title; ?>: <?php echo $value; ?>
				<?php } ?>
			</p>
			<h3>Code Snippet</h3>
			<?php echo $this->getCodeSnippet($exception->getFile(), $exception->getLine()); ?>
			<h3>Backtrace</h3>
			<?php echo $this->getBacktrace($exception->getTrace()); ?>
			<?php echo $this->exposePHP(); ?>
			<h3>Contact</h3>
			<p>Please notify the board administrator: <a href="mailto:<?php echo Config::get('general.email'); ?>"><?php echo Config::get('general.email'); ?></a></p>
			<h3>Copyright</h3>
			<p>
				<strong><a href="http://www.viscacha.org" target="_blank">Viscacha <?php echo Core::version(); ?></a></strong><br />
				Copyright &copy; by Viscacha.org Team
			</p>
		</body>
	</html>
		<?php
		$this->log("{$name} [{$errdate}]: {$exception}");
		exit;
	}

	/**
	 * Writes a error message to the log file internals.log.
	 *
	 * @see Debug::add()
	 * @param string Error that should be saved to the log file.
	 */
	private function log($error) {
		if (Config::get('core.debug') == 1) {
			$debug = Core::getObject('Viscacha.System.Debug');
			$debug->add($error);
		}
	}

	/**
	 * Get backtrace in xHTML.
	 *
	 * Function debug_backtrace() needs to be enabled.
	 *
	 * @return string xHTML backtrace infomation.
	 */
	private function getBacktrace($backtrace = null) {

		if (is_array($backtrace) == false && function_exists('debug_backtrace')) {
			$backtrace = @debug_backtrace();
		}
		if (is_array($backtrace) == false) {
			return '<p>Backtrace is not available!</p>';
		}

		$output = '';
		foreach ($backtrace as $number => $trace) {
			// We skip the first one, because it only shows this file/function
			if ($number == 0) {
				continue;
			}

			if (isset($trace['file'])) {
				// Strip the current directory from path
				$trace['file'] = str_replace(array(realpath('./'), '\\'), array('', '/'), $trace['file']);
				$trace['file'] = substr($trace['file'], 1);
			}

			$args = array();
			if (isset($trace['args']) && is_array($trace['args'])) {
				foreach ($trace['args'] as $argument) {
					switch (gettype($argument)) {
						case 'integer':
						case 'double':
							$args[] = $argument;
						break;

						case 'string':
							$argument = htmlspecialchars(substr($argument, 0, 64)) . ((strlen($argument) > 64) ? '...' : '');
							$args[] = "'{$argument}'";
						break;

						case 'array':
							$args[] = 'Array(' . count($argument) . ')';
						break;

						case 'object':
							$args[] = 'Object(' . get_class($argument) . ')';
						break;

						case 'resource':
							$args[] = 'Resource(' . strstr($argument, '#') . ')';
						break;

						case 'boolean':
							$args[] = ($argument) ? 'true' : 'false';
						break;

						case 'NULL':
							$args[] = 'NULL';
						break;

						default:
							$args[] = 'Unknown';
					}
				}
			}

			$trace['file'] = (!isset($trace['file'])) ? 'N/A' : $trace['file'];
			$trace['line'] = (!isset($trace['line'])) ? 'N/A' : $trace['line'];
			$trace['class'] = (!isset($trace['class'])) ? '' : $trace['class'];
			$trace['type'] = (!isset($trace['type'])) ? '' : $trace['type'];

			$output .= '<ul class="code">';
			$output .= '<li class="linetwo"><b>File:</b> ' . htmlspecialchars($trace['file']) . '</li>';
			$output .= '<li class="lineone"><b>Line:</b> ' . $trace['line'] . '</li>';
			$output .= '<li class="linetwo"><b>Call:</b> ' . htmlspecialchars($trace['class'] . $trace['type'] . $trace['function']) . '(' . ((count($args)) ? implode(', ', $args) : '') . ')</li>';
			$output .= '</ul>';
		}
		return $output;
	}

	/**
	 * Gets a short code snippet in xHTML.
	 *
	 * Loads the file with the error and outputs the lines surrounding the error.
	 *
	 * @param string File with the error to load
	 * @param int Line with the error
	 * @return string xHTML-Code-Snippet
	 */
	private function getCodeSnippet($file, $line) {
		$file = new File($file);
		$lines = $file->read(FILE_LINES_TRIM);
	    if(is_array($lines) == false) {
	        return '<p>Could not load code snippet!</p>';
	    }

		$code    = '<ul class="code">';
		$total   = count($lines);

		for($i = $line - 5; $i <= $line + 5; $i++) {
			if(($i >= 1) && ($i <= $total)) {
	            $codeline = htmlentities($lines[$i - 1]);
	            $codeline = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $codeline);
	            $codeline = str_replace(' ',  '&nbsp;',                   $codeline);

	            $i = sprintf("%05d", $i);

	            $class = $i % 2 == 0 ? 'lineone' : 'linetwo';

	            if($i != $line) {
	                $code .= "<li class=\"{$class}\"><span>{$i}</span>{$codeline}</li>\n";
	            }
	            else {
	                $code .= "<li class=\"mark\"><span>{$i}</span>{$codeline}</li>\n";
	            }
	        }
		}

	    $code .= "</ul>";

		return $code;
	}

	/**
	 * Gets some PHP informations (version, extensions) if system.expose_php is activated.
	 *
	 * @return xHTML Core with PHP Informations
	 */
	private function exposePHP() {
		if (Config::get('system.expose_php') == 1) {
			$phpVer = phpversion();
			$code  = "<h3>PHP Version: {$phpVer}</h3>\n";
			$code .= "<p>\n";
			if (function_exists('get_loaded_extensions') == true) {
				$ext = @get_loaded_extensions();
				natcasesort($ext);
				$code .= "<strong>Extensions:</strong>\n";
				$code .= "<ul>\n";
				foreach ($ext as $name) {
					$ver = phpversion($name);
					$ver = iif($ver != false, " ({$ver})");
					$code .= "<li>{$name}{$ver}</li>\n";
				}
				$code .= "</ul>\n";
			}
			else {
				$code .= "Function get_loaded_extensions() is disabled. Can not get information about installed extensions.";
			}
			$code .= "</p>\n";
			return $code;
		}
		else {
			return '';
		}
	}
}
?>
