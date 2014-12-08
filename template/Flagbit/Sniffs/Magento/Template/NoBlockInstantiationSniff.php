<?php
class Flagbit_Sniffs_Magento_Template_NoBlockInstantiationSniff implements PHP_CodeSniffer_Sniff
{


    public function register()
    {
        return array(T_STRING);
    }


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'] === 'getBlockSingleton') {
            if ($tokens[$stackPtr - 1]['code'] === T_DOUBLE_COLON) {
                $phpcsFile->addError('Block instantiation in templates is prohibited', $stackPtr);
            }
        }
    }


}