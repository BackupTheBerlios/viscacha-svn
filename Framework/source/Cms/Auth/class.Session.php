<?php
Core::loadClass("Cms.Auth.Authentication");
/**
 * Session handling and more.
 *
 * @package		Cms
 * @subpackage 	Auth
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Session {

	private $sid;
	private $ip;
	private $auth;
	private $settings;
	private $me;

	// Singleton
	private static $instance = NULL;
 
	public static function getObject() {
		if (self::$instance === NULL) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __clone() {}

	private function __construct() {
		$this->ip = new IPv4();
		$this->auth = new Authentication();
		$this->settings = array();
		$this->invalidate();
		$this->load();
	}

	private function loadSID() {
		// Get Session ID from Cookie / Query-String
		$cookie = Core::_(HTTPHEADER)->getCookie('sid');
		if (strlen($cookie) == 32) {
			return $cookie;
		}
		$query = Request::get('s');
		if(strlen($query) == 32) {
			return $query;
		}
		return null;
	}

	private function setSID($sid) {
		$this->sid = $sid;
		Core::_(HTTPHEADER)->setCookie('sid', $sid, 60 * Config::get('security.session_lifetime'), true);
	}

	public function getSID() {
		return $this->sid;
	}

	private function invalidate() {
		// Delete old sessions
		$time = time()-Config::get('security.session_lifetime') * 60;
		Core::_(DB)->query("DELETE FROM <p>session WHERE visit <= <time:int>", compact("time"));
	}

	// 0 = Guest, > 0 Member ID, -1 = Invalid
	private function check($sid) {
		if (strlen($sid) != 32) {
			return -1;
		}

		$ip = Sanitize::saveDb($this->ip->getIP(3));
		$db = Core::_(DB);
		$db->query(
			"SELECT user_id, settings FROM <p>session WHERE sid = <sid> AND ip LIKE '<ip:raw>%'",
			compact("sid", "ip")
		);
		if ($db->numRows() == 1) {
			$data = $db->fetchAssoc();
			if (!empty($data['settings'])) {
				$this->settings = unserialize($data['settings']);
			}
			if ($data['user_id'] === null || $data['user_id'] < 0) {
				$data['user_id'] = -1;
			}
			return $data['user_id'];
		}
	}

	private function create($uid) {
		// No valid session found: Insert a new session, try to login as user
		$sid = Hash::getRandom();
		$data = array(
			'sid' => $sid,
			'time' => time(),
			'ip' => Sanitize::saveDb($this->ip->getIP()),
			'settings' => serialize($this->settings),
			'uid' => $uid
		);
		Core::_(DB)->query("INSERT INTO <p>session SET user_id = <uid:int>, sid = <sid>, visit = <time:int>, ip = '<ip:raw>', settings = <settings>", $data);
		return $sid;
	}

	private function load() {
		// Session handling
		$sid = $this->loadSID();
		$uid = $this->check($sid);
		if ($uid == -1) {
			$this->me = $this->auth->loginWithCookies();
			$sid = $this->create($this->me->getId());
		}
		else {
			$this->me = $this->auth->loginWithId($uid);
		}
		$this->setSID($sid);
	}

	public function update() {
		$data = array(
			'time' => time(),
			'settings' => serialize($this->settings),
			'sid' => $this->sid,
			'uid' => $this->me->getId()
		);
		Core::_(DB)->query("UPDATE <p>session SET visit = <time:int>, settings = <settings>, user_id = <uid:int> WHERE sid = <sid>", $data);
		if ($data['uid'] > 0) {
			Core::_(DB)->query("UPDATE <p>user SET lastvisit = <time:int> WHERE id = <uid:int>", $data);
		}
	}

	public function loggedIn() {
		return $this->me->loggedIn();
	}

	// Login
	public function open($email, $pw) {
		$this->me = $this->auth->login($email, $pw);
		return $this->loggedIn();
	}

	// Logout
	public function close() {
		$this->me = $this->auth->logout();
	}

	public function getIp() {
		return $this->ip;
	}

	public function getSetting($name) {
		return $this->settings[$name];
	}

	public function setSetting($name, $value) {
		$this->settings[$name] = $value;
	}

	public function getMe() {
		return $this->me;
	}

	public function refreshMe() {
		$this->me = $this->auth->loginWithId($this->me->getId());
	}

}

// Stupid short form for lazy developers.
class Me {
	public static function get() {
		return Session::getObject()->getMe();
	}
}
?>