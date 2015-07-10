<?php

require_once("AbstractSniff.php");

/**
 * Class Flagbit_Sniffs_Template_AlternativeSyntaxSniff
 */
class Flagbit_Sniffs_Template_AlternativeSyntaxSniff extends Flagbit_Sniffs_Template_AbstractSniff
{


    public function register()
    {
        return array(T_IF, T_FOREACH, T_WHILE, T_FOR);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'])) {
            if ($tokens[$stackPtr]['scope_opener']['code'] === T_OPEN_CURLY_BRACKET) {
                $phpcsFile->addError(
                    'Use alternative syntax for the ' . $tokens[$stackPtr]['content'] . ' operator',
                    $stackPtr
                );
            }
        } else {
            $phpcsFile->addError('A colon as scope opener expected', $stackPtr);
        }
    }


}