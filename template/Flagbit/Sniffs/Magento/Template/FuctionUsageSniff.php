<?php

/**
 * Class Flagbit_Sniffs_Magento_Template_FuctionUsageSniff
 */
class Flagbit_Sniffs_Magento_Template_FuctionUsageSniff extends Flagbit_Sniffs_Magento_AbstractSniff
{

    protected $_allowedFunctions = array(
        'count',
    );

    public function register()
    {
        return array(T_STRING);
    }


    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();


        $prevPtr = $phpcsFile->findPrevious(array(T_WHITESPACE), $stackPtr - 1, null, true);
        $nextPtr = $phpcsFile->findPrevious(array(T_WHITESPACE), $stackPtr + 1, null, true);

        $isClass = $tokens[$nextPtr]['code'] === T_DOUBLE_COLON;
        $isConstant = strtoupper($tokens[$stackPtr]['content']) === $tokens[$stackPtr]['content'];
        $isMethod = in_array($tokens[$prevPtr]['code'], array(T_DOUBLE_COLON, T_OBJECT_OPERATOR));
        $isAllowed = in_array($tokens[$stackPtr]['content'], $this->_allowedFunctions);

        if (!$isClass
            && !$isConstant
            && !$isMethod
            && !$isAllowed
        ) {
            $phpcsFile->addError(
                'Usage of the function "' . $tokens[$stackPtr]['content'] . '()" is not allowed in templates',
                $stackPtr
            );
        }

    }


}