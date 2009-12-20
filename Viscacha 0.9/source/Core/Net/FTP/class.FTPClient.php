<?php
/**
 * pemftp - Advanced FTP client class
 *
 * Copyright (C) 2008 by Alexey Dotsenko
 *
 * This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of the
 * License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package		Core
 * @subpackage	Net
 * @version		2008-09-17
 * @author		Alexey Dotsenko
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2008, Alexey Dotsenko
 * @license		http://www.gnu.org/licenses/lgpl-2.1.txt GNU Lesser General Public License
 */

/**
 * Base FTP class
 *
 * @package		Core
 * @subpackage	Net
 * @author		Alexey Dotsenko
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2008, Alexey Dotsenko
 * @since 		0.8
 * @abstract
 */
abstract class FTPClient {

	const CRLF = "\r\n";

	const AUTO = -1;
	const BINARY = 1;
	const ASCII = 0;

	const FORCE = true;

	public $LocalEcho;
	public $Verbose;
	public $OS_local;
	public $OS_remote;

	protected $lastaction;
	protected $errors;
	protected $type;
	protected $umask;
	protected $timeout;
	protected $passive;
	protected $host;
	protected $fullhost;
	protected $port;
	protected $datahost;
	protected $dataport;
	protected $ftp_control_sock;
	protected $ftp_data_sock;
	protected $ftp_temp_sock;
	protected $ftp_buff_size;
	protected $login;
	protected $password;
	protected $connected;
	protected $ready;
	protected $code;
	protected $message;
	protected $can_restore;
	protected $port_available;
	protected $curtype;
	protected $features;
	protected $OS_FullName;
	protected $error_array;
	protected $AuthorizedTransferMode;
	protected $eol_code;
	protected $AutoAsciiExt;

	public function __construct($port_mode = false, $verb = false, $le = false) {
		$this->LocalEcho = $le;
		$this->Verbose = $verb;
		$this->lastaction = null;
		$this->error_array = array();
		$this->eol_code = array(
			System::UNIX => "\n",
			System::MAC => "\r",
			System::WINDOWS => "\r\n"
		);
		$this->AuthorizedTransferMode = array(self::AUTO, self::ASCII, self::BINARY);
		$this->OS_FullName = array(
			System::UNIX => 'Unix',
			System::WINDOWS => 'Windows',
			System::MAC => 'MacOS'
		);
		$this->AutoAsciiExt = array(
			"asp",
			"aspx",
			"bat",
			"c",
			"cfc",
			"cfm",
			"cgi",
			"conf",
			"cpp",
			"css",
			"csv",
			"js",
			"h",
			"hpp",
			"hta",
			"htaccess",
			"htm",
			"html",
			"java",
			"inc",
			"ini",
			"inf",
			"log",
			"nfo",
			"pas",
			"php",
			"php3",
			"php4",
			"php5",
			"php6",
			"phtml",
			"pl",
			"perl",
			"sh",
			"shtml",
			"svg",
			"sql",
			"txt",
			"vb",
			"vbs",
			"wml",
			"xhtml",
			"xml",
			"xsd",
			"xsl",
			"xslt",
			"xul"
		);
		$this->port_available = ($port_mode == true);
		$this->sendMsg(
			"Staring FTP client class".
				($this->port_available ? "" : " without PORT mode support")
		);
		$this->connected = false;
		$this->ready = false;
		$this->can_restore = false;
		$this->code = 0;
		$this->message = '';
		$this->ftp_buff_size = 4096;
		$this->curtype = null;
		$this->login = 'anonymous';
		$this->password = '';
		$this->features = array();
	    $this->OS_local = System::getOS();
		$this->OS_remote = System::UNIX;
		$this->features = array();
		$this->setUmask(0022);
		$this->setType(self::AUTO);
		$this->setTimeout(30);
		$this->passive(!$this->port_available);

	}

