<?php
class Flagbit_Sniffs_Commenting_FileCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
{

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        // Allow declare() statements at the top of the file.
        if ($tokens[$commentStart]['code'] === T_DECLARE) {
            $semicolon    = $phpcsFile->findNext(T_SEMICOLON, ($commentStart + 1));
            $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($semicolon + 1), null, true);
        }

        // Ignore vim header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], 'vim:') !== false) {
                $commentStart = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentStart + 1),
                    null,
                    true
                );
            }
        }

        $errorToken = ($stackPtr + 1);
        if (isset($tokens[$errorToken]) === false) {
            $errorToken--;
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return ($phpcsFile->numTokens + 1);
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $error = 'You must use "/**" style comments for a file comment';
            $phpcsFile->addError($error, $errorToken, 'WrongStyle');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');
            return ($phpcsFile->numTokens + 1);
        } else if ($commentStart === false
            || $tokens[$commentStart]['code'] !== T_DOC_COMMENT_OPEN_TAG
        ) {
            $phpcsFile->addError('Missing file doc comment', $errorToken, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'no');
            return ($phpcsFile->numTokens + 1);
        } else {
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');
        }

        // Check the PHP Version, which should be in some text before the first tag.
        // Flagbit: do not process php version
        /* $commentEnd = $tokens[$commentStart]['comment_closer'];
        $found      = false;
        for ($i = ($commentStart + 1); $i < $commentEnd; $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {
                break;
            } else if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING
                && strstr(strtolower($tokens[$i]['content']), 'php version') !== false
            ) {
                $found = true;
                break;
            }
        }

        if ($found === false) {
            $error = 'PHP version not specified';
            $phpcsFile->addWarning($error, $commentEnd, 'MissingVersion');
        } */

        // Check each tag.
        $this->processTags($phpcsFile, $stackPtr, $commentStart);

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


    /**
     * Process the copyright tags.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array                $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processCopyright(PHP_CodeSniffer_File $phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            $matches = array();
            if (preg_match('/([0-9]{4}),?((.{1})([0-9]{4}))? (.+)$/', $content, $matches) !== 0) {
                // Check earliest-latest year order.
                if ($matches[3] !== '') {
                    if ($matches[3] !== '-') {
                        $error = 'A hyphen must be used between the earliest and latest year';
                        $phpcsFile->addError($error, $tag, 'CopyrightHyphen');
                    }

                    if ($matches[4] !== '' && $matches[4] < $matches[1]) {
                        $error = "Invalid year span \"$matches[1]$matches[3]$matches[4]\" found; consider \"$matches[4]-$matches[1]\" instead";
                        $phpcsFile->addWarning($error, $tag, 'InvalidCopyright');
                    }
                }
            } else {
                $error = '@copyright tag must contain a year and the name of the copyright holder';
                $phpcsFile->addError($error, $tag, 'IncompleteCopyright');
            }
        }//end foreach

    }//end processCopyright()

}