<?php
class Flagbit_Sniffs_Magento_Template_NoConcatenationSniff extends Flagbit_Sniffs_Magento_Template_AbstractSniff
{


    public function register()
    {
        return array(T_STRING_CONCAT);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile->addWarning('Usage of PHP concatenation is discouraged', $stackPtr);
    }


}