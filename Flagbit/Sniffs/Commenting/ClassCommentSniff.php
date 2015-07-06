<?php
class Flagbit_Sniffs_Commenting_ClassCommentSniff extends PEAR_Sniffs_Commenting_ClassCommentSniff
{

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