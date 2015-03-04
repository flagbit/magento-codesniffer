#!/bin/bash

# read options
while getopts ":n:" opt; do
  case $opt in
    n) # name
      NAME=$OPTARG >&2
      ;;
    :) # empty name
      echo "Option -$OPTARG requires an argument." >&2
      exit 1;
      ;;
  esac
done

if [ ${#NAME} -lt 1 ]; then
    NAME="phpcs-run";
fi

LINK="/usr/bin/$NAME";
DIR=$( dirname $( readlink -f $0 ) );

ln -s $DIR"/run.sh" $LINK