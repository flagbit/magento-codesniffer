magento-codesniffer
===================

Code Sniffer rules for Magento-based projects at Flagbit.

Usage:
------

1. Install ECG rules using composer
2. Run `sudo ./install.sh -n <desired-command-name>`(default: `phpcs-run`) to link run.sh to your /usr/bin
3. Run `phpcs-run -t` if you want to check templates
4. Run `phpcs-run -e php,phtml` if you want to check some specific file extensions
5. Note, that `phpcs-run` checks only files, that were modified or added according to git.

Description
--------
Contains two rulesets for PHP_CodeSniffer: for .php and for .phtml files.
The PHP standard includes some generic sniffs, as well as Magento specific sniffs.
Magento specific sniffs are implemented by Magento team and can be found here https://github.com/magento-ecg/coding-standard

Template sniffs are implemented by Flagbit team and located in the folder _template_.



*gl&amp;hf*