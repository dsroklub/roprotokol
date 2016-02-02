#!/bin/bash -x
target=$1
source=$(dirname $0)/../rowingapp
mkdir -p $target/app
rsync -av --exclude '.git' $source/frontend/app/ $target/app/
