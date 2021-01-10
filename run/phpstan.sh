#!/bin/bash

docker run --rm -ti -v ${PWD}:/opt/project \
   -w /opt/project \
   silvermoonframework/php-7.4:latest \
   bin/php-dev run:stan
