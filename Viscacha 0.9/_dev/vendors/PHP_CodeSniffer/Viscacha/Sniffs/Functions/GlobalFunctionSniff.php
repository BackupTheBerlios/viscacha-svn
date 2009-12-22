<?php
/**
 * Viscacha_Sniffs_Functions_GlobalFunctionSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author	Greg Sherwood <gsherwood@squiz.net>
 * @author	Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: GlobalFunctionSniff.php 261303 2008-06-18 04:49:50Z squiz $
 * @link	  http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Viscacha_Sniffs_Functions_GlobalFunctionSniff.
 *
 * Tests for functions outside of classes.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author	Greg Sherwood <gsherwood@squiz.net>
 * @author	Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN Rev. 291629
 * @link	  http://pear.php.net/package/PHP_CodeSniffer
 */
class Viscacha_Sniffs_Functions_GlobalFunctionSniff implements PHP_CodeSniffer_Sniff
{


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(T_FUNCTION);

	}//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (empty($tokens[$stackPtr]['conditions']) === true) {
            $functionName = $phpcsFile->getDeclarationName($stackPtr);
			if ($functionName === null) {
				return;
			}

            // Special exception for __autoload as it needs to be global.
            // Viscacha specific Core function should be global too for quick access
            if ($functionName !== '__autoload' && $functionName !== 'Core') {
                $error = "Consider putting global function \"$functionName\" in a static class";
                $phpcsFile->addWarning($error, $stackPtr);
            }
        }

    }//end process()


}//end class

?>
