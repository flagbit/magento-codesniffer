<?php
class Flagbit_Sniffs_Magento_Template_NoModelInstantiationSniff implements PHP_CodeSniffer_Sniff
{


    public function register()
    {
        return array(T_STRING);
    }


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'] === 'getModel') {
            if ($tokens[$stackPtr - 1]['code'] === T_DOUBLE_COLON) {
                $phpcsFile->addError('Model instantiation in templates is prohibited', $stackPtr);
            }
        }
    }


}