<?php
/**
 * Viscacha_Sniffs_PHP_ForbiddenFunctionsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author	Greg Sherwood <gsherwood@squiz.net>
 * @author	Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ForbiddenFunctionsSniff.php 265109 2008-08-19 06:35:37Z squiz $
 * @link	  http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Viscacha_Sniffs_PHP_ForbiddenFunctionsSniff.
 *
 * Discourages the use of alias functions that are kept in PHP for compatibility
 * with older versions. Can be used to forbid the use of any function.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author	Greg Sherwood <gsherwood@squiz.net>
 * @author	Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0
 * @link	  http://pear.php.net/package/PHP_CodeSniffer
 */
class Viscacha_Sniffs_PHP_ForbiddenFunctionsSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * A list of alias/... functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * Functions liste here throw only a warning.
	 *
	 * @var array(string => string|null)
	 */
	protected $aliasFunctions = array(
		'sizeof' => 'count',
		'delete' => 'unset',
		'print' => 'echo',
		'is_null' => '$var !== null comparison',
		'create_function' => 'lambda functions / closures',
		'user_error' => 'trigger_error or Exceptions',
		'mime_content_type' => 'finfo_file',
		'diskfreespace' => 'disk_free_space',
		'show_source' => 'highlight_file',
		'ftp_quit' => 'ftp_close',
		'chop' => 'rtrim',
		'doubleval' => 'floatval',
		'is_integer' => 'is_int',
		'is_long' => 'is_int',
		'is_real' => 'is_float',
		'is_double' => 'is_float',
		'mysqli_client_encoding' => 'mysqli_character_set_name',
		'mysqli_set_opt' => 'mysqli_options',
		'mysqli_param_count' => 'mysqli_stmt_param_count',
		'mysqli_bind_param' => 'mysqli_stmt_bind_param',
		'mysqli_bind_result' => 'mysqli_stmt_bind_result',
		'mysqli_execute' => 'mysqli_stmt_execute',
		'mysqli_fetch' => 'mysqli_stmt_fetch',
		'mysqli_get_metadata' => 'mysqli_stmt_result_metadata',
		'mysqli_stmt_send_long_data' => 'mysqli_send_long_data',
		'imagecreate' => 'imagecreatetruecolor',
		'eval' => null,
		//'die' => 'exit',
		'mysql_db_query' => 'mysql_query',
		'mysql_ping' => null,
		'mysql_list_tables' => null,
		'mysql_listdbs' => 'mysql_list_dbs',
		'mysql_fieldtable' => 'mysql_field_table',
		'mysql_fieldtype' => 'mysql_field_type',
		'mysql_fieldname' => 'mysql_field_name',
		'mysql_fieldlen' => 'mysql_field_len',
		'mysql_fieldflags' => 'mysql_field_flags',
		'print_r' => 'Debug class',
		'var_dump' => 'Debug class',
		'error_log' => 'Debug class'
	);

	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * Functions listed here throw an error.
	 *
	 * @var array(string => string|null)
	 */
	protected $forbiddenFunctions = array(
		'ereg' => 'preg_match',
		'eregi' => 'preg_match',
		'ereg_replace' => 'preg_replace',
		'eregi_replace' => 'preg_replace',
		'split' => 'preg_split',
		'spliti' => 'preg_split',
		'sql_regcase' => null,
		'get_magic_quotes_gpc' => null,
		'overload' => null,
		'php_check_syntax' => null,
		'mysqli_escape_string' => 'mysqli_real_escape_string',
		'mysql_escape_string' => 'mysql_real_escape_string'
	);


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_STRING, T_PRINT);

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int				  $stackPtr  The position of the current token in the
	 *										stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
		if (in_array($tokens[$prevToken]['code'], array(T_DOUBLE_COLON, T_OBJECT_OPERATOR, T_FUNCTION)) === true) {
			// Not a call to a PHP function.
			return;
		}

		$function = strtolower($tokens[$stackPtr]['content']);

		$isErrorFunc = in_array($function, array_keys($this->forbiddenFunctions));
		$isAliasFunc = in_array($function, array_keys($this->aliasFunctions));

		if ($isErrorFunc === false || $isAliasFunc === false) {
			return;
		}

		$error = "The use of function {$function}() is ";
		if ($isErrorFunc === true) {
			$error .= 'forbidden';
			if ($this->forbiddenFunctions[$function] !== null) {
				$error .= '; use '.$this->forbiddenFunctions[$function].'() instead';
			}
		}
		else {
			$error .= 'discouraged';
			if ($this->aliasFunctions[$function] !== null) {
				$error .= '; use '.$this->aliasFunctions[$function].'() instead';
			}
		}

		if ($isErrorFunc === true) {
			$phpcsFile->addError($error, $stackPtr);
		}
		else {
			$phpcsFile->addWarning($error, $stackPtr);
		}

	}


}
?>