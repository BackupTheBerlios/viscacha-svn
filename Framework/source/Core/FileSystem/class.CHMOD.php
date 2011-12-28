<?php
/**
 * This class manages the different CHMOD values/types.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 */
class CHMOD {

	/**
	 * CHMOD value saved as octal (e.g. 777, 755, 644)
	 * @access	private
	 * @var	int
	 */
	private $chmod;

	/**
	 * Constructs a new CHMOD Manager.
	 *
	 * If the parameter is not null and not valid an Exception will be thrown.
	 *
	 * @param	int	Sets to the value of the the octal integer.
	 * @throws	Exception
	 */
	public function __construct($chmod = null) {
		$this->chmod = $chmod;
		if ($chmod != null && $this->checkMode(true) == false) {
			throw new Exception("Specified CHMOD ({$chmod}) is invalid.");
		}
	}

	/**
	 * Returns the Octal value of the specified CHMOD.
	 *
	 * @return	string	Octal CHMOD
	 */
	public function __toString() {
		return strval($this->chmod);
	}

	/**
	 * Returns the Octal value of the specified CHMOD.
	 *
	 * The script will abort if no CHMOD is set.
	 *
	 * @return	int	Octal CHMOD
	 */
	public function getOctal() {
		return (int) $this->chmod;
	}

	/**
	 * Returns the Decimal value of the specified CHMOD.
	 *
	 * The script will abort if no CHMOD is set.
	 *
	 * @return	int	Decimal CHMOD
	 */
	public function getDecimal() {
		return (int) base_convert($this->chmod, 8, 10);
	}

	/**
	 * Returns the string representation (contains rwx-) of the specified CHMOD.
	 *
	 * Example: rwxrw-r-- (is 764 as an octal integer).
	 * The script will abort if no CHMOD is set.
	 *
	 * @return	string	String representation CHMOD
	 */
	public function getString() {
		$trans = array(
					'0'=>'---',
					'1'=>'--x',
					'2'=>'-w-',
					'3'=>'-wx',
					'4'=>'r--',
					'5'=>'r-x',
					'6'=>'rw-',
					'7'=>'rwx',
				);
		$mode = strval($this->chmod);
		$newmode = '';
		for($i=0; $i<3; $i++) {
			$char = $mode{$i};
			$newmode .= $trans[$char];
		}
		return (string) $newmode;
	}

	/**
	 * Applies this CHMOD to the specified file or folder.
	 *
	 * The script will abort if no CHMOD is set.
	 *
	 * @param	mixed	Path to the file or directory
	 * @return	boolean	Returns TRUE on success or FALSE on failure.
	 */
	public function apply($path) {
		if (is_dir($path) == true) {
			$dir = new Folder(path);
			return $dir->chmod($this);
		}
		else {
			$file = new File($path);
			return $file->chmod($this);
		}
	}

	/**
	 * Sets the CHMOD to the value of the specified string.
	 *
	 * The script will abort if the CHMOD is incorrect.
	 * Correct example: rwxrw-r-- (is 764 as an octal integer).
	 *
	 * @param	string	String representation CHMOD
	 */
	public function setString($string) {
		$string = strtolower($string);
		if (strlen($string) != 9) {
			Core::throwError('Specified CHMOD has to be 9 characters long, '.strlen($mode).' given.', INTERNAL_ERROR);
		}
		if (preg_match("/[rwx-]{9}/i", $string) == 0) {
			Core::throwError('Specified CHMOD is incorrect, only rwx- allowed.', INTERNAL_ERROR);
		}
		$trans = array(
					'-'=>'0',
					'r'=>'4',
					'w'=>'2',
					'x'=>'1'
				);
		$mode = strtr($string, $trans);
		$this->chmod = 0;
		$this->chmod += ($mode{0}+$mode{1}+$mode{2})*100;
		$this->chmod += ($mode{3}+$mode{4}+$mode{5})*10;
		$this->chmod += ($mode{6}+$mode{7}+$mode{8});
		$this->checkMode();
	}

	/**
	 * Sets to the value of the the octal integer.
	 *
	 * The script will abort if the CHMOD is incorrect.
	 *
	 * @param	int	Decimal CHMOD
	 */
	public function setOctal($octal) {
		$this->chmod = $octal;
		$this->checkMode();
	}

	/**
	 * Sets to the value of the the decimal integer.
	 *
	 * The script will abort if the CHMOD is incorrect.
	 *
	 * @param	int	Decimal CHMOD
	 */
	public function setDecimal($decimal) {
		$this->chmod = base_convert($decimal, 10, 8);
		$this->checkMode();
	}


	/**
	 * Sets to the value of the the decimal integer.
	 *
	 * The script will abort if the CHMOD is incorrect.
	 *
	 * @param	int	Path to the file to read the CHMOD from.
	 */
	public function read($path) {
		if (file_exists($path) == true) {
			$perms = fileperms($path);
			$this->chmod = substr(sprintf('%o', $perms), -3);
			$this->checkMode();
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks that the value of the specified CHMOD is correct.
	 *
	 * If the parameter is FALSE (default), this method will abort the script if the CHMOD is incorrect.
	 * If the parameter is TRUE, this method will return false if the CHMOD is incorrect. If it is correct TRUE will be returned.
	 *
	 * @param boolean Set this parameter to TRUE to return the result of the check. FALSE will abort the script when an error occurs.
	 * @return boolean If the parameter is true a boolean will be returned.
	 */
	private function checkMode($disableError = false) {
		if ($this->chmod == null) {
			if (!$disableError) {
				Core::throwError('You have not set a value for CHMOD.', INTERNAL_ERROR);
			}
			return false;
		}
		else {
			$decimal = $this->getDecimal();
			if ($decimal < 0 || $decimal > 511) {
				if (!$disableError) {
					Core::throwError('Specified CHMOD is invalid.', INTERNAL_ERROR);
				}
				return false;
			}
			else {
				return true;
			}
		}
	}

}
?>
