<?php
abstract class Flagbit_Sniffs_Magento_Template_AbstractSniff implements PHP_CodeSniffer_Sniff
{

    protected $_templateExtensions = array('phtml');


    /**
     * File processing should be done here
     *
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     */
    abstract protected function _process(PHP_CodeSniffer_File $phpcsFile, $stackPtr);


    /**
     * Here we check the file extension and then run the main script
     *
     * @param \PHP_CodeSniffer_File $phpcsFile
     * @param int $stackPtr
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $extension = pathinfo($phpcsFile->getFilename(), PATHINFO_EXTENSION);

        if (in_array($extension, $this->_templateExtensions)) {
            $this->_process($phpcsFile, $stackPtr);
        }
    }


}