#!/bin/bash -x
rm -f TAGS
#(find . -name "*.js" && find -name "*.php")| grep -v /SubdivisionCode/|grep -v /ckeditor/| grep -v /unit/|grep -v /Exceptions/ |grep -iv /tests/|xargs etags --members --append

ctags-exuberant  --recurse -e --languages=php,javascript,sql --exclude=vendor --exclude="ckeditor" --exclude=unit --exclude=Exceptions --exclude=tests --exclude=SubdivisionCode
