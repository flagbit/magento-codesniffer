<?php
/**
 * Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Oleh Kravets <oleh.kravets@flagbit.de>
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
class Flagbit_Sniffs_Functions_FunctionCallSignatureSniff extends PEAR_Sniffs_Functions_FunctionCallSignatureSniff
{


    /**
     * If true, the function parenthesis is being checked
     *
     * @var bool
     */
    public $checkParenthesis = false;


    /**
     * Processes multi-line calls.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param int                  $openBracket The position of the openning bracket
     *                                          in the stack passed in $tokens.
     * @param array                $tokens      The stack of tokens that make up
     *                                          the file.
     *
     * @return void
     */
    public function processMultiLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // We need to work out how far indented the function
        // call itself is, so we can work out how far to
        // indent the arguments.
        $functionIndent = 0;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        if ($i > 0 && $tokens[$i]['code'] === T_WHITESPACE) {
            $functionIndent = strlen($tokens[$i]['content']);
        }

        // Each line between the parenthesis should be indented n spaces.
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $lastLine     = $tokens[$openBracket]['line'];
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip nested function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i        = $tokens[$i]['parenthesis_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($tokens[$i]['line'] !== $lastLine) {
                $lastLine = $tokens[$i]['line'];

                // Ignore heredoc indentation.
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$heredocTokens) === true) {
                    continue;
                }

                // Ignore multi-line string indentation.
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) === true) {
                    if ($tokens[$i]['code'] === $tokens[($i - 1)]['code']) {
                        continue;
                    }
                }

                // We changed lines, so this should be a whitespace indent token, but first make
                // sure it isn't a blank line because we don't need to check indent unless there
                // is actually some code to indent.
                if ($tokens[$i]['code'] === T_WHITESPACE) {
                    $nextCode = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closeBracket + 1), true);
                    if ($tokens[$nextCode]['line'] !== $lastLine) {
                        $error = 'Empty lines are not allowed in multi-line function calls';
                        $phpcsFile->addError($error, $i, 'EmptyLine');
                        continue;
                    }
                } else {
                    $nextCode = $i;
                }

                // Check if the next line contains an object operator, if so rely on
                // the ObjectOperatorIndentSniff to test the indent.
                if ($tokens[$nextCode]['type'] === 'T_OBJECT_OPERATOR') {
                    continue;
                }

                if ($nextCode === $closeBracket) {
                    // Closing brace needs to be indented to the same level
                    // as the function call.
                    $expectedIndent = $functionIndent;
                } else {
                    $expectedIndent = ($functionIndent + $this->indent);
                }

                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    // Just check if it is a multi-line block comment. If so, we can
                    // calculate the indent from the whitespace before the content.
                    if ($tokens[$i]['code'] === T_COMMENT
                        && $tokens[($i - 1)]['code'] === T_COMMENT
                    ) {
                        $trimmed     = ltrim($tokens[$i]['content']);
                        $foundIndent = (strlen($tokens[$i]['content']) - strlen($trimmed));
                    } else {
                        $foundIndent = 0;
                    }
                } else {
                    $foundIndent = strlen($tokens[$i]['content']);
                }

                if ($expectedIndent !== $foundIndent) {
                    $error = 'Multi-line function call not indented correctly; expected %s spaces but found %s';
                    $data  = array(
                        $expectedIndent,
                        $foundIndent,
                    );
                    $phpcsFile->addError($error, $i, 'Indent', $data);
                }
            }//end if

            // Skip the rest of a closure.
            if ($tokens[$i]['code'] === T_CLOSURE) {
                $i        = $tokens[$i]['scope_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            // Skip the rest of a short array.
            if ($tokens[$i]['code'] === T_OPEN_SHORT_ARRAY) {
                $i        = $tokens[$i]['bracket_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($this->allowMultipleArguments === false && $tokens[$i]['code'] === T_COMMA) {
                // Comma has to be the last token on the line.
                $next = $phpcsFile->findNext(array(T_WHITESPACE, T_COMMENT), ($i + 1), $closeBracket, true);
                if ($next !== false
                    && $tokens[$i]['line'] === $tokens[$next]['line']
                ) {
                    $error = 'Only one argument is allowed per line in a multi-line function call';
                    $phpcsFile->addError($error, $next, 'MultipleArguments');
                }
            }
        }//end for

        /*
         * This does not fit with Magento internal usage of functions
         * @todo Flagbit Think about more sophisticated approach to this
         **/
        if ($this->checkParenthesis) {
            if ($tokens[($openBracket + 1)]['content'] !== $phpcsFile->eolChar) {
                $error = 'Opening parenthesis of a multi-line function call must be the last content on the line';
                $phpcsFile->addError($error, $stackPtr, 'ContentAfterOpenBracket');
            }

            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), null, true);
            if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
                $error = 'Closing parenthesis of a multi-line function call must be on a line by itself';
                $phpcsFile->addError($error, $closeBracket, 'CloseBracketLine');
            }
        }

    }//end processMultiLineCall()


}//end class
