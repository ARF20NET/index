#!/bin/bash
echo -e "HTTP/1.1 200 OK\nContent-Type: text/html\n\n<meta charset="utf8"><pre>"
sudo ipmitool sdr elist full
echo -e "</pre>"
