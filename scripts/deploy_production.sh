#!/usr/bin/env bash
# Merge Develop into Production
git checkout develop
git pull origin develop
git checkout production
git pull origin production
git merge develop
git push origin production
git checkout develop

# Tag release
git checkout master
git pull origin master
git merge $dev_branch
echo "Enter version number and press [ENTER]"
read version_number
echo "Enter version message and press [ENTER]"
read version_message
git tag -a v$version_number -m "$version_message"
git push --tags

# Deploy to production
echo "Enter ssh key file and press [ENTER]"
read ssh_key_file
ssh ubuntu@tnt_fe -i $ssh_key_file <<'ENDSSH'
#commands to run on remote host
cd /var/www/html/courierplusng
git pull
exit
ENDSSH