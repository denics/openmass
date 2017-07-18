# Mayflower

[Mayflower](https://github.com/massgov/mayflower) is an open source Design System built in [Pattern Lab](http://patternlab.io/) (PHP) and maintained by Mass. Digital Services. Learn more about Mayflower in the [project's repo readme](https://github.com/massgov/mayflower).

## Mayflower and Mass.gov's Drupal theme

Mass.gov uses a custom Drupal theme, called [mass_theme](https://github.com/massgov/mass/tree/master/docroot/themes/custom/mass_theme), which uses Mayflower build artifacts as a project dependency for static assets (css, js, and image) as well as twig templates.  

This relationship is managed alongside other project dependencies through composer.  Learn more about this relationship in the [Mayflower Artifacts](#mayflower-artifacts) section below.

Mass.gov also uses a custom Drupal module, called [mayflower](https://github.com/massgov/mass/tree/master/docroot/modules/custom/mayflower), which acts as "glue code" to get `mass_theme` working with Mayflower.  Learn more in the [mass_theme](https://github.com/massgov/mass/tree/master/docroot/themes/custom/mass_theme) and [mayflower module](https://github.com/massgov/mass/tree/master/docroot/modules/custom/mayflower) readmes.

### A visual flow from Mayflower to Drupal
[![Mayflower + Drupal theme](assets/mayflower_drupal.png)](https://docs.google.com/presentation/d/1qWY-QoXu8JgazqnwNUoPyumu_XH-DgFj_iNoFiKu1YA/edit#slide=id.p)

## Mayflower Artifacts
[Mayflower Artifacts](https://github.com/massgov/mayflower-artifacts) is a repository containing versioned build artifacts of Mayflower. [Tagged releases](https://github.com/massgov/mayflower/releases) from Mayflower are automatically (via CircleCI) deployed to Mayflower Artifacts. *Learn more about [implementation details of Mayflower Artifacts](mayflower_assets.md)*.

### Updating Mayflower for Mass.gov
 
Follow these steps in order to keep Mass.gov up to date with new Mayflower releases.

1. Make sure you are familiar with the implications of each kind of Mayflower release (i.e. major, minor, patch) by reading the documentation about [semantic versioning applied to Mayflower](https://github.com/massgov/mayflower/#versioning).
1. When there is a new [Mayflower release](https://github.com/massgov/mayflower/releases), it's a good idea to read the release notes to get an idea of what has changed. 
1. Create a JIRA ticket (if one does not exist already) to make any theme/module/config updates required to implement the new version.
1. Follow the [composer workflow for adding packages](https://github.com/massgov/mass#adding-a-new-module-to-composer) on the branch created in the step above to update the `palantirnet/mayflower-artifacts` package.
    - This branch might also serve as an integration branch for any existing tickets to make Drupal changes for the new release that are in progress.
1. Make any other changes necessary to implement the new Mayflower version and workflow the ticket through internal and external reviews.

### Feature testing Mayflower changes in Drupal

If you're working on a ticket that requires updates in Mayflower that have not yet been released, you can [create a pre-release tag](https://help.github.com/articles/creating-releases/) in Mayflower and *temporarily* implement the corresponding Mayflower Artifacts pre-release tag in Mass.gov codebase.

#### Naming your pre-release tag

Pre-release tags should use the following naming convention:

`1.2.3-dev-1234` where `1.2.3` is the value of the next release and `-1234` is the issue or ticket number.

If you were working on `DP-4401` and Mayflower was currently on `5.0.0` and your ticket was proposing a [patch level change](https://github.com/massgov/mayflower/blob/DP-4080-Versioning/docs/versioning.md#patch-versions-001), you would name your pre-release tag:

```5.0.1-dev-4401```  

**If you do not see your pre-release tag being deployed:**

1. Make sure your tag name is unique.
1. Test your tag name with this [regex test](https://regex101.com/r/UJGppF/2).
1. Check CircleCI builds for Mayflower project to see if there are any errors.

#### Bumping mayflower-artifacts in your branch

Shortly after you cut a pre-release tag cut in `massgov/mayflower`, you should [see a corresponding tag](https://github.com/massgov/mayflower-artifacts/releases) in `massgov/mayflower-artifacts`.  This is the tag you'll include via composer on your `massgov/mass` feature branch.

Assuming your pre-release tag was `5.0.1-dev-4401`, you would follow these steps to update Mayflower in your feature branch for testing:

1. From your terminal, within the VM, update your mayflower version in composer.json by running `composer require --no-update palantirnet/mayflower-artifacts:5.0.1`
1. You'll be notified that composer.json has been updated
1. Get the updated mayflower dependency files by running `composer update palantirnet/mayflower-artifacts`
1. Commit only the files and file hunks which correspond to updating mayflower-artifacts
1. You should now have Mayflower updated in your feature branch.  Remember to rebuild your cache!

#### Use pre-release tags to...

* Facilitate local development on a feature or fix that integrates Mayflower and Drupal code updates
* Enable internal and/or external reviews of a branch *before* it is merged into develop

#### Do not use pre-release tags to...

* Update the version of Mayflower used in Mass.gov production!

