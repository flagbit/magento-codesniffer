<?php
class Flagbit_Sniffs_Magento_Template_HelperInstantiationSniff extends Flagbit_Sniffs_Magento_Template_AbstractSniff
{
    public function register()
    {
        return array(T_STRING);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'] === 'helper') {
            if ($tokens[$stackPtr - 1]['code'] === T_DOUBLE_COLON) {
                $phpcsFile->addWarning('Calling helpers in templates is not recommended', $stackPtr);
            }
        }
    }


}