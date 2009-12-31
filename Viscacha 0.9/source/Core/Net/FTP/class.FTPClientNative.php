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
 * FTP class that uses the native PHP code (fsockopen).
 *
 * @package		Core
 * @subpackage	Net
 * @author		Alexey Dotsenko
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2008, Alexey Dotsenko
 * @since 		0.8
 */
class FTPClientNative extends FTPClient {

	public function __construct($verb = false, $le = false) {
		parent::__construct(false, $verb, $le);
	}

	protected function _settimeout($sock) {
		if(!@stream_set_timeout($sock, $this->timeout)) {
			$this->pushError('_settimeout', 'socket set send timeout');
			$this->_quit();
			return false;
		}
		return true;
	}

	protected function _connect($host, $port) {
		$this->sendMsg("Creating socket");
		$sock = fsockopen(Networking::encodeIDNA($host), $port, $errno, $errstr, $this->timeout);
		if (!$sock) {
			$this->pushError('_connect', 'socket connect failed', $errstr." (".$errno.")");
			return false;
		}
		$this->connected = true;
		return $sock;
	}

	protected function _readMsg($function = "_readmsg") {
		if(!$this->connected) {
			$this->pushError($function, 'Connect first');
			return false;
		}
		$result = true;
		$this->message = '';
		$this->code = 0;
		$go = true;
		do {
			$tmp = @fgets($this->ftp_control_sock, 512);
			if($tmp === false) {
				$go = false;
				$result = false;
				$this->pushError($function, 'Read failed');
			}
			else {
				$this->message .= $tmp;
				$m = preg_match(
					"/^([0-9]{3})(-(.*[".self::CRLF."]{1,2})+\\1)? [^".self::CRLF."]+[".self::CRLF."]{1,2}$/",
					$this->message,
					$regs
				);
				if($m) {
					$go = false;
				}
			}
		} while($go);
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
		$status = @fputs($this->ftp_control_sock, $cmd.self::CRLF);
		if($status === false) {
			$this->pushError($function, 'socket write failed');
			return false;
		}
		$this->lastaction = time();
		if(!$this->_readMsg($function)) {
			return false;
		}
		return true;
	}

	protected function _data_prepare($mode = FTPClient::ASCII) {
		if(!$this->_settype($mode)) {
			return false;
		}
		if($this->passive) {
			if(!$this->_exec("PASV", "pasv")) {
				$this->_data_close();
				return false;
			}
			if(!$this->_checkCode()) {
				$this->_data_close();
				return false;
			}
			$msg = preg_replace(
				"~^.+ \\(?([0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]+,[0-9]+)\\)?.*".self::CRLF."$~",
				"\\1",
				$this->_message
			);
			$ip_port = explode(",", $msg);
			$this->datahost = $ip_port[0].".".$ip_port[1].".".$ip_port[2].".".$ip_port[3];
			$this->dataport = (((int)$ip_port[4])<<8) + ((int)$ip_port[5]);
			$this->sendMsg("Connecting to ".$this->datahost.":".$this->dataport);
			$this->ftp_data_sock = @fsockopen(
				Networking::encodeIDNA($this->datahost),
				$this->dataport,
				$errno,
				$errstr,
				$this->timeout
			);
			if(!$this->ftp_data_sock) {
				$this->pushError(
					"_data_prepare",
					"fsockopen fails (host: {$this->datahost})",
					"{$errstr} ({$errno})"
				);
				$this->_data_close();
				return false;
			}
		}
		else {
			$this->sendMsg("Only passive connections available!");
			return false;
		}
		return true;
	}

	protected function _data_read($mode = self::ASCII, $fp = null) {
		if(is_resource($fp)) {
			$out = 0;
		}
		else {
			$out = '';
		}
		if(!$this->passive) {
			$this->sendMsg("Only passive connections available!");
			return false;
		}
		while (!feof($this->ftp_data_sock)) {
			$block = fread($this->ftp_data_sock, $this->ftp_buff_size);
			if($mode != self::BINARY) {
				$block = Strings::replaceLineBreaks($block, $this->eol_code[$this->OS_local]);
			}
			if(is_resource($fp)) {
				$out += fwrite($fp, $block, strlen($block));
			}
			else {
				$out .= $block;
			}
		}
		return $out;
	}

	protected function _data_write($mode = self::ASCII, $fp = null) {
		if(!$this->passive) {
			$this->sendMsg("Only passive connections available!");
			return false;
		}
		if(is_resource($fp)) {
			while(!feof($fp)) {
				$block = fread($fp, $this->ftp_buff_size);
				if(!$this->_data_write_block($mode, $block)) {
					return false;
				}
			}
		}
		elseif(!$this->_data_write_block($mode, $fp)) {
			return false;
		}
		return true;
	}

	protected function _data_write_block($mode, $block) {
		if($mode != self::BINARY) {
			$block = Strings::replaceLineBreaks($block, $this->eol_code[$this->OS_remote]);
		}

		do {
			if(($t = @fwrite($this->ftp_data_sock, $block)) === false) {
				$this->pushError("_data_write", "Can't write to socket");
				return false;
			}
			$block = substr($block, $t);
		} while(!empty($block));

		return true;
	}

	protected function _data_close() {
		@fclose($this->ftp_data_sock);
		$this->sendMsg("Disconnected data from remote host");
		return true;
	}

	protected function _quit($force = false) {
		if($this->connected || $force) {
			@fclose($this->ftp_control_sock);
			$this->connected = false;
			$this->sendMsg("Socket closed");
		}
	}
}
?>