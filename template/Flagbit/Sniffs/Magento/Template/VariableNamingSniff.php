<?php

require_once("AbstractSniff.php");

class Flagbit_Sniffs_Magento_Template_VariableNamingSniff extends Flagbit_Sniffs_Magento_Template_AbstractSniff
{


    public function register()
    {
        return array(T_STRING_VARNAME, T_VARIABLE, T_EQUAL);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // find next non-whitespace token
        $nextTokenPtr = $phpcsFile->findNext(array(T_WHITESPACE), $stackPtr+1, null, true);

        // if it's an assigment and the variable isn't underscored add a warning
        if ($tokens[$nextTokenPtr]['code'] === T_EQUAL) {
            if ($tokens[$stackPtr]['content']{1} != '_') {
                $phpcsFile->addError(
                    'The variable must be underscored: ' . $tokens[$stackPtr]['content'],
                    $stackPtr
                );
            }
        }
    }


}