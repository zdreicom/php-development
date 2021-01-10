#!/bin/bash

docker run --rm -ti -v ${PWD}:/opt/project \
   -w /opt/project \
   silvermoonframework/php-7.4-xdebug:latest \
   /bin/bash
