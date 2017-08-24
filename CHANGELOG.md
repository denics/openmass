# Changelog
All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)


## Upcoming (add in progress changes here)

### Added

### Changed
- DP-4416 - Changed label on "Related Parks" to "Related Locations"
- DP-4764 - Change service page to allow unlimited key information links and additional resources links
- DP-4441 - Users will notice various updates to field labels and help text throughout the Event content type.
- DP-4558 - Users will notice updated help text for various fields of the News content type (used for Speeches, Announcements, Press Releases, and Press statements).

### Removed

## [0.30.1] - August 21, 2017

### Added
- DP-4507 - Adds options to organization page to allow editors and authors to feature 2 news items on an org page and display a list of up to the next 6 most recent news items related to the given organization.
- DP-4224 - Add permissions for content administrators and developers to create url redirects
- Added a new field, More Info Link (field_contact_more_info_link) to Contact Information content type, back end only will not display on the front end yet.
- DP-4719 - Add short description to service page front-end.

### Changed
- DP-4883 - Update signees field on news content type to pull stored image URI for ('Url To Image') from field_bg_wide to field_sub_brand on the organization content type.
- DP-4443 - Updates location details content type name to "Location detail". Changes cardinality on "field_location_details_links_5" and adds custom validation to limit the number of related items to 5.
- DP-4345 - Allows multiple contacts to location content types. Changes cardinality on field_ref_contact_info on location ct.
- Content authors and editors can no longer see "Regulation Page" content type as an option to create new content.
- DP-4533 - Resolves error on event pages, when referenced contact information node in address field does not have an address.
- DP-2220 - Fixed the bullet lists under the "What you need" section on the How-To pages to become checkboxes when printing the page.
- DP-4557 Changed signees field on news content type to show both external and state org buttons so that users can better find the choice to pick internal state organization.
- DP-4557 - Changed signees field on news content type to show both external and state org buttons so that users can better find the choice to pick internal state organization.
- DP-4781 - Update flag form to use clearer text on what is actually occurring when submitting a form.
- DP-4592 - Add the more info link field to the Contact information content type, which enables authors to add an organization, location or service page.

### Removed

## [0.30.0] - August 17, 2017

### Added
- DP-4561 – Users can now choose a “type” of location. The default is a general location page. A user can select ‘park’ if they are making a park location page. Users select a value from a dropdown as they create/edit a “location page,” which will expose/hide certain additional fields. Note: a “type” must be selected the next time any existing or new location page is edited or created.
- DP-4342 - Users with permissions will now see an "all activities" field for "Location" pages with type: "Park"

### Changed
- Content authors and editors can no longer see "Regulation Page" content type as an option to create new content.
- DP-4533 - On the front end, event pages now load without error when they include a contact which has no address.
- Developers will notice that their Circle builds just got a little faster because we no longer pull files and documents down.
- Everyone can sleep a little easier tonight knowing that we are running the latest, greatest, and safest version of Drupal; with no visible changes to the back end (for authors, etc.) or frontend (constituents).

### Removed

## [0.29.0] - August 16, 2017

### Added
- DP-4561, DP-4342 - Adds all activities field and add conditional fields to Location page.
- MPRR-224, MPRR-445 - Added data.json formatting for document endpoint, which exposes a feed of d\Documents as an API.
- MPRR-366 - Added confirmation message on Media Document insert and update.
- MPRR-367 - Added Patch for Core - Link Module help text, which improves authoring experience of link fields.
- MPRR-409 - Added Auto-populate Media Document Form Fields from User profile, which allows authors to fill in a default value for 4 Document fields from their user profile.
- MPRR-456, MPRR-482, MPRR-367 - Added fields to Media Document for MassDocs compatibility. All new fields are in an "Advanced" tab. Documents now uses user_organization taxonomy for compatibility with existing user profiles.
- MPRR-456 - Added default taxonomy terms update hook for Media, which adds select lists for Language, License and Document "Content Type".
- MPRR-466, MPRR-486 - Added Migrate class to import files from Percussion via CSV source. 155,000 Documents will be ported from Percussion, and future updates and additions can be imported with this migration.
- MPRR-466 - Added Patch for media_entity_document module to avoid errors during migration. Document entities can be migrated even if a Percussion file that returns a 404.
- MPRR-471 - Added "All Documents" admin screen at admin/ma-dash/documents - Authors and Editors can now view and filter Documents from a central location.
- MPRR-475 - Added link to create Document to node/add screen, so that Authors and Editors can more easily create standalone Documents.
- MPRR-484 - robots.txt - hide Media entities (Documents, Video) from search engines, as they currently have no Mayflower styling.
- MPRR-487 - Updated "Add Existing File" Media Browser - old browser showed only Title. New browser shows, Organization, Updated Date, and User, and can be filtered and sorted accordingly.
- MPRR-487 - Authors and Editors have permissions "create media, update media, access media overview" and editor has "edit any media", to bring Document workflow inline with content.
- DP-2373 - Adds regulation content type and theming.
- DP-4960 - Allow users with the role Tester to use the content type "Form page"

### Changed
- Update Flag Content form to make it more clear to users on what it does.
- Updates timestamp used within the body of Watch emails.
- DP-4314 - Updates to Service Page sidebar, logo, offered by
- DP-4936 - Adjust ### to only show once and only if News type Press Release is selected.
- DP-4938 - Make Listing Description an optional field in News content type.
- DP-4879 - Remove contact icon and label is contact value does not exist.

### Removed
- DP-5080 - Disable Watch notifications in lower environments.
- DP-4967 - Updates timestamp used within the body of Watch emails to correctly reflect when the action occurred.
- DP-4571 - Fixes the authoring dashboard views under My Content (My Work, Needs Review, All Content) all now work correctly, have minor usability tweaks, and include a functioning Content Type filter.
- DP-4303 - Updates text of headers and subheaders on Organizational Landing Pages.

### Post Deploy
Follow post deploy step listed in the PR (https://github.com/massgov/mass/pull/925) to add the migration source data to the files directory.

## [0.28.1] - August 11, 2017

### Changed
- Revert to legacy iFrame solution to fix home + other pages.
- Fix with redeployment of DP-4285: Adds end date to field_event_date on events. Front end does not render end date yet.
- Makes the Audience field optional. Limits the 'Primary Audience' field to administrator users only.

### Post Deploy
Follow post deploy steps listed in the PR (https://github.com/massgov/mass/pull/1089) to re-add the "announcing pilot.mass.gov" youtube video to the home page.

## [0.28.0] - August 10, 2017

### Added
- Added the route_iframes module to support dashboards as a tab / local task on nodes / pages.
- DP-4211 - Add iframe paragraph to service details and location details.
- DP-4179 - (for devs) Add docs for updating dependency packages to repo readme + mayflower docs
- Adds a "category" metatag when viewing most nodes. The category is dynamically determined based on the content type and will allow future filtered searches using Google CSE.
- Adds a "Primary audience" field to Guide Page, How-to Page, Service Page, and Service Detail Page content types. The value of this field is used to populate an "audience" metatag for those pages, allowing Google CSE to filter by audience.
- Added notification message for users automatically added as content watchers.

### Changed
- DP-4416 - Changed label on "Related Parks" to "Related Locations"
- DP-5004 - Enables moderation for Advisory, Decision, Executive order, Legacy redirects.
- DP-4305 - Removes Inline Form Errors error message from the username field to replace the default invalid login message at top of the user login page.
- Updated capitalization of "Next Steps" and "More Info" in header/jump menu on How-to pages
- Fix bug where pages with no table data (i.e. How-To's with no fees) were not loading.
- Fixed email headers being used to send out Watch notifications from mass_flagging module
- DP-4285 - Adds end date to field_event_date on events. Front end does not render end date yet.
- Updated mass_flagging module to send Watch emails on local or Prod environments only.
- DP-5075 Allow authors to see help text for Watching feature.

### Removed
- DP-4211 - Add iframe paragraph to service details and location details.

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
- Removed a bar showing on pilot.mass.gov header
- Fixed Location pages show page level alert even with no alert content
- Fixed Pilot.mass.gov design is changing when choosing a different language
- Fixed Topic page title in the cards are not wrapping with IE11

### Removed
None.


[0.26.0] - August 3, 2017


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

### Removed
None.


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
