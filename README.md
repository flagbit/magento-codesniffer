magento-codesniffer
===================

Code Sniffer rules for Magento-based projects at Flagbit.

Usage:
------

1. Install ECG rules using composer
2. phpcs --standard=/path/to/this/folder /path/to/code
3. phpcs --standard=/path/to/this/folder/template --extensions=phtml /path/to/code

Description
--------
Contains two rulesets for PHP_CodeSniffer: for .php and for .phtml files.
The PHP standard includes some generic sniffs, as well as Magento specific sniffs.
Magento specific sniffs are implemented by Magento team and can be found here https://github.com/magento-ecg/coding-standard

Template sniffs are implemented by Flagbit team and located in the folder _template_.



*gl&amp;hf*