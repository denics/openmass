# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)

## Upcoming (add in progress changes here)
### Added
- Add Watch link to all nodes.

### Changed
- Made the "Desktop Banner" image a required field within a Organization page.

### Removed
- Remove `artifacts` folder (no longer used)

## [0.22.2] - July 11, 2017

### Changed (fixed)

- Location pages no longer render blank location-specific page level alert by default (i.e. when there is no location alert content).

## [0.22.1] - July 10, 2017

### Changed (fixed)

- Mayflower module `Organisms::preparePageBanner` method now supports multiple image styles for the background image.  This fixes the rendering issues for banner images on Topic pages.

## [0.22.0] - July 05, 2017

### Added
- List service details parent pages on edit page.
- Add static release notes content type
- Add the location listings page with functional proximity search and checkbox filters
- Know what's happening? Now you do thanks to the Events content type
- Press release content type added, including adding an associated state organization.
- Hardened content types can be added to related content in stacked rows.
- Metadata for Service pages now exposed in the skin of Schema.org classes
- An organization (gov agency) can be associated with a user account. Information can be exported to CSV that shows all content a created by a given user and an org.

### Changed
- Updated Core Drupal to 8.3.4 https://www.drupal.org/project/drupal/releases?api_version%5B%5D=7234
- Add schema_metatag module to improve Drupal functionality with Schema.org
- Redirect page auto generates title confirms link start with mass.gov
- Add flag module that will allow us to create Flagging and watching feature
- Make searches more accessible
- Make service page links only link to content types. E.g. The All tasks field should only return how-to pages
- Make language selector box show in IE10-11.
- Add Fees to the sticky nav when the field is populated.
- Resolves error messsage on creating on location pages
- Content cards can now be organized in groups.
- XML sitemap is configurable post-deploy
- Embed images from rich text editors save the alt and title values
- It's movie time! YouTube links appear on Service pages w/ video links.
- Devs only: Git hub tag is no longer needed for release
- Devs only: Remove limitation on local builds making work more efficient
- Devs only: Local environment no longer requires importing own aliases
- Devs only: Make debug available via the command line
- Devs only: CIMY removed from deployment flow
- On Guide pages, there can now be as many key actions as you want and the editing experience has flatter navigation (has fewer tabs)
- Help text has been improved on Services content type.

### Removed
- Remove error message when better description is deleted
- Remove obsolete shortlinks
- Devs only: Make configuration changes easier to export / import.
- Remove extra decorative line when contact group does not have a title

## [0.21.2] - June 20, 2017

### Added
- None

### Changed
- Users can only make edits to mass.gov from approved networks, which should make it less likely for intruders to modify the site.
- Patched a security vulnerability with two factor authentication

### Removed
- How-to pages no longer show contacts in too many places and the sidebar alignment and headings are fixed.
