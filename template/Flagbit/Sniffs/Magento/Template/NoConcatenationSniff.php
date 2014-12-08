<?php
class Flagbit_Sniffs_Magento_Template_NoConcatenationSniff implements PHP_CodeSniffer_Sniff
{


    public function register()
    {
        return array(T_STRING_CONCAT);
    }


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $phpcsFile->addWarning('Usage of PHP concatenation is discouraged', $stackPtr);
}


}