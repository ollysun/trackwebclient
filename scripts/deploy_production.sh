#!/usr/bin/env bash
# Merge Develop into Production
git checkout develop
git pull origin develop
git checkout production
git pull origin production
git merge develop
git push origin production
git checkout develop

echo "Enter ssh key file and press [ENTER]"
read ssh_key_file
ssh ubuntu@tnt_fe -i $ssh_key_file <<'ENDSSH'
#commands to run on remote host
cd /var/www/html/courierplusng
git pull
exit
ENDSSH