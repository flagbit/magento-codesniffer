<?php

/**
 * Class Flagbit_Sniffs_Magento_Template_AlternativeSyntaxSniff
 */
class Flagbit_Sniffs_Magento_Template_AlternativeSyntaxSniff implements PHP_CodeSniffer_Sniff
{


    public function register()
    {
        return array(T_IF, T_FOREACH, T_WHILE, T_FOR);
    }


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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