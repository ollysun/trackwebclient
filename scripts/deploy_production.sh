#!/usr/bin/env bash
function merge() {
    # Merges from dev branch to environment branch
    echo "Merging $1 to $2"
    git checkout $1
    git pull origin $1
    git checkout $2
    git pull origin $2
    git merge $1
    git push origin $2
    git checkout $1
}

#function deploy() {
#    ## Deploys app to server
#    # 1 - Environment
#    # 2 - Username
#    # 3 - Host
#    # 4 - App Folder
#    echo "Deploying to $1 $2"
#    echo "Enter ssh key file and press [ENTER]"
#    read ssh_key_file
#    ssh $2@$3 -i $ssh_key_file <<'ENDSSH'
#        #commands to run on remote host
#        cd $4
#        git pull
#        exit
#    ENDSSH
#}

function tagRelease() {
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
    git checkout $dev_branch
}

# Declare Variables
staging_branch=staging
dev_branch=develop
production_branch=production
echo "Deployment Starting..."

# Staging
echo "Do you want to deploy to staging? Y/N"
read should_deploy_staging
if [ $should_deploy_staging = "Y" -o $should_deploy_staging = "y" ] ; then
    merge $dev_branch $staging_branch
fi

# Production
echo "Do you want to deploy to production? Y/N"
read should_deploy_production
if [ $should_deploy_production == 'Y' -o $should_deploy_production == 'y' ] ; then
    merge $dev_branch $production_branch
#    deploy
    tagRelease
fi