#!/usr/bin/env bash
function merge() {
    # Merges from dev branch to environment branch
    echo "Merging $1 to $2"
    git checkout $1
    git pull origin $1
    if [ $1 != $2 ]; then
       git checkout $2
       git pull origin $2
       git merge $1
       git push origin $2
       git checkout $1
    fi
}

function deploy() {
    ## Deploys app to server
    # 1 - Environment
    # 2 - Username
    # 3 - Host
    # 4 - App Folder
    echo "Deploying to $1 $2"
    echo "Enter ssh key file and press [ENTER]"
    read ssh_key_file
    ssh $2@$3 -i $ssh_key_file "cd $4 && git pull"
}

function tagRelease() {
    echo "Tagging realese"
    git checkout master
    git pull origin master
    git merge $1
    echo "Enter version number and press [ENTER]"
    read version_number
    echo "Enter version message and press [ENTER]"
    read version_message
    echo "Creating tag $version_number($version_message)"
    git tag -a v$version_number -m "$version_message"
    git push --tags
    git checkout $1
}

echo "Deployment Starting..."

echo "Enter the development branch and press [ENTER]"
read dev_branch

envs=(staging production)

if [ -n "$dev_branch" ] ; then
    for env in "${envs[@]}"
    do
        echo "Do you want to deploy to $env? Y/N"
        read should_deploy
        if [ $should_deploy = "Y" -o $should_deploy = "y" ] ; then
            echo "Enter the $env branch and press [ENTER]"
            read branch
            if [ -n "$branch" ] ; then
                merge $dev_branch $branch
                deploy $env $username $host $app_folder
                if [ "$env" = "production" ]; then
                    tagRelease $dev_branch
                fi
            else
                echo "Invalid branch entered"
            fi
        fi
    done
else
    echo "You can't proceed without a dev branch"
fi

