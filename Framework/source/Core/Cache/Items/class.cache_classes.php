<?php
/**
 * Caches the classes for the ClassesManager.
 *
 * @package		Core
 * @subpackage	Cache
 * @author		Matthias Mohr
 * @since 		1.0
 */
class cache_classes extends CacheItem implements CacheObject {

	private $next;
	private $classes;

	public function __construct($filename){
		parent::__construct($filename);
		$this->next = false;
		$this->classes = array();
		if (function_exists('token_get_all') == false) {
			Core::throwError('Function token_get_all() is not supported. You can not use the ClassManager.', E_NOTICE);
		}
	}

	public function load() {
		$files = $this->scanSourceFolder('source', 'class.*.php');
		foreach($files as $file){
			$this->parse($file);
		}
	}

	/**
	 * Scans recursively all Source folders for classes.
	 *
	 * @param string 	Directory to start with.
	 * @param string 	Pattern to glob for.
	 * @param int 		Flags sent to glob.
	 * @return array containing all pattern-matched files.
	 * @todo escapeshellcmd gegen eigene Variante ersetzen (weil die Methode auf manchen Hosts gesperrt ist)
	 */
	private function scanSourceFolder($sDir, $sPattern, $nFlags = null) {
		if (function_exists('escapeshellcmd')) {
			$sDir = @escapeshellcmd($sDir);
		}

		$aFiles = glob("{$sDir}/{$sPattern}", $nFlags);

		foreach (glob("{$sDir}/*", GLOB_ONLYDIR) as $sSubDir) {
			$aSubFiles = $this->scanSourceFolder($sSubDir, $sPattern, $nFlags);
			$aFiles = array_merge($aFiles, $aSubFiles);
		}

	  	return $aFiles;
	}

    /**
     * Looks for class names in a PHP code
     *
     * @param string File to scan for class name.
     * @todo Reine PHP Alternative um Klassen zu erkennen (RegExp)
     */
    private function parse($filepath) {
    	$file = new File($filepath);
    	if ($file->exists() == true) {
	        $code = $file->read();
	        $tokens = @token_get_all($code);
	        foreach ($tokens as $token) {
	            if (!isset($token[0])) {
					continue;
				}
	            // next token after this one is our desired class name
	            if ($token[0] == T_CLASS) {
	                $this->next = true;
	            }
	            if ($token[0] == T_STRING && $this->next === true) {
	            	if (isset($this->data[$token[1]]) == true) {
	            		Core::throwError('Class with name "'.$token[1].'" was found more than once. Only file "'.$file->absPath().'" has been indexed!', INTERNAL_NOTICE);
	            	}
	                $this->data[$token[1]] = $file->relPath();
	                $this->next = false;
	            }
	        }
    	}
    }

}
?>
