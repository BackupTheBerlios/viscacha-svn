<?php
/**
 * Interface for all CacheItem classes.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
interface CacheObject {

	public function export();

	public function import();

	public function expired($max_age);

	public function age();

	public function exists($max_age = null);

	public function delete();

	public function load();

	public function rebuildable();

	public function get($max_age = null);

	public function set($data);

}
?>