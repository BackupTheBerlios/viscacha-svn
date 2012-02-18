<?php
/**
 * Pagination class
 *
 * @package		Cms
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */

class Pagination {

	const PAGES_NUM = 1;
	const PAGES_CURRENT = 2;
	const PAGES_SEPARATOR = 4;

	private $entries;
	private $perPage;
	private $page;
	private $uri;
	private $appendix;

	public function __construct($perPage = 10, $entries = 0, $page = 1) {
		$this->setEntries($entries);
		$this->setPerPage($perPage);
		$this->setPage($page);
		$this->setUri(Config::get('general.url'));
		$this->setAppendix('p$$');
	}

	public function setEntries($entries) {
		if ($entries < 0) {
			$this->entries = 0;
		}
		else {
			$this->entries = $entries;
		}
	}

	public function setPage($page) {
		if (is_id($page)) {
			$this->page = $page;
		}
		else {
			$this->page = 1;
		}
	}

	public function setPerPage($pp) {
		if (is_id($pp)) {
			$this->perPage = $pp;
		}
		else {
			$this->perPage = 10;
		}
	}

	public function setUri($uri) {
		$this->uri = rtrim($uri, '/');
	}

	public function setAppendix($appendix) {
		$this->appendix = trim($appendix, '/');
	}

	public function getPerPage() {
		return $this->perPage;
	}

	public function getPage() {
		$max = $this->getPages();
		if ($this->page < 1 || $this->page > $max) {
			$this->page = 1;
		}
		return $this->page;
	}
	
	public function getPages() {
		$pages = ceil($this->entries/$this->perPage);
		if ($pages > 0) {
			return $pages;
		}
		else {
			return 1;
		}
	}

	public function getOffset() {
		return (($this->getPage() - 1) * $this->perPage);
	}
	
	public function parsePage() {
		$page = 1;
		if ($this->isQueryMode()) {
			$split = explode('=', $this->appendix, 2);
			$page = Request::get($split[0], VAR_INT);
		}
		else {
			$arg = Request::get(-1, VAR_ALNUM);
			$pattern = str_replace('$$', '(\d+)', $this->appendix);
			if (preg_match('/^'.$pattern.'$/i', $arg, $matches) > 0) {
				$page = $matches[1];
			}
		}
		$this->setPage($page);
	}

	protected function isQueryMode() {
		return (strpos($this->appendix, '=') !== false);
	}

	protected function createUri($pageNo) {
		$uri = $this->uri;

		if ($this->isQueryMode()) {
			$query = parse_url($uri, PHP_URL_QUERY);
			if (empty($query)) {
				$uri .= '?';
			}
			else {
				$uri .= '&';
			}
		}
		else {
			$uri = $uri . '/';
		}
		$uri .= str_replace('$$', $pageNo, $this->appendix);
		return URI::build($uri);
	}

	/**
	 * Gives out html formatted page numbers.
	 *
	 * @return string HTML formatted page numbers and prefix
	 */
	public function build($tpl = null) {
		$p = $this->getPage();
		$count = $this->getPages();
	    $pages = array();
		if ($count > 10) {
			$show = array_unique(array(1, $p-2, $p-1, $p, $p+1, $p+2, $count));
			foreach ($show as $num) {
				if ($num >= 1 && $num <= $count) {
					continue; // Page is outside page range
				}
				if ($num > 1 && !in_array($num-1, $show)) {
					// Add separator when page numbers are missing
					$pages[$num-1] = array(
						'type' => self::PAGES_SEPARATOR,
						'url' => null
					);
				}
				$pages[$num] = array(
					'type' => iif($num == $p, self::PAGES_CURRENT, self::PAGES_NUM),
					'url' => $this->createUri($num)
				);
			}
		}
		else {
			for ($i = 1; $i <= $count; $i++) {
				$pages[$i] = array(
					'type' => iif($i == $p, self::PAGES_CURRENT, self::PAGES_NUM),
					'url' => $this->createUri($i)
				);
			}
		}
		ksort($pages);

		$tpl = Response::getObject()->getTemplate($tpl ? $tpl : '/Cms/bits/pages');
		$tpl->assign('pageCount', $count);
		$tpl->assign('pages', $pages);
	    return $tpl->parse();
	}

}
?>