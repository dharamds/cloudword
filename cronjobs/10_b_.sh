#!/bin/bash
#!/bin/env bash
lftp -u dharam,jP3LW0%F ftp://162.253.126.178:21<<EOF
set xfer:clobber on
set ssl:verify-certificate no
set sftp:auto-confirm yes
mirror -c --use-pget-n=8 -P 8 --exclude admin2/ --exclude Install/ / /var/www/vhosts/cloudserviceworld.com/projects/RGF0YWxvZ3kgUHJvamVjdA==_1626418046/ftp_server/TkVUX05VS0U=_1626424406_1/syncbackup
quit
EOF