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
 * FTP class that uses the sockets extension of PHP.
 *
 * @package		Core
 * @subpackage	Net
 * @author		Alexey Dotsenko
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2008, Alexey Dotsenko
 * @since 		0.8
 */
class FTPClientSockets extends FTPClient {

	public function __construct($verb = false, $le = false) {
		parent::__construct(true, $verb, $le);
	}

	protected function _setTimeout($sock) {
		$timeout = array(
			'sec' => $this->timeout,
			'usec' => 0
		);
		if(!@socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout)) {
			$se = socket_strerror(socket_last_error($sock));
			$this->pushError('_connect', 'socket set receive timeout', $se);
			@socket_close($sock);
			return false;
		}
		if(!@socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, $timeout)) {
			$se = socket_strerror(socket_last_error($sock));
			$this->pushError('_connect', 'socket set send timeout', $se);
			@socket_close($sock);
			return false;
		}
		return true;
	}

	protected function _connect($host, $port) {
		$this->sendMsg("Creating socket");
		$socketErr = socket_strerror(socket_last_error($sock));
		if(!($sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
			$this->pushError('_connect', 'socket create failed', $socketErr);
			return false;
		}
		if(!$this->_setTimeout($sock)) {
			return false;
		}
		$this->sendMsg("Connecting to '{$host}:{$port}'");
		if (!($res = @socket_connect($sock, $host, $port))) {
			$socketErr = socket_strerror(socket_last_error($sock));
			$this->pushError('_connect', 'socket connect failed', $socketErr);
			@socket_close($sock);
			return false;
		}
		$this->connected = true;
		return $sock;
	}

	protected function _readMsg($function = "_readMsg") {
		if(!$this->connected) {
			$this->pushError($function, 'Connect first');
			return false;
		}
		$result = true;
		$this->message = '';
		$this->code = 0;
		$go = true;
		do {
			$tmp = @socket_read($this->ftp_control_sock, 4096, PHP_BINARY_READ);
			if($tmp === false) {
				$go = false;
				$result = false;
				$socketErr = socket_strerror(socket_last_error($this->ftp_control_sock));
				$this->pushError($function, 'Read failed', $socketErr);
				$regs = array(0, 0);
			}
			else {
				$this->message .= $tmp;
				$go = !preg_match(
					"/^([0-9]{3})(-.+\\1)? [^".self::CRLF."]+".self::CRLF."$/Us",
					$this->message,
					$regs
				);
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
		$status = @socket_write($this->ftp_control_sock, $cmd.self::CRLF);
		if($status === false) {
			$se = socket_strerror(socket_last_error($this->stream));
			$this->pushError($function, 'socket write failed', $se);
			return false;
		}
		$this->lastaction = time();
		return $this->_readMsg($function);
	}

	protected function _data_prepare($mode = self::ASCII) {
		if(!$this->_settype($mode)) {
			return false;
		}
		$this->sendMsg("Creating data socket");
		$this->ftp_data_sock = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->ftp_data_sock < 0) {
			$socketErr = socket_strerror(socket_last_error($this->ftp_data_sock));
			$this->pushError('_data_prepare', 'socket create failed', $socketErr);
			return false;
		}
		if(!$this->_setTimeout($this->ftp_data_sock)) {
			$this->_data_close();
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
				$this->message
			);
			$ip_port = explode(",", $msg);
			$this->datahost = $ip_port[0].".".$ip_port[1].".".$ip_port[2].".".$ip_port[3];
			$this->dataport = (((int)$ip_port[4])<<8) + ((int)$ip_port[5]);
			$this->sendMsg("Connecting to {$this->datahost}:{$this->dataport}");
			if(!@socket_connect($this->ftp_data_sock, $this->datahost, $this->dataport)) {
				$socketErr = socket_strerror(socket_last_error($this->ftp_data_sock));
				$this->pushError("_data_prepare", "socket_connect", $socketErr);
				$this->_data_close();
				return false;
			}
			else {
				$this->ftp_temp_sock = $this->ftp_data_sock;
			}
		}
		else {
			if(!@socket_getsockname($this->ftp_control_sock, $addr, $port)) {
				$this->pushError(
					"_data_prepare",
					"can't get control socket information",
					socket_strerror(socket_last_error($this->ftp_control_sock))
				);
				$this->_data_close();
				return false;
			}
			if(!@socket_bind($this->ftp_data_sock, $addr)) {
				$this->pushError(
					"_data_prepare",
					"can't bind data socket",
					socket_strerror(socket_last_error($this->ftp_data_sock))
				);
				$this->_data_close();
				return false;
			}
			if(!@socket_listen($this->ftp_data_sock)) {
				$this->pushError(
					"_data_prepare",
					"can't listen data socket",
					socket_strerror(socket_last_error($this->ftp_data_sock))
				);
				$this->_data_close();
				return false;
			}
			if(!@socket_getsockname($this->ftp_data_sock, $this->datahost, $this->dataport)) {
				$this->pushError(
					"_data_prepare",
					"can't get data socket information",
					socket_strerror(socket_last_error($this->ftp_data_sock))
				);
				$this->_data_close();
				return false;
			}
			$port = str_replace(
				'.',
				',',
				$this->datahost.'.'.($this->dataport>>8).'.'.($this->dataport&0x00FF)
			);
			if(!$this->_exec('PORT '.$port, "_port")) {
				$this->_data_close();
				return false;
			}
			if(!$this->_checkCode()) {
				$this->_data_close();
				return false;
			}
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
			$this->sendMsg("Connecting to {$this->_datahost}:{$this->_dataport}");
			$this->ftp_temp_sock = socket_accept($this->ftp_data_sock);
			if($this->ftp_temp_sock === false) {
				$socketErr = socket_strerror(socket_last_error($this->ftp_temp_sock));
				$this->pushError("_data_read", "socket_accept", $socketErr);
				$this->_data_close();
				return false;
			}
		}

		while(($block = @socket_read($this->ftp_temp_sock, $this->ftp_buff_size, PHP_BINARY_READ)) !== false) {
			if($block === '') {
				break;
			}
			if($mode != self::BINARY) {
				$block = Strings::replaceLineBreaks($block, $this->_eol_code[$this->OS_local]);
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
			$this->sendMsg("Connecting to {$this->_datahost}:{$this->_dataport}");
			$this->ftp_temp_sock = socket_accept($this->ftp_data_sock);
			if($this->ftp_temp_sock === false) {
				$socketErr = socket_strerror(socket_last_error($this->ftp_temp_sock));
				$this->pushError("_data_write", "socket_accept", $socketErr);
				$this->_data_close();
				return false;
			}
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
			if(($t = @socket_write($this->ftp_temp_sock, $block)) === false) {
				$socketErr =  socket_strerror(socket_last_error($this->ftp_temp_sock));
				$this->pushError("_data_write", "socket_write", $ocketErr);
				$this->_data_close();
				return false;
			}
			$block = substr($block, $t);
		} while(!empty($block));
		return true;
	}

	protected function _data_close() {
		@socket_close($this->ftp_temp_sock);
		@socket_close($this->ftp_data_sock);
		$this->sendMsg("Disconnected data from remote host");
		return true;
	}

	protected function _quit() {
		if($this->connected) {
			@socket_close($this->ftp_control_sock);
			$this->connected = false;
			$this->sendMsg("Socket closed");
		}
	}
}
?>