	/**
	 * Returns a new instance for ftp handling that suits the php configuration best.
	 *
	 * If ftp extension can't be used null will be returned.
	 */
	public static function getObject($verbose = false, $localeEcho = false) {
		$object = null;
		if (extension_loaded('ftp')) {
			$object = new FTPClientExtension($verbose, $localeEcho);
		}
		else {
			$modSockets = true;
			if (!extension_loaded('sockets')) {
				if (!function_exists('dl')) {
					$modSockets = false;
				}
				else {
					$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
					if(!@dl($prefix . 'sockets.' . PHP_SHLIB_SUFFIX)) {
						$modSockets = false;
					}
				}
			}
			if ($modSockets == true) {
				$object =  new FTPClientSockets($verbose, $localeEcho);
			}
			elseif ($modSockets == false && function_exists('fsockopen')) {
				$object =  new FTPClientNative($verbose, $localeEcho);
			}
		}
		return $object;
	}

	/**
	 * Parses a file listing line received from the ftp server.
	 *
	 * Parses line like: "drwxrwx---  2 owner group 4096 Apr 23 14:57 text" received from list -l
	 * FTP command and return array( type, perms, inode, owner, group, size, date, name )
	 *
	 * @param string Line to parse
	 * @return array Parsed result as array
	 */
	public function parseListing($list) {
		$match = preg_match(
			"/^([-ld])([rwxst-]+)\s+(\d+)\s+([^\s]+)\s+([^\s]+)\s+(\d+)\s+(\w{3})\s+(\d+)\s+".
				"([\:\d]+)\s+(.+)$/i",
			$list,
			$ret
		);
		if($match) {
			$v = array(
				"type"	=> ($ret[1] == "-" ? "f" : $ret[1]),
				"perms"	=> 0,
				"inode"	=> $ret[3],
				"owner"	=> $ret[4],
				"group"	=> $ret[5],
				"size"	=> $ret[6],
				"date"	=> $ret[7]." ".$ret[8]." ".$ret[9],
				"name"	=> $ret[10]
			);
			$bad = array("(?)");
			if(in_array($v["owner"], $bad)) {
				$v["owner"] = null;
			}
			if(in_array($v["group"], $bad)) {
				$v["group"] = null;
			}
			$v["perms"] += 00400*(int)($ret[2]{0}=="r");
			$v["perms"] += 00200*(int)($ret[2]{1}=="w");
			$v["perms"] += 00100*(int)in_array($ret[2]{2}, array("x","s"));
			$v["perms"] += 00040*(int)($ret[2]{3}=="r");
			$v["perms"] += 00020*(int)($ret[2]{4}=="w");
			$v["perms"] += 00010*(int)in_array($ret[2]{5}, array("x","s"));
			$v["perms"] += 00004*(int)($ret[2]{6}=="r");
			$v["perms"] += 00002*(int)($ret[2]{7}=="w");
			$v["perms"] += 00001*(int)in_array($ret[2]{8}, array("x","t"));
			$v["perms"] += 04000*(int)in_array($ret[2]{2}, array("S","s"));
			$v["perms"] += 02000*(int)in_array($ret[2]{5}, array("S","s"));
			$v["perms"] += 01000*(int)in_array($ret[2]{8}, array("T","t"));
		}
		return $v;
	}

	public function sendMsg($message = '', $crlf = true) {
		if ($this->Verbose) {
			echo $message.($crlf ? CRLF : '');
			flush();
		}
		return true;
	}

	/**
	 * Sets transfer type.
	 *
	 * Possible values: FTPClient::AUTO (default), FTPClient::ASCII, FTPClient::BINARY
	 */
	public function setType($mode = self::AUTO) {
		if(!in_array($mode, $this->AuthorizedTransferMode)) {
			$this->sendMsg("Wrong type");
			return false;
		}
		else {
			$this->type = $mode;
			$this->sendMSG(
				"Transfer type: ".
					($this->type == self::BINARY ? "binary" :
						($this->type == self::ASCII ? "ASCII" : "auto ASCII")
					)
			);
			return true;
		}
	}

	protected function _setType($mode = self::ASCII) {
		if($this->ready) {
			if($mode == self::BINARY) {
				if($this->curtype != self::BINARY) {
					if(!$this->_exec("TYPE I", "SetType")) {
						return false;
					}
					$this->curtype = self::BINARY;
				}
			}
			elseif($this->curtype != self::ASCII) {
				if(!$this->_exec("TYPE A", "SetType")) {
					return false;
				}
				$this->_curtype = self::ASCII;
			}
		}
		else {
			return false;
		}
		return true;
	}

	/**
	 * Set what mode to use during connection: passive or not passive.
	 *
	 * Not passive mode available only if sockets module available in PHP.
	 */
	public function passive($pasv = null) {
		if($pasv === null) {
			$this->passive = (!$this->passive);
		}
		else {
			$this->passive = $pasv;
		}
		if(!$this->port_available && !$this->passive) {
			$this->sendMsg("Only passive connections available!");
			$this->passive = true;
			return false;
		}
		$this->sendMsg("Passive mode ".($this->passive ? "on" : "off"));
		return true;
	}

	/**
	 * Set ftp host
	 */
	public function setServer($host, $port = 21, $reconnect = true) {
		if(!is_long($port)) {
	        $this->verbose = true;
    	    $this->sendMsg("Incorrect port syntax");
			return false;
		}
		else {
			$ip = @gethostbyname($host);
	        $dns = @gethostbyaddr($host);
	        if(!$ip) {
				$ip = $host;
			}
	        if(!$dns) {
				$dns = $host;
			}
			if(ip2long($ip) === -1) {
				$this->sendMsg("Wrong host name/address '{$host}'");
				return false;
			}
	        $this->host = $ip;
	        $this->fullhost = $dns;
	        $this->port = $port;
	        $this->dataport = $port-1;
		}
		$this->sendMsg("Host '{$this->fullhost}({$this->host}):{$this->port}'");
		if($reconnect && $this->connected) {
			$this->sendMsg("Reconnecting");
			if(!$this->quit(self::FORCE)) {
				return false;
			}
			if(!$this->connect()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Set umask for local file save.
	 */
	public function setUmask($umask = 0022) {
		$this->umask = $umask;
		umask($this->umask);
		$this->sendMSG("UMASK 0".decoct($this->umask));
		return true;
	}

	/**
	 * Set timeout in seconds.
	 */
	public function setTimeout($timeout = 30) {
		$this->timeout = $timeout;
		$this->sendMsg("Timeout ".$this->timeout);
		if($this->connected && !$this->_setTimeout($this->ftp_control_sock)) {
			return false;
		}
		return true;
	}

	public function connect() {
		if($this->ready) {
			return true;
		}
		$this->sendMsg('Local OS: '.$this->OS_FullName[$this->OS_local]);
		$this->ftp_control_sock = $this->_connect($this->host, $this->port);
		if(!$this->ftp_control_sock) {
			$this->sendMsg(
				"Error: Cannot connect to remote host '{$this->fullhost}:{$this->port}'"
			);
			return false;
		}
		$this->sendMsg(
			"Connected to remote host '{$this->_fullhost}:{$this->_port}'. Waiting for greeting."
		);
		do {
			if(!$this->_readMsg()) {
				return false;
			}
			if(!$this->_checkCode()) {
				return false;
			}
			$this->_lastaction = time();
		} while($this->code < 200);
		$this->ready = true;
		$this->detectOS();
		if(!$this->features()) {
			$this->sendMsg("Can't get features list. All supported - disabled");
		}
		else {
			$this->sendMsg("Supported features: ".implode(", ", array_keys($this->features)));
		}
		return true;
	}

	protected function detectOS() {
		$syst = $this->systype();
		if(!$syst) {
			$this->sendMsg("Can't detect remote OS");
			return false;
		}
		else {
			if(preg_match("/win|dos|novell/i", $syst[0])) {
				$this->OS_remote = System::WINDOWS;
			}
			elseif(preg_match("/os/i", $syst[0])) {
				$this->OS_remote = System::MAC;
			}
			elseif(preg_match("/(li|u)nix/i", $syst[0])) {
				$this->OS_remote = System::UNIX;
			}
			else {
				$this->OS_remote = System::MAC;
			}
			$this->sendMsg("Remote OS: ".$this->OS_FullName[$this->OS_remote]);
			return true;
		}
	}

	/**
	 * Close FTP connection
	 */
	public function quit($force = false) {
		if($this->ready) {
			if(!$this->_exec("QUIT") && !$force) {
				return false;
			}
			if(!$this->_checkCode() && !$force) {
				return false;
			}
			$this->_ready = false;
			$this->sendMsg("Session finished");
		}
		$this->_quit();
		return true;
	}

	/**
	 * Login to FTP server
	 */
	public function login($user = null, $pass = null) {
		if($user !== null) {
			$this->login = $user;
		}
		else {
			$this->login = "anonymous";
		}
		if($pass !== null) {
			$this->password = $pass;
		}
		else {
			$this->password = "";
		}
		if(!$this->_exec("USER ".$this->login, "login")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		if($this->code != 230) {
			$code = ($this->code == 331) ? "PASS " : "ACCT ";
			if(!$this->_exec($code.$this->password, "login")) {
				return false;
			}
			if(!$this->_checkCode()) {
				return false;
			}
		}
		$this->sendMsg("Authentication succeeded");
		if(empty($this->features)) {
			if(!$this->features()) {
				$this->sendMsg("Can't get features list. All supported - disabled");
			}
			else {
				$this->sendMsg("Supported features: ".implode(", ", array_keys($this->features)));
			}
		}
		return true;
	}

	/**
	 * Current FTP path
	 */
	public function pwd() {
		if(!$this->_exec("PWD", "pwd")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return preg_replace("~^[0-9]{3} \"(.+)\" .+".self::CRLF."~", "\\1", $this->message);
	}

	/**
	 * CDUP command
	 */
	public function cdup() {
		if(!$this->_exec("CDUP", "cdup")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * Change directory
	 */
	public function chdir($pathname) {
		if(!$this->_exec("CWD ".$pathname, "chdir")) {
			return false;
		}
		elseif(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * Delete directory
	 */
	public function rmdir($pathname) {
		if(!$this->_exec("RMD ".$pathname, "rmdir")) {
			return false;
		}
		elseif(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * Make directory
	 */
	public function mkdir($pathname) {
		if(!$this->_exec("MKD ".$pathname, "mkdir")) {
			return false;
		}
		elseif(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * Rename file
	 */
	public function rename($from, $to) {
		if(!$this->_exec("RNFR ".$from, "rename")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		if($this->code == 350) {
			if(!$this->_exec("RNTO ".$to, "rename")) {
				return false;
			}
			if(!$this->_checkCode()) {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}

	/**
	 * Get file size
	 */
	public function filesize($pathname) {
		if(!isset($this->features["SIZE"])) {
			$this->pushError("filesize", "not supported by server");
			return false;
		}
		if(!$this->_exec("SIZE ".$pathname, "filesize")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return preg_replace("~^[0-9]{3} ([0-9]+)".self::CRLF."~", "\\1", $this->message);
	}

	public function abort() {
		if(!$this->_exec("ABOR", "abort")) {
			return false;
		}
		if(!$this->_checkCode()) {
			if($this->code != 426) {
				return false;
			}
			if(!$this->_readMsg("abort")) {
				return false;
			}
			if(!$this->_checkCode()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Return file timestamp
	 */
	public function mdtm($pathname) {
		if(!isset($this->features["MDTM"])) {
			$this->pushError("mdtm", "not supported by server");
			return false;
		}
		if(!$this->_exec("MDTM ".$pathname, "mdtm")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		$mdtm = preg_replace("~^[0-9]{3} ([0-9]+)".self::CLRF."~", "\\1", $this->message);
		$date = sscanf($mdtm, "%4d%2d%2d%2d%2d%2d");
		$timestamp = mktime($date[3], $date[4], $date[5], $date[1], $date[2], $date[0]);
		return $timestamp;
	}

	/**
	 * Command SYST
	 */
	public function systype() {
		if(!$this->_exec("SYST", "systype")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		$DATA = explode(" ", $this->message);
		return array($DATA[1], $DATA[3]);
	}

	/**
	 * Delete file
	 */
	public function delete($pathname) {
		if(!$this->_exec("DELE ".$pathname, "delete")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * SITE command implementation
	 */
	public function site($command, $function = "site") {
		if(!$this->_exec("SITE ".$command, $function)) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * Set CHMOD
	 */
	public function chmod($pathname, $mode) {
		return $this->site("CHMOD ".decoct($mode)." ".$pathname, "chmod");
	}

	/**
	 * REST offset
	 */
	public function restore($from) {
		if(!isset($this->features["REST"])) {
			$this->pushError("restore", "not supported by server");
			return false;
		}
		if($this->curtype != self::BINARY) {
			$this->pushError("restore", "can't restore in ASCII mode");
			return false;
		}
		if(!$this->_exec("REST ".$from, "resore")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return true;
	}

	/**
	 * FEAT command
	 */
	public function features() {
		if(!$this->_exec("FEAT", "features")) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		$f = array_slice(
			preg_split("/[".self::CRLF."]+/", $this->message, -1, PREG_SPLIT_NO_EMPTY),
			1,
			-1
		);
		$walk = function(&$a) {
			$a = preg_replace("/[0-9]{3}[\s-]+/", "", trim($a));
		};
		array_walk($f, $walk);
		$this->features = array();
		foreach($f as $k => $v) {
			$v = explode(" ", trim($v));
			$this->features[array_shift($v)] = $v;
		}
		return true;
	}

	/**
	 * Raw FTP listing
	 */
	public function rawList($pathname = "", $arg = "") {
		return $this->_list(
			($arg ? " ".$arg : "").
				($pathname ? " ".$pathname : ""),
			"LIST",
			"rawlist"
		);
	}

	/**
	 * FTP listing
	 */
	public function nList($pathname = "") {
		return $this->_list(
			($arg ? " ".$arg : "").
				($pathname ? " ".$pathname : ""),
			"NLST",
			"nlist"
		);
	}

	/**
	 * Return true if file/folder exists.
	 */
	public function exists($pathname) {
		$exists = true;
		if(!$this->_exec("RNFR ".$pathname, "rename")) {
			$exists = false;
		}
		else {
			if(!$this->_checkCode()) {
				$exists = false;
			}
			$this->abort();
		}
		if($exists) {
			$this->sendMsg("Remote file {$pathname} exists");
		}
		else {
			$this->sendMsg("Remote file {$pathname} does not exist");
		}
		return $exists;
	}

	/**
	 * Receive remotefile from FTP server and save it as localfile or return contents of this file
	 */
	public function get($remotefile, $localfile = null, $rest = 0) {
		if($localfile === null) {
			$localfile = $remotefile;
		}
		if (file_exists($localfile)) {
			$this->sendMsg("Warning: local file will be overwritten");
		}
		$fp = @fopen($localfile, "w");
		if (!$fp) {
			$this->pushError("get", "can't open local file", "Cannot create '{$localfile}'");
			return false;
		}
		if($this->can_restore && $rest != 0) {
			fseek($fp, $rest);
		}
		$file = new File($remotefile);
		if($this->type == self::ASCII || ($this->type == self::AUTO && in_array($file->extension(), $this->AutoAsciiExt))) {
			$mode = self::ASCII;
		}
		else {
			$mode = self::BINARY;
		}
		if(!$this->_data_prepare($mode)) {
			fclose($fp);
			return false;
		}
		if($this->can_restore && $rest != 0) {
			$this->restore($rest);
		}
		if(!$this->_exec("RETR ".$remotefile, "get")) {
			$this->_data_close();
			fclose($fp);
			return false;
		}
		if(!$this->_checkCode()) {
			$this->_data_close();
			fclose($fp);
			return false;
		}
		$out = $this->_data_read($mode, $fp);
		fclose($fp);
		$this->_data_close();
		if(!$this->_readMsg()) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return $out;
	}

	/**
	 * Store localfile to FTP server as remotefile or with the same name
	 */
	public function put($localfile, $remotefile = null, $rest = 0) {
		if (!file_exists($localfile) && !is_resource($localfile)) {
			$this->pushError(
				"put",
				"can't open local file",
				"No such file or directory '{$localfile}'"
			);
			return false;
		}
		if (is_resource($localfile)) {
			$fp = $localfile;
			$localfile = $remotefile;
			if (!is_string($remotefile)) {
				$this->pushError(
					"put",
					"second paramater is not a string",
					"String needed, when first parameter is resource."
				);
			}
		}
		else {
			if ($remotefile === null) {
				$remotefile = $localfile;
			}
			$fp = @fopen($localfile, "r");
			if (!$fp) {
				$this->pushError(
					"put",
					"can't open local file",
					"Cannot read file '{$localfile}'"
				);
				return false;
			}
		}

		if($this->can_restore && $rest != 0) {
			fseek($fp, $rest);
		}
		$file = new File($localfile);
		if($this->type == self::ASCII || ($this->type == self::AUTO && in_array($file->extension(), $this->AutoAsciiExt))) {
			$mode = self::ASCII;
		}
		else {
			$mode = self::BINARY;
		}
		if(!$this->_data_prepare($mode)) {
			fclose($fp);
			return false;
		}
		if($this->can_restore && $rest != 0) {
			$this->restore($rest);
		}
		if(!$this->_exec("STOR ".$remotefile, "put")) {
			$this->_data_close();
			fclose($fp);
			return false;
		}
		if(!$this->_checkCode()) {
			$this->data_close();
			fclose($fp);
			return false;
		}
		$ret = $this->_data_write($mode, $fp);
		fclose($fp);
		$this->_data_close();
		if(!$this->_readMsg()) {
			return false;
		}
		if(!$this->_checkCode()) {
			return false;
		}
		return $ret;
	}

	/**
	 * Like put but can work with folder tree
	 */
	public function mput($local = '.', $remote = null, $continious = false) {
		$local = realpath($local);
		if(!file_exists($local)) {
			$this->pushError("mput", "can't open local folder", "Cannot stat folder '{$local}'");
			return false;
		}
		if(!is_dir($local)) {
			return $this->put($local, $remote);
		}
		if(empty($remote)) {
			$remote = '.';
		}
		elseif(!$this->exists($remote) && !$this->mkdir($remote)) {
			return false;
		}

		$handle = opendir($local);
		if(!$handle) {
			$list = array();
			while (($file = readdir($handle)) !== false) {
				if ($file != "." && $file != "..") {
					$list[] = $file;
				}
			}
			closedir($handle);
		}
		else {
			$this->pushError("mput", "can't open local folder", "Cannot read folder '{$local}'");
			return false;
		}
		if(empty($list)) {
			return true;
		}
		$ret = true;
		foreach($list as $el) {
			if(is_dir($local."/".$el)) {
				$t = $this->mput($local."/".$el, $remote."/".$el);
			}
			else {
				$t = $this->put($local."/".$el, $remote."/".$el);
			}
			if(!$t) {
				$ret = false;
				if(!$continious) {
					break;
				}
			}
		}
		return $ret;
	}

	/**
	 * Like get but can work with folder tree
	 */
	public function mget($remote, $local = '.', $continious = false) {
		$list = $this->rawList($remote, "-lA");
		if($list === false) {
			$this->pushError(
				"mget",
				"can't read remote folder list",
				"Can't read remote folder '{$remote}' contents"
			);
			return false;
		}
		if(empty($list)) {
			return true;
		}
		if(!@file_exists($local)) {
			if(!@mkdir($local)) {
				$this->pushError(
					"mget",
					"can't create local folder",
					"Cannot create folder '{$local}'"
				);
				return false;
			}
		}
		foreach($list as $k => $v) {
			$list[$k] = $this->parseListing($v);
			if($list[$k]["name"] == '.' || $list[$k]["name"] == '..') {
				unset($list[$k]);
			}
		}
		$ret = true;
		foreach($list as $el) {
			if($el["type"] == "d") {
				if(!$this->mget($remote."/".$el["name"], $local."/".$el["name"], $continious)) {
					$this->pushError(
						"mget",
						"can't copy folder",
						"Can't copy remote folder '{$remote}/{$el['name']}' to ".
							"local '{$local}/{$el['name']}'"
					);
					$ret = false;
					if(!$continious) {
						break;
					}
				}
			}
			else {
				if(!$this->get($remote."/".$el["name"], $local."/".$el["name"])) {
					$this->pushError(
						"mget",
						"can't copy file",
						"Can't copy remote file '{$remote}/{$el['name']}' to ".
							"local '{$local}/{$el["name"]}'"
					);
					$ret = false;
					if(!$continious) {
						break;
					}
				}
			}
			@chmod($local."/".$el["name"], $el["perms"]);
			$t = strtotime($el["date"]);
			if($t !== -1 && $t !== false) {
				@touch($local."/".$el["name"], $t);
			}
		}
		return $ret;
	}

	public function mdel($remote, $continious = false) {
		$list = $this->rawList($remote, "-la");
		if($list === false) {
			$this->pushError(
				"mdel",
				"can't read remote folder list",
				"Can't read remote folder '{$remote}' contents"
			);
			return false;
		}

		foreach($list as $k => $v) {
			$list[$k] = $this->parseListing($v);
			if($list[$k]["name"] == '.' || $list[$k]["name"] == '..') {
				unset($list[$k]);
			}
		}
		$ret = true;

		foreach($list as $el) {
			if($el["type"] == "d") {
				if(!$this->mdel($remote."/".$el["name"], $continious)) {
					$ret = false;
					if(!$continious) {
						break;
					}
				}
			}
			elseif (!$this->delete($remote."/".$el["name"])) {
				$this->pushError(
					"mdel",
					"can't delete file",
					"Can't delete remote file '{$remote}/{$el['name']}'"
				);
				$ret = false;
				if(!$continious) {
					break;
				}
			}
		}

		if(!$this->rmdir($remote)) {
			$this->pushError(
				"mdel",
				"can't delete folder",
				"Can't delete remote folder '{$remote}/{$el['name']}'"
			);
			$ret = false;
		}
		return $ret;
	}

	public function glob($pattern, $handle = null) {
		$path = null;
		$output = null;
		if(isWindows() == true) {
			$slash='\\';
		}
		else {
			$slash='/';
		}
		$lastpos = strrpos($pattern, $slash);
		if($lastpos !== false) {
			$path = substr($pattern, 0, -$lastpos-1);
			$pattern = substr($pattern, $lastpos);
		}
		else {
			$path = getcwd();
		}
		if(is_array($handle) && !empty($handle)) {
			while($dir = each($handle)) {
				if($this->glob_pattern_match($pattern, $dir)) {
					$output[] = $dir;
				}
			}
		}
		else {
			$handle = @opendir($path);
			if($handle === false) {
				return false;
			}
			while($dir = readdir($handle)) {
				if($this->glob_pattern_match($pattern, $dir)) {
					$output[] = $dir;
				}
			}
			closedir($handle);
		}
		if(is_array($output)) {
			return $output;
		}
		else {
			return false;
		}
	}

	public function glob_pattern_match($pattern, $string) {
		$out = null;
		$chunks = explode(';', $pattern);
		foreach($chunks as $pattern) {
			$escape = array('$', '^', '.', '{', '}', '(', ')', '[', ']', '|');
			while(strpos($pattern, '**') !== false) {
				$pattern = str_replace('**', '*', $pattern);
			}
			foreach($escape as $probe) {
				$pattern = str_replace($probe, "\\{$probe}", $pattern);
			}
			$pattern = str_replace(
				array('?*', '*?', '*', '?'),
				array('*', '*', '.*', '.{1,1}'),
				$pattern
			);
			$out[] = $pattern;
		}
		if(count($out) == 1) {
			return $this->glob_regexp("^{$out[0]}$", $string);
		}
		else {
			foreach($out as $tester) {
				if($this->my_regexp("^{$tester}$", $string)) {
					return true;
				}
			}
		}
		return false;
	}

	public function glob_regexp($pattern, $probe) {
		$cs = (System::getOS() == System::WINDOWS) ? 'i' : '';
		return preg_match("~{$pattern}~{$cs}", $probe);
	}

	protected function _checkCode() {
		return ($this->code < 400 && $this->code > 0);
	}

	protected function _list($arg = '', $cmd = 'LIST', $function = '_list') {
		if(!$this->_data_prepare()) {
			return false;
		}
		if(!$this->_exec($cmd.$arg, $function)) {
			$this->_data_close();
			return false;
		}
		if(!$this->_checkCode()) {
			$this->_data_close();
			return false;
		}
		$out = '';
		if($this->code < 200) {
			$out = $this->_data_read();
			$this->_data_close();
			if(!$this->_readmsg()) {
				return false;
			}
			if(!$this->_checkCode()) {
				return false;
			}
			if($out === false) {
				return false;
			}
			$out = preg_split("/[".self::CRLF."]+/", $out, -1, PREG_SPLIT_NO_EMPTY);
		}
		return $out;
	}

	protected function pushError($fctname, $msg, $desc = false) {
		$error = array();
		$error['time'] = time();
		$error['fctname'] = $fctname;
		$error['msg'] = $msg;
		$error['desc'] = $desc;
		if($desc) {
			$tmp = " ({$desc})";
		}
		 else {
			 $tmp = '';
		 }
		$this->sendMsg($fctname.': '.$msg.$tmp);
		return array_push($this->error_array, $error);
	}

	protected function popError() {
		if(count($this->error_array)) {
			return array_pop($this->error_array);
		}
		else {
			return false;
		}
	}
}
?>