<?php

require_once("AbstractSniff.php");

/**
 * Class Flagbit_Sniffs_Template_NoFunctionNoClassDeclarationSniff
 */
class Flagbit_Sniffs_Template_NoFunctionNoClassDeclarationSniff
    extends Flagbit_Sniffs_Template_AbstractSniff
{


    public function register()
    {
        return array(T_FUNCTION, T_CLASS);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_FUNCTION) {
            $phpcsFile->addError(
                'Function declarations in templates are disallowed',
                $stackPtr
            );
        } else {
            $phpcsFile->addError(
                'Class declarations in templates are disallowed',
                $stackPtr
            );
        }
    }


}
