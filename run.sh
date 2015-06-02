#!/bin/bash

# todo:
# - summary parameter
# - non-git mode parameter (or git-mode marameter)
# - exclude non php files by default
# - help
#

# default file extensions
EXTENSIONS="php,phtml";

# read options
while getopts "t?r?g?s:e:" opt; do
  case $opt in
    s) # standard
      STANDARD=$OPTARG >&2
      ;;
    e) # extensions
      EXTENSIONS=$OPTARG >&2
      ;;
    t) # template mode
      TEMPLATE_MODE=1;
      EXTENSIONS="phtml";
      ;;
    r) # report only
      REPORT=1;
      ;;
    g) # git mode
      GIT_MODE=1;
      ;;
    :)
      echo "Option -$OPTARG requires an argument." >&2
      exit 1;
      ;;
  esac
done

# if no standard specified use the ones from script directory
if [ ${#STANDARD} -lt 1 ]; then

    # take the path to default rulesets
    DIR=$( dirname $( readlink -f $0 ) );

    # if template mode
    if [[ $TEMPLATE_MODE == 1 ]]; then
        STANDARD=$DIR"/template/ruleset.xml";
    else
        STANDARD=$DIR"/ruleset.xml";
    fi

fi

COMMAND="phpcs";

# apply standard path
COMMAND=$COMMAND" --standard=$STANDARD";

# show report only
if [[ $REPORT == 1 ]]; then
    COMMAND=$COMMAND" --report=summary";
fi

#TARGET="./*";

if [[ $GIT_MODE == 1 ]]; then

    # get list of changed and untracked files
    FILES=$( git status --porcelain );

    for FILE in ${FILES}; do

        if [ ${#FILE} -gt 2 ]; then

            # if the file has none of the specified extensions we skip it
            if [ ${#EXTENSIONS} -gt 0 ]; then

                # get extension of the current file
                FILENAME=$(basename "$FILE");
                EXT="${FILENAME##*.}";

                if [[ $EXTENSIONS !=  *$EXT* ]]; then
                    continue;
                fi
#
            fi

            TARGET=$TARGET" "$FILE;

        fi

    done;
else
    TARGET=$BASH_ARGV;
fi

#in case target was not specified
if [ ${#TARGET} -lt 3 ]; then
    TARGET="./*"
fi

# execute the command
$COMMAND $TARGET;
