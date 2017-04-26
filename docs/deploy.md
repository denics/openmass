# TL;DR

To deploy to Acquia dev, run the following command from within Vagrant (*on a clean tag*):

`vendor/bin/phing -f build/deploy.xml`

# Deployment Artifact

A Drupal deployment consists of three things:
1) Code (managed in a version controll system, usually git)
2) A database (which reflects changes made by or in code)
3) Files (because the database references files)

Without all three components you do not have a fully deployable Drupal artifact.

The deployment artifact may be able to be build in-place (such is the case in a prod deployment where the database is not built in a separate environment but is changed "live" and in-place).

# Execution Order

When a deployment is needed, the following things must happen to the existing codebase, database, and files:

1) The codebase must be updated to reflect the desired deployment status.
2) The database needs to be updated in two ways: config needs to be imported *and then* update hooks need to be run.
3) Files need to stay in place.

Any deployment process, whether scripted or done by handed, must execute these steps in this order to be successful.

# Promoting Deployment Artifacts

## To Dev or Feature environments
Deploying to the dev environment involves building a full Drupal Deployment Artifact locally (by pulling the database and files from canonical, then running processes on them) and then putting all three components of the artifact in the dev environment at once.

1. You will want to know which database to download during the `phing install` step. You may use Acquia's tools to copy a production database to the environment you will be working with, e.g. Feature1.
1. Build and install. `vendor/bin/phing build install`
1. Cut a tag. `git tag -a int-ds5-12-week-two-0.1 -m"DS-5 branch deployed to Feature1"`
1. Push the tag. `git push origin int-ds5-12-week-two-0.1`
1. Delete the artifacts/acquia directory. `rm -Rf artifacts/acquia`
1. Deploy. `vendor/bin/phing -f build/deploy.xml -Dacquia.branch=int-ds5`
1. Choose the environment you want to populate, e.g. @massgov.feature1.
1. Provide the admin password.
1. Review the phing properties. If `deploy.code_path=build` then you are not on your tag for deployment. You should see something like `deploy.code_path=build-int-ds5-12-week-two-0.1`.
1. If phing properties are correct, type yes and watch the reticulating splines.
1. Login to [Acquia](http://cloud.acquia.com) to make sure the tag is deployed. Right now, this involves loading another branch (e.g. master) and then reloading the branch your deploy script just created (e.g. build-int-ds5-12-week-two-0.1). Otherwise, your code does not truly get deployed to that server. This is referred to as "wiggling the branches".
1. Remove the site from maintenance mode. `drush @massgov.feature1 sset system.maintenance_mode 0`

## To Staging
`@todo Moshe?`

## To Production
`@todo Moshe?`
