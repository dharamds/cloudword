#!/bin/bash
#!/bin/env bash
lftp -u ec2-user,dummy sftp://65.0.186.196:22<<EOF
set xfer:clobber on
set sftp:auto-confirm yes
set xfer:clobber on
set sftp:connect-program "ssh -a -x -T -c arcfour -o Compression=no"
set ssl:verify-certificate no
set sftp:auto-confirm yes
set sftp:connect-program "ssh -v -a -x -i /var/www/vhosts/cloudserviceworld.com/key_files/aws_nodejs37.pem"
mirror -R -c --Remove-source-files --Remove-source-dirs /var/www/vhosts/cloudserviceworld.com/projects/Q2xvdWQgV29yZCBUZXN0aW5n_1626420829/ftp_server/aWxsdXNpb25tZWRpYQ==_1626421110_2/temp/restore_folder_YVd4c2RYTnBiMjV0WldScFlRPT1fMTYyNjQyMTExMF8y_1626696028/syncbackup/ /var/www/html
EOF