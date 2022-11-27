#!/bin/bash
echo -e "HTTP/1.1 200 OK\nContent-Type: text/html\n\n<pre>"
for i in {0..7}
do
	echo "=============================================== DRIVE "$i" ==============================================="
	sudo smartctl -a /dev/bus/4 -d megaraid,$i
done
echo "</pre>"
