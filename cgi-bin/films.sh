#!/bin/bash
echo -e "HTTP/1.1 200 OK\nContent-Type: text/html\n\n<meta charset="utf8"><pre>"
tree --du -h /d/FTPServer/films
echo -e "</pre>"
