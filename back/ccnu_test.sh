#!/bin/sh
gcc -o /www/new/ccnu ccnu.c -ldl -lfcgi
killall ccnu
#nginx -s stop

spawn-fcgi -a 127.0.0.1 -p 9001 -f /www/new/ccnu
#nginx
