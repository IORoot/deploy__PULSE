# Deploying Process

## Deployment to Staging Server

To deploy onto the staging server you can push to the `master` git branch. This will trigger the necessary steps to fully build the site from the code in this repository.

Use "-nodeploy" in the commit message   to not run a deploy and just commit to repo.

## Updating this repository with the vagrant version

If you wish to update this repository with the copy on vagrant, use the command;

```
gitdeploy
```

This is a bash script in the script-library repository (https://github.com/IORoot/script-library/tree/master/deploy) that will do the following steps:

1. SSH into the specified vagrant machine (default dev.londonparkour.com) and run `dumpdb` in the vhost directory.
1. Move the dump file into this repo's `/wp-content/database/` folder so you have a copy of the latest vagrant DB.
1. Recursively update all git submodules in this repo to get all latest copies of themes and plugins.
1. Git add all.
1. Git commit to `master` unless otherwise specified.
1. Git push to github.

The `gitdeploy` command also takes a single argument to push to another branch.


## Deployment to LIVE

To deploy to the live server, you need to first add a release to this repository.

```
github > Releases > Draft a new Release
```

Then manually run the action.
```
github > Actions > [LIVE] Create a release > Run Workflow
```


## Changelog

v1.0.0 - Setup repo and initial release of PULSE V1.0.0 