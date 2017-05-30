# Mass.gov Drupal Site
![Massachusetts Seal](http://www.mass.gov/resources/images/template/header-seal.gif)
*The official website of the Commonwealth of Massachusetts*

This is the codebase for the Drupal8 site powering (currently) pilot.mass.gov, and eventually mass.gov.
The Drupal theme (mass_theme) integrates with our Pattern Library [Mayflower](https://github.com/massgov/mayflower).

[![CircleCI](https://circleci.com/gh/massgov/mass.svg?style=svg&circle-token=591bd0354ff2fce66095cbb97087fd8eae090b5d)](https://circleci.com/gh/massgov/mass)


## Sections

- [Preparing your machine](#preparing-your-machine)
- [Setting up the local repo & virtual machine](#setting-up-the-local-repo-and-virtual-machine)
- [Setting up authentication & credentials](#setting-up-authentication-and-credentials)
- [Working locally](#working-locally)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)


## Preparing your machine
Make sure the following are installed on your machine:

Special MAC instructions will be labeled as the following, `macs are awesome`.

- VMWare, or [virtualBox](https://www.virtualbox.org/wiki/Downloads) >= 5.0 `brew cask install virtualbox`. (You'll need to install [homebrew](https://brew.sh/) first. This is not available on Windows host machines, [but there is a workaround](https://docs.ansible.com/ansible/intro_windows.html).)
- [vagrant](http://downloads.vagrantup.com/) >= 1.8 `brew cask install vagrant`
- [ansible](https://github.com/ansible/ansible) `brew install ansible`  
- [vagrant-hostmanager](https://github.com/smdahlen/vagrant-hostmanager) `vagrant plugin install vagrant-hostmanager`
- [vagrant-auto_network](https://github.com/oscar-stack/vagrant-auto_network) `vagrant plugin install vagrant-auto_network`
- [git](https://git-scm.com/downloads)


## Setting up the local repo and virtual machine
*In order to keep our development environment consistent across all developers' machines, we use a VM(Virtual Machine) for local development. The root project directory `mass` is 'shared' with `/var/www/mass.local` within the VM. Which is to say that changes done on either side (vm or local machine) are immediately available to the developer, whether they are on the vm or the local machine.*
**Our workflow is to author code changes & use git on your machine, but run commands *within the VM* (as that's where `drush` various other scripts can run).**

1. Get a local copy of this repo: `git clone git@github.com:massgov/mass.git`
1. Move into the project directory: `cd mass`
1. Set up the VM by running: `vagrant up`
1. You will be prompted for the administration password on your host machine. Obey.
1. SSH into the VM by running: `vagrant ssh`

**A few notes on our VM & convenience features**
- The project root is in `/var/www/mass.local`. There is an alias provided so you can type `www` which is the equivalent of `cd /var/www/mass.local`
- There are aliases for scripts within the `scripts/` directory. So you can just type, for example, `ma-test` to run tests, instead of `./scripts/ma-test`.

## Setting up authentication and credentials

### Acquia site aliases
*We use [Acquia](http://acquia.com) for our hosting environment. Follow the steps below to set up your Acquia [Drush](http://www.drush.org/en/master/) integration (Drush is a command-line interface for Drupal). This allows you to run Drush commands that interact with the Acquia environments within the VM (you'll meet the VM in the next section).*

1. Log in to your Acquia account at [www.acquia.com](http://www.acquia.com)
1. Navigate to the "Credentials" tab under your profile
1. Under the "Drush Integration" heading, click the "Download Drush aliases" link
1. This will download acquiacloud.tar.gz
1. Unzip this archive into a directory called `acquiacloud`
1. Place the `acquiacloud` directory within this project's `artifacts` directory. You should now have your Acquia Cloud credentials available at `artifacts/acquiacloud` (which will have two subdirectories: .acquia and .drush)


### Acquia command line API
*In addition to setting up aliases for drush to interact with Acquia, in order to perform full deployments you'll need to interact with Acquia's Cloud API. All Cloud API calls need to be authenticated in order to work. You authenticate a call using your user name (the email address with which you sign in to Acquia) and a private key that you can find on your Acquia Profile page.*
1. Move into the VM: `vagrant ssh`
1. Move into the project root: `www`
1. Authenticate with the Acquia cloud API `drush ac-api-login`. You'll be prompted for your email address and Acquia cloud API key. **You can find your cloud API key under Profile > Credentials and then the heading Cloud API**. Run the login command as follow:
```
$ drush ac-api-login
Email []: mass.developer@mass.gov
Key []: ********** [paste you api key here, then hit Enter]
Endpoint URL [https://cloudapi.acquia.com/v1]: [Enter for default endpoint url]
$ < you are brought back to command prompt >
```


### Composer Github authentication
*You only need to do this if you plan to run things like `composer update` that pull down new dependencies.*

1. Go to [GitHub Settings - Tokens](https://github.com/settings/tokens)
1. If setting up a new Token, make the scope only for the repo
1. Once you have completed creating the token, add it to your composer config `composer config -g github-oauth.github.com <oauthtoken>`


## Working Locally

### Setup a local database

1. SSH into the VM: `vagrant ssh`
1. Move into the project root within the VM: `www`
1. Run this script from inside the VM & follow prompts to get a local database set up: `ma-refresh-local`
1. You've got your very own mass.gov! Visit [mass.local](http://mass.local) in your browser of choice.


### Testing
*We use [behat](http://behat.org/en/latest/) for tests, and also run code linters. You should be running these whenever you change things.*

1. SSH into the VM: `vagrant ssh`, `www`
1. To test & lint all the things, run: `ma-test`
1. You may only want to run a single test at a time, if so use behat directly: `behat feature/<your test file>`

### Enabling development-only modules (e.g. Devel)
You are welcome to enable development-only modules (e.g. Devel, Kint, Views UI, Migrate UI, ...) in your VM. In your Pull Requests,
remember not to commit a core.extension.yml which enables these projects, and remember not to add the config files that
 belong to these modules. @todo We can help devs avoid this error by adding common development config files to
  our .gitignore. We could also add core.extension.yml and ask devs to use `git add --force` when they really want to change that
  file.


### Adding or altering configuration
*Many features will require edits to site configuration, such as adding new content types or altering the configuration of fields.  When you commit your changes, you must be careful only to add or edit the configuration you intend to alter.  This is made difficult by the fact that not all of the site's configuration is stored in code.*

* After performing a `drush config-export` NEVER perform a `git add .` or a `git add conf/drupal*`
* All changes to config yml files should be added carefully and individually.
* Suggested approach to make this process a bit easier:
   * Before starting work, `drush config-export` so you have a clean baseline.
   * Do any configuration changes needed, run tests.
   * Review (and perhaps copy or screenshot) Drupal's helpful list of the changes you have made to configuration at /admin/config/development/configuration
   * Run `drush config-export` again.
   * With your list in hand, add config files to your branch interactively, using `git add -i`, or tools provided by your IDE.


### Adding a new module to composer
*We manage dependencies with composer, and also track all dependencies with git. If you run `composer install`, you should see a 'Nothing to install or update' message, since git already has all the dependencies. Changes to dependencies still go through composer.*

1. Download modules with composer: ex. `composer require drupal/bad_judgement:^8.1`
1. Enable the module: `drush en bad_judgement`
1. Export the config with the module enabled: `drush config-export`
1. Sometimes composer will pull in a git directory instead of distribution files for a depdency. Please double check that there is no `.git` directory within the module code, and if so, please remove it. You can test with this command: `find . | grep ".git$"` and should ONLY see the output of: `./.git` (which is this projects git directory - don't delete that!).
1. Commit the changes to `composer.json`, `composer.lock`, `conf/drupal/config/core.extension.yml`, and the module code itself.


### Patching a module
Sometimes we need to apply patches from the Drupal.org issue queues. These patches should be applied using composer using the [Composer Patches](https://github.com/cweagans/composer-patches) composer plugin.


## Deployment
*We have various testing environments available in Acquia. Follow the steps to be able to deploy to them.*


### Deploying work to a testing environment
1. Outside the VM in the project root, make sure you've checked out the branch you want to test.
1. (_This step will happen automatically via CircleCI if you've pushed your code to a branch in GitHub. Do this if you haven't._) Push that branch up to the Acquia git remote `git push acquia <your branch>`. Now that acquia knows about your code, we can move it to a server and run the necessary setup.
1. SSH into the VM `vagrant ssh`, `cd /var/www/mass.local`
1. Run the deploy script and pass in the environment you want to deploy to. `drush ma-deploy <environment> <branch name>`
1. Wait a bit, watch the output, and soon you'll have your work on a remote environment for testing.

## Releases (Deploying to production)

There's a series of steps that the team follows, including process actions and discrete technical steps. See [docs/Release to Prod.md](docs/Release.md) for instructions.

## Troubleshooting

### Woah. Everything got weird in my local Drupal. What do I do.
If you want to reset everything to a working environment:
```
  vagrant ssh
  # inside the VM
  www
  ma-refresh-local
  # it'll ask for an environment, pull from prod (or whatever you want)
```
As long as you know your source code is stable, this should get you back up and running.


### DNS could not be found

If, on browsing to `http://mass.local`, you get the following error:
> mass.local’s server DNS address could not be found.

Then `vagrant up` may have failed half way through. When this happens, the `vagrant-hostmanager` plugin does not add the hostname to `/etc/hosts`. Try halting and re-upping the machine: `vagrant halt && vagrant up`. Reload is not sufficient to trigger updating the hosts file.

### public key warning on `ma-refresh-ma`

This happens when your local private key is not added to the vm, luckily we have a fix for that.

- Copy both `~/.ssh/id_rsa` and `~/.ssh/id_rsa.pub` into the VM.  Now we can't go directly to the VM`s `~/.ssh/` directory but if we put it in the docroot that will allow us to pick it up on the box.
- While SSH'd into the VM, move the key files from the docroot to `~/.ssh/`.

### Windows troubleshooting

- All host machine command line steps should be done from an elevated (admin) prompt.
- Make sure that you have run `git config --local core.symlinks true` to enable symlinks when
you checkout the repository.
- If the symlinks from the theme to the patternlab assets are not working after running composer,
delete the non-working symlinks and git checkout again.
- You will find it helpful to copy docroot/.gitattributes to the root of the project.  [@todo - add this to the automation]
- The Vagrantfile will required edits to run on Windows:
   - Run the ansible_local provisioner instead of ansible.
   - Do not disable the vagrant directory.
   - Depending on whether your computer has ntfs support, you may need to change the settings for the share.
