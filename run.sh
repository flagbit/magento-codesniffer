#!/bin/bash

# read options
while getopts "t?s:e:" opt; do
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

# get list of changed and untracked files
for FILE in $( git status --porcelain ); do

    if [ ${#FILE} -gt 2 ]; then

        COMMAND="phpcs";

        # if the file has none of the specified extensions we skip it
        if [ ${#EXTENSIONS} -gt 0 ]; then

            # get extension of the current file
            FILENAME=$(basename "$FILE");
            EXT="${FILENAME##*.}";

            if [[ $EXTENSIONS !=  *$EXT* ]]; then
                continue;
            fi
        fi

        # apply standard path if it's been specified
        if [ ${#STANDARD} -gt 0 ]; then
            COMMAND=$COMMAND" --standard=$STANDARD";
        fi

        # execute the command
        $COMMAND $FILE;

    fi

done;