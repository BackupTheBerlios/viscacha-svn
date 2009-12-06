<?php
/**
 * Viscacha_Sniffs_Formatting_SpaceAfterCastSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: SpaceAfterCastSniff.php 240175 2007-07-23 01:47:54Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Viscacha_Sniffs_Formatting_SpaceAfterCastSniff.
 *
 * Ensures there is a single space after cast tokens.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Viscacha_Sniffs_Formatting_SpaceAfterCastSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$castTokens;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $error = 'A cast statement must be followed by a single space';
            $phpcsFile->addError($error, $stackPtr);
            return;
        }

        if ($tokens[($stackPtr + 1)]['content'] !== ' ') {
            $error = 'A cast statement must be followed by a single space';
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
