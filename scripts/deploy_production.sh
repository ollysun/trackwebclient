#!/usr/bin/env bash
# Merge Develop into Production
git checkout develop
git pull origin develop
git checkout production
git pull production
git merge develop

echo "Enter ssh key file and press [ENTER]"
read ssh_key_file
command_after_ssh = "cd /var/www/html/courierplusng && git pull";
ssh ubuntu@tnt_fe -i $ssh_key_file $command_after_ssh

