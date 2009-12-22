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

Core::loadClass('Core.Net.FTP.FTPClient');

/**
 * FTP class that uses the ftp extension of PHP.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Matthias Mohr
 * @since 		0.8
 */
class FTPClientExtension extends FTPClient {

	public function __construct($verb = false, $le = false) {
		parent::__construct(false, $verb, $le);
	}

	protected function _settimeout($sock) {
		if(!ftp_set_option($sock, FTP_TIMEOUT_SEC, $this->timeout)) {
			$this->pushError('_settimeout', 'ftp set send timeout');
			$this->_quit();
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
			$this->sendMsg("Error: Cannot connect to remote host '{$this->fullhost}:{$this->port}'");
			return false;
		}
		$this->sendMsg("Connected to remote host '{$this->fullhost}:{$this->port}'. Waiting for greeting.");
		$this->lastaction = time();
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

	protected function _connect($host, $port) {
		$this->sendMsg("Creating ftp connection");
		$sock = ftp_connect($host, $port, $this->timeout);
		if (!$sock) {
			$this->pushError('_connect', 'ftp connect failed');
			return false;
		}
		else {
			$this->connected = true;
			return $sock;
		}
	}

	public function get($remotefile, $localfile = null, $rest = 0) {
		if(!$this->ready) {
			$this->pushError('get', 'Connect first');
			return false;
		}

		if($localfile === null) {
			$localfile = $remotefile;
		}
		if (file_exists($localfile)) {
			$this->sendMsg("Warning: local file will be overwritten");
		}

		$file = new File($remotefile);
		if($this->type == self::ASCII || ($this->type == self::AUTO && in_array($file->extension(), $this->AutoAsciiExt))) {
			$mode = self::ASCII;
		}
		else {
			$mode = self::BINARY;
		}

		if(!$this->can_restore) {
			$rest = 0;
		}
		$status = ftp_get($this->ftp_control_sock, $localfile, $remotefile, $mode, $rest);
		if ($status == true) {
			return @file_get_contents($localfile);
		}
		else {
			return false;
		}
	}

	public function put($localfile, $remotefile = null, $rest = 0) {
		if(!$this->ready) {
			$this->pushError('put', 'Connect first');
			return false;
		}

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
				$this->pushError("put", "can't open local file", "Cannot read file '{$localfile}'");
				return false;
			}
		}

		$file = new File($localfile);
		if($this->type == self::ASCII || ($this->type == self::AUTO && in_array($file->extension(), $this->AutoAsciiExt))) {
			$mode = self::ASCII;
		}
		else {
			$mode = self::BINARY;
		}

		if(!$this->can_restore) {
			$rest = 0;
		}
		if($rest > 0) {
			fseek($fp, $rest);
		}

		$status = ftp_fput($this->ftp_control_sock, $remotefile, $fp, $mode, $rest);
		fclose($fp);

		return $status;
	}

	protected function _list($arg = '', $cmd = 'LIST', $function = '_list') {
		if(!$this->ready) {
			$this->pushError($function, 'Connect first');
			return false;
		}

		if ($cmd == 'NLST') {
			$contents = ftp_nlist($this->ftp_control_sock, $arg);
		}
		else {
			$contents = ftp_rawlist($this->ftp_control_sock, $arg);
		}

		return $contents;
	}

	protected function _readMsg($function = "_readmsg") {
		if(!$this->connected) {
			$this->pushError($function, 'Connect first');
			return false;
		}
		if (!is_array($this->_ftp_data_sock)) {
			$this->pushError($function, 'No data retrieved');
			return false;
		}
		$result = true;
		$this->message = implode(self::CRLF, $this->ftp_data_sock).self::CRLF;
		$this->code = 0;
		$m = preg_match(
			"/^([0-9]{3})(-(.*[".self::CRLF."]{1,2})+\\1)? [^".self::CRLF."]+[".self::CRLF."]{1,2}$/m",
			$this->_message,
			$regs
		);
		if(!$m) {
			$this->pushError($function, 'Invalid response from FTP');
			return false;
		}
		if($this->LocalEcho) {
			echo "GET < ".Strings::trimLineBreaks($this->message).self::CRLF;
		}
		$this->code = (int) $regs[1];
		return $result;
	}

	protected function _exec($cmd, $function = "_exec") {
		if(!$this->ready) {
			$this->pushError($function, 'Connect first');
			return false;
		}
		if($this->LocalEcho) {
			echo "PUT > ".$cmd.self::CRLF;
		}
		$this->ftp_data_sock = ftp_raw($this->ftp_control_sock, $cmd);
		$this->lastaction = time();
		return $this->_readMsg($function);
	}

	protected function _quit($force = false) {
		if($this->connected || $force) {
			ftp_close($this->ftp_control_sock);
			$this->connected = false;
			$this->sendMsg("FTP closed");
		}
	}
}
?>