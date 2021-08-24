#!/bin/bash

GIT_BRANCH='master'
LOCAL_PROJECT_LOCATION='/Users/andypearson/Repository/Code/LondonParkour.com/dev.pulse.londonparkour.com'
VAGRANT_MACHINE='pulse.londonparkour.com'
DUMP_COMMAND='dev.pulse.londonparkour.com_dumpdb'
ANSWERDB=FALSE

question_vagrant_db(){  
    printf "${Cyan}Do you want to get the Vagrant DB. Y/n?\n"
    read ANSWERDB
    export ANSWERDB
}

get_vagrant_id(){

    printf "${Cyan}Getting vagrant box ID.\n"

    ID=`vagrant global-status | grep "$VAGRANT_MACHINE" | cut -d ' ' -f 1 `
    if [ ! ${#ID} -eq 7 ]; 
        then
            printf "${Red}Machine Not Found\n"
            exit 1
        else 
            printf "${Green}Machine Found for ${VAGRANT_MACHINE}. ID: ${ID} \n" ;
    fi
}


dump_database(){
    printf "${Cyan}Dumping DB.${Green}\n"
    vagrant ssh $ID -c "sudo ${DUMP_COMMAND}"
}


move_vagrant_db_to_deploy_repo(){
    printf "${Cyan}Moving Vagrant Database to this deploy repo.\n"
    sudo mv $LOCAL_PROJECT_LOCATION/wp-content/database/*.sql ./wp-content/database/
    printf "${Green}Moved to ./wp-content/database/\n"
}

question_submodules(){  
    printf "${Cyan}Do you want to update all Submodules? Y/n?\n"
    read ANSWERSUBMODULES
    export ANSWERSUBMODULES
}

update_all_submodules(){
    printf "${Cyan}Updating all git submodules recursively.\n"
    git submodule update --recursive --remote
    printf "${Green}All submodules pulled.\n"
}


commit_changes_to_repo(){
    printf "${Cyan}Commit repo to the ${GIT_BRANCH} branch.\n"
    git add .
    git commit -m "deploy to ${GIT_BRANCH} branch"
    git push origin $GIT_BRANCH
    printf "${Green}Repository pushed to origin.\n"
}


pre_deploy_message(){
    printf "${NC}----------------------------------------\n"
    printf "${Red}           gitdeploy tool\n"
    printf "${Yellow} Deploying to STAGING Server.\n"
    printf "${NC}----------------------------------------\n"
}


post_deploy_message(){
    printf "\n${Red}Post-Deploy Tasks.\n"
    printf "${Orange}1. Enter License into WP Real Media Library Plugin.\n\n"

    printf "\n${Red}Push to LIVE by creating a repository release.\n"
}


cli_colours() {
    NC='\033[0m'               # NO Colour (reset)
    Black='\033[0;30m'         # Black
    Red='\033[0;31m'           # Red
    Green='\033[0;32m'         # Green
    Yellow='\033[0;33m'        # Yellow
    Orange='\033[0;34m'        # Orange
    Purple='\033[0;35m'        # Purple
    Cyan='\033[0;36m'          # Cyan
    White='\033[0;37m'         # White
}


cli_colours
pre_deploy_message

question_vagrant_db
if [ "$ANSWERDB" != "${ANSWERDB#[Yy]}" ] ;then
    get_vagrant_id 
    dump_database  
    move_vagrant_db_to_deploy_repo 
fi

question_submodules
if [ "$ANSWERSUBMODULES" != "${ANSWERSUBMODULES#[Yy]}" ] ;then
    update_all_submodules
fi

commit_changes_to_repo 
post_deploy_message