# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)


## Upcoming (add in progress changes here)

### Added
- Added the route_iframes module to support dashboards as a tab / local task on nodes / pages.
- DP-4211 - Add iframe paragraph to service details and location details.
- DP-4179 - (for devs) Add docs for updating dependency packages to repo readme + mayflower docs
- Adds a "category" metatag when viewing most nodes. The category is dynamically determined based on the content type and will allow future filtered searches using Google CSE.
- Adds a "Primary audience" field to Guide Page, How-to Page, Service Page, and Service Detail Page content types. The value of this field is used to populate an "audience" metatag for those pages, allowing Google CSE to filter by audience.

### Changed
- DP-4285 - Adds end date to field_event_date on events. Front end does not render end date yet.

### Removed



## [0.27.0] - August 8, 2017

### Added
- Added notification message for users automatically added as content watchers.
- Added iframe paragraph to service details and location details.
- Added basic documentation for "Watching" for users
- Added Theming / Validation for Decisions
- Added Theming / Validation for Policy Advisory
- Added Theming / Validation for Executive Orders

### Changed
- Changed label on "Related Parks" to "Related Locations"
- Removed a bar showing on pilot.mass.gov header
- Fixed Location pages show page level alert even with no alert content
- Fixed Pilot.mass.gov design is changing when choosing a different language
- Fixed Topic page title in the cards are not wrapping with IE11



## [0.26.0] - August 3, 2017

### Added
- DP-4565 - Implement structured data (schema.org) for "Topic Page". When you view the source code of a Topic page, you will now see the JSON-LD object that maps to this page type.
- DP-4521 - Added documentation on how to map content types to schema.org.
- DP-3882 - (For devs) `composer.json` and `composer.lock` are now validated on CircleCI under the "test" section of the `circle.yml` file (See "Troubleshooting" in README.md).
- DP-4809 - Changed permissions to allow authors and editors to use new content type location details.
- DP-3882 - `composer.json` and `composer.lock` are now validated on CircleCI under the "test" section of the `circle.yml` file (See "Troubleshooting" in README.md).
- Added notification message for users automatically added as content watchers.
- Change merge driver to union for changelog so we don't always have conflicts.

### Changed
- DP-4589 - Added custom template suggestion for Flag Content contact form to ensure proper textarea rendering.
- DP-4721 - Update Mass Watching module help page so users understand how the module works.
- DP-3882 - (For devs) Halt `composer install` operation on CircleCI when a referenced patch fails to install (See "Troubleshooting" in README.md).
- DP-4589 - Added custom template suggestion for Flag Content contact form to ensure proper textarea rendering.
- DP-4773 - Hides flagging link container if user is not authenticated.



## [0.25.0] - Aug 1, 2017

### Added
- Added dashboard admin/ma-dash/service-content to see content related to a service.
- "Organization Pages" and "Service Pages" now have event listing.
- New "Tester" role for select users to try new features.
- New oauth-secured content metadata API at `/api/v1/content-metadata`

### Changed
- Fix bug where pages with no table data (i.e. How-To's with no fees) were not loading.



## [0.24.1] - July 27, 2017

### Added
- Adds form_page content type.
- Adds custom field type for form embed.
- Modifies topic page to allow it to display as a section landing. Requires post update of.

### Changed
- Removes "publish" and "unpublish" actions from admin/content and adds proper Workbench Moderation states instead.
- Add logic around link fields to resolve error where node is deleted but link still exists.
- Fixed url encoding for the 'target' property value on 'How-To Page' content type.
- Refactor contact links for contact_information pages
- Update mayflower to 5.5.0
- Watch notification emails include revision author



## [0.24.0] - July 25, 2017

### Added
- Implemented structured data (schema.org) for the following three content types: Fee, Guide and Location. When you visit these page types, they now render JSON-LD (viewable in source page source code).
- Introduced "Content Flagging" capability. As a mass.gov internal user, I can flag a piece of content that appears inappropriate or incorrect.



## [0.23.1] - July 20, 2017

### Added
- DP-0000 - Add "stamp-and-deploy" script. Under "deployment" in "circle.yml", the "commands" section for branches that CircleCI acts upon, are now in a bash script "./scripts/stamp-and-deploy". (Sorry, no Jira ticket - Youssef & Moshe)
- DP-4179 For devs: update project documentation with steps to update a dependency, including some Mayflower-specific docs.

### Changed
- DP-0000 - Fine-tune branch name regex for CircleCI; i.e. act on any branch name that is not "develop". Only push to Acquia if it is not "develop". (Sorry, no Jira ticket - Youssef & Moshe)
- Adds "stamp-and-deploy" script. Under "deployment" in "circle.yml", the "commands" section for branches that CircleCI acts upon, are now in a bash script "./scripts/stamp-and-deploy".
- Adds email notifications for watchers of content
- Adds preliminary configuration for Advisory content type
- Adds preliminary configuration for Decision content type

### Changed
Fine-tune branch name regex for CircleCI; i.e. act on any branch name that is not "develop". Only push to Acquia if it is not "develop".



## [0.23.0] - July 18, 2017

### Added

- When editing a piece of content, you are automatically subscribed to get email notifications when that content changes in the future.
- Executive Orders are now a content type! The Governor will have a field day.
- Devs: The relationship between Mayflower and OpenMass is now documented.
- Devs: Config mistmatches are checked at build time.
- Added content fields for assigning labels such as top content, sticky content, and secretariat ownership.

### Changed
- Content cards on topic pages are strictly only able to link to other topic pages, organizations, and services. This helps keep topic pages clean and structured.
- Legacy redirects cannot be used more than once.
- Devs: Composer state is fixed. Composer install works again.



## [0.22.3] - July 13, 2017

### Added
- On edit.mass.gov, you can manually watch and unwatch content. P.S. No watch notifications are sent yet, but you can sign up to watch something.
- Metadata for Service Details is published via a Schema.org mapping (good for search engines)
- Photo credits added to images in hardened content types.

### Changed

- In the edit experience, when a required piece of content hasn't been added yet, it says "No <part> added yet." For example, on a How-to page, if no next steps are added, it'll say "No next step added yet".
- Accessibility of the directions link on Locations pages is improved.
- An unlimited number of tasks can be added to Service pages
- Press releases changed to news, with some new fields too!




## [0.22.2] - July 11, 2017

### Changed
- Location pages no longer render blank location-specific page level alert by default (i.e. when there is no location alert content).




## [0.22.1] - July 10, 2017

### Changed
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

### Changed
- Users can only make edits to mass.gov from approved networks, which should make it less likely for intruders to modify the site.
- Patched a security vulnerability with two factor authentication

### Removed
- How-to pages no longer show contacts in too many places and the sidebar alignment and headings are fixed.
