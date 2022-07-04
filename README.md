
<div id="top"></div>

<div align="center">


<img src="https://svg-rewriter.sachinraja.workers.dev/?url=https%3A%2F%2Fcdn.jsdelivr.net%2Fnpm%2F%40mdi%2Fsvg%406.7.96%2Fsvg%2Fcloud-sync.svg&fill=%234ADE80&width=200px&height=200px" style="width:200px;"/>

<h3 align="center">Deploy ParkourLabs.com</h3>

<p align="center">
Deploy ParkourLabs onto Production Server through Github Action.
</p>    
</div>

##  1. <a name='TableofContents'></a>Table of Contents


* 1. [Table of Contents](#TableofContents)
* 2. [About The Project](#AboutTheProject)
	* 2.1. [Built With](#BuiltWith)
	* 2.2. [Installation](#Installation)
* 3. [Usage](#Usage)
	* 3.1. [Deploying Process](#DeployingProcess)
		* 3.1.1. [Deployment to Staging Server](#DeploymenttoStagingServer)
		* 3.1.2. [Updating this repository with the vagrant version](#Updatingthisrepositorywiththevagrantversion)
		* 3.1.3. [Deployment to LIVE](#DeploymenttoLIVE)
* 4. [ Customising](#Customising)
* 5. [Troubleshooting](#Troubleshooting)
* 6. [Contributing](#Contributing)
* 7. [License](#License)
* 8. [Contact](#Contact)
* 9. [Changelog](#Changelog)


##  2. <a name='AboutTheProject'></a>About The Project


This is a deployment workflow that allows me to fully push a WordPress site (including the database) into version control and then deploy the site to the production server.


<p align="right">(<a href="#top">back to top</a>)</p>


###  2.1. <a name='BuiltWith'></a>Built With

This project was built with the following frameworks, technologies and software.

* [Wordpress](https://wordpress.org/)

<p align="right">(<a href="#top">back to top</a>)</p>


###  2.2. <a name='Installation'></a>Installation

Fork repo.

Note, all secrets and database are git-crypted. The real `wp-config.php` file is already on the server and is symlinked during the process.

<p align="right">(<a href="#top">back to top</a>)</p>


##  3. <a name='Usage'></a>Usage

###  3.1. <a name='DeployingProcess'></a>Deploying Process

####  3.1.1. <a name='DeploymenttoStagingServer'></a>Deployment to Staging Server

To deploy onto the staging server you can push to the `master` git branch. This will trigger the necessary steps to fully build the site from the code in this repository.

Use "-nodeploy" in the commit message   to not run a deploy and just commit to repo.

####  3.1.2. <a name='Updatingthisrepositorywiththevagrantversion'></a>Updating this repository with the vagrant version

If you wish to update this repository with the copy on vagrant, use the command;

```bash
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


####  3.1.3. <a name='DeploymenttoLIVE'></a>Deployment to LIVE

To deploy to the live server, you need to first add a release to this repository.

```bash
github > Releases > Draft a new Release
```

Then manually run the action.
```bash
github > Actions > [LIVE] Create a release > Run Workflow
```


##  4. <a name='Customising'></a> Customising

None.

##  5. <a name='Troubleshooting'></a>Troubleshooting

None.

<p align="right">(<a href="#top">back to top</a>)</p>


##  6. <a name='Contributing'></a>Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue.
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#top">back to top</a>)</p>



##  7. <a name='License'></a>License

Distributed under the MIT License.

MIT License

Copyright (c) 2022 Andy Pearson

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

<p align="right">(<a href="#top">back to top</a>)</p>



##  8. <a name='Contact'></a>Contact

Author Link: [https://github.com/IORoot](https://github.com/IORoot)

<p align="right">(<a href="#top">back to top</a>)</p>

##  9. <a name='Changelog'></a>Changelog

- v1.0.1 - Fixed GUID issue with the tree shortcode plugin. Also added gitdeploy.sh command.

- v1.0.0 - Setup repo and initial release of LABS V2.0.0
