<?php

require_once("AbstractSniff.php");

class Flagbit_Sniffs_Magento_Template_NoModelInstantiationSniff extends Flagbit_Sniffs_Magento_Template_AbstractSniff
{


    public function register()
    {
        return array(T_STRING);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'] === 'getModel') {
            if ($tokens[$stackPtr - 1]['code'] === T_DOUBLE_COLON) {
                $phpcsFile->addError('Model instantiation in templates is prohibited', $stackPtr);
            }
        }
    }


}