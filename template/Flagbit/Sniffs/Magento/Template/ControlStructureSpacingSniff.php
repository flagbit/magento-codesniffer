<?php

require_once("AbstractSniff.php");

class Flagbit_Sniffs_Magento_Template_ControlStructureSpacingSniff extends Flagbit_Sniffs_Magento_Template_AbstractSniff
{
    protected $_openingTokens = array(
        T_IF,
        T_ELSEIF,
        T_FOREACH,
        T_WHILE,
        T_FOR,
    );

    protected $_closingTokens = array(
        T_ENDIF,
        T_ENDFOREACH,
        T_ENDWHILE,
        T_ENDFOR,
    );

    public function register()
    {
        return array_merge(
            $this->_openingTokens,
            $this->_closingTokens,
            array(T_ELSE)
        );
    }

    protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (in_array($tokens[$stackPtr]['code'], $this->_openingTokens)) {
            $this->_checkStructureOpening($phpcsFile, $stackPtr);
        } elseif (in_array($tokens[$stackPtr]['code'], $this->_closingTokens)) {
            $this->_checkStructureClosing($phpcsFile, $stackPtr);
        } else {
            $this->_checkElse($phpcsFile, $stackPtr);
        }
    }


    /**
     * Check control structure opening
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param $stackPtr
     */
    protected function _checkStructureOpening(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_checkOpeningStart($phpcsFile, $stackPtr);
        $this->_checkOpeningEnd($phpcsFile, $stackPtr);
    }


    /**
     * Check control structure closing
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param $stackPtr
     */
    protected function _checkStructureClosing(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_checkOpenTag($phpcsFile, $stackPtr);

        $tokens = $phpcsFile->getTokens();
        $nextTokenPtr = $phpcsFile->findNext(array(T_SEMICOLON, T_CLOSE_TAG), $stackPtr);

        if ($tokens[$nextTokenPtr]['code'] !== T_SEMICOLON) {
            $phpcsFile->addWarning('Semicolon expected after"' . $tokens[$stackPtr]['content'] . '"', $stackPtr);
        } else {
            if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
                $phpcsFile->addWarning('No spaces are allowed between "' . $tokens[$stackPtr]['code']
                    . '" and semicolon',
                    $stackPtr
                );
            }

            $this->_checkCloseTag($phpcsFile, $nextTokenPtr);
        }
    }


    /**
     * Check beginning of the control structure opening, like <?php if (//condition
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param $stackPtr
     */
    protected function _checkOpeningStart(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // one space between before the opening parenthesis
        $openerPtr = $phpcsFile->findNext(array(T_WHITESPACE, T_OPEN_PARENTHESIS), $stackPtr + 1);
        if ($tokens[$openerPtr]['code'] == T_WHITESPACE) {
            $spaceNum = strlen($tokens[$openerPtr]['content']);
        } elseif ($tokens[$openerPtr]['code'] == T_OPEN_PARENTHESIS) {
            $spaceNum = 0;
        } else {
            $phpcsFile->addError('Open parenthesis expected', $stackPtr);
        }

        if (isset($spaceNum) && $spaceNum != 1) {
            $phpcsFile->addWarning(
                'Exactly 1 space required between the "' . $tokens[$stackPtr]['content']
                . '" and the open parenthesis, ' . $spaceNum . ' found',
                $stackPtr
            );
        }

        // check whitespaces before the structure
        $this->_checkOpenTag($phpcsFile, $stackPtr);
    }


    /**
     * Check the end of the control structure opening, like //condition): ?>
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param $stackPtr
     */
    protected function _checkOpeningEnd(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener'])) {
            $colonPtr = $tokens[$stackPtr]['scope_opener'];
        } else {
            $colonPtr = $phpcsFile->findNext(array(T_COLON), $stackPtr + 1);
        }

        $this->_checkCloseTag($phpcsFile, $colonPtr);

        // check spaces before colon
        if ($tokens[$colonPtr - 1]['code'] == T_WHITESPACE) {
            $phpcsFile->addWarning('No spaces allowed between the closing parenthesis and the colon', $stackPtr);
        } elseif ($tokens[$colonPtr - 1]['code'] !== T_CLOSE_PARENTHESIS) {
            $phpcsFile->addError('Closing parenthesis expected', $stackPtr);
        }
    }


    /**
     * Check else spacing
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param $stackPtr
     */
    protected function _checkElse(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->_checkOpenTag($phpcsFile, $stackPtr);

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
            $phpcsFile->addWarning('No spaces allowed between "else" and colon', $stackPtr);
        }

        $colonPtr = $phpcsFile->findNext(array(T_COLON), $stackPtr + 1);

        //  check spaces after colon
        $this->_checkCloseTag($phpcsFile, $colonPtr);
    }


    /**
     * * Check if the open tag is on the same line and if it has a single space after
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param $stackPtr
     */
    protected function _checkOpenTag(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // check whitespaces before the structure
        if ($tokens[$stackPtr - 1]['code'] !== T_OPEN_TAG) {
            if ($tokens[$stackPtr - 1]['code'] === T_WHITESPACE) {
                $spaceNum = strlen($tokens[$stackPtr - 1]['content']);
                $phpcsFile->addWarning(
                    'Where must be just 1 space between PHP open tag and "' . $tokens[$stackPtr]['content']
                    . ', ' . ($spaceNum + 1) . ' found',
                    $stackPtr
                );
            }

            $openTagPtr = $phpcsFile->findPrevious(array(T_OPEN_TAG), $stackPtr);
            if ($tokens[$stackPtr]['line'] !== $tokens[$openTagPtr]['line']) {
                $phpcsFile->addWarning(
                    'The line must start with PHP open tag', $stackPtr
                );
            }

            if ($tokens[$stackPtr - 2]['code'] !== T_OPEN_TAG) {
                $phpcsFile->addWarning(
                    'No operators are allowed between PHP open tag and "' . $tokens[$stackPtr]['content'] . '"',
                    $stackPtr
                );
            }
        }
    }


    /**
     * Check if the close tag is on the same line and if it has a single space before
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int                  $lastPtr   Last non-whitespace token before closing tag pointer
     */
    protected function _checkCloseTag(PHP_CodeSniffer_File $phpcsFile, $lastPtr)
    {
        $tokens = $phpcsFile->getTokens();

        //  check spaces after colon
        if ($tokens[$lastPtr + 1]['code'] == T_WHITESPACE) {
            $spaceNum = strlen($tokens[$lastPtr + 1]['content']);
        } elseif ($tokens[$lastPtr + 1]['code'] == T_CLOSE_TAG) {
            $spaceNum = 0;
        }

        if (isset($spaceNum) && $spaceNum != 1) {
            $phpcsFile->addWarning(
                'Exactly 1 space required between "'. $tokens[$lastPtr]['content']
                .'" and the close tag, ' . $spaceNum . ' found',
                $lastPtr
            );
        }

        $closeTag = $phpcsFile->findNext(array(T_CLOSE_TAG), $lastPtr);
        if ($tokens[$lastPtr]['line'] !== $tokens[$closeTag]['line']) {
            $phpcsFile->addWarning(
                'The line must end with PHP close tag', $lastPtr
            );
        }
    }
}