@api
Feature: Author Role
  As an author whose work needs approval before publication,
  I want a role to be able to create / edit certain kinds of content without the ability to publish
  so I can create the best content experience for the constituents of Massachusetts.

  Scenario: Verify that authors can only see content menu item
    Given I am logged in as a user with the "author" role
    And I am on "admin/content"
    Then I should see the link "Content" in the admin_menu
    And I should not see the link "Structure" in the admin_menu
    And I should not see the link "Appearance" in the admin_menu
    And I should not see the link "Extend" in the admin_menu
    And I should not see the link "Configuration" in the admin_menu
    And I should not see the link "People" in the admin_menu
    And I should not see the link "Reports" in the admin_menu
    And I should not see the link "Help" in the admin_menu

  Scenario: Verify that author does not have permission to change site code or administer users
    Given I am logged in as a user with the "author" role
    Then I should not have the "administer modules, administer software updates, administer themes, administer users" permissions

  Scenario: Verify that author can see 'Add content' button
    Given I am logged in as a user with the "author" role
    And I am on "admin/content"
    Then I should see the link "Add content"

  #http response 200 is a successful response
  Scenario: Verify author can create contact_information content
    Given I am logged in as a user with the "author" role
    When I go to "node/add/contact_information"
    Then the response status code should be 200

#  Scenario: Verify author can create guide_page content
#    Given I am logged in as a user with the "author" role
#    When I go to "node/add/guide_page"
#    Then the response status code should be 200

#  Scenario: Verify author can create how_to_page content
#    Given I am logged in as a user with the "author" role
#    When I go to "node/add/how_to_page"
#    Then the response status code should be 200

#  Scenario: Verify author can create location content
#    Given I am logged in as a user with the "author" role
#    When I go to "node/add/location"
#    Then the response status code should be 200

  Scenario: Verify author can create org_page content
    Given I am logged in as a user with the "author" role
    When I go to "node/add/org_page"
    Then the response status code should be 200

#  Scenario: Verify author can create service_page content
#    Given I am logged in as a user with the "author" role
#    When I go to "node/add/service_page"
#    Then the response status code should be 200
  Scenario: Verify author can create service_page content
    Given I am logged in as a user with the "author" role
    When I go to "node/add/service_page"
    Then the response status code should be 200

  Scenario: Verify author cannot create / edit / delete users
    Given I am logged in as a user with the "author" role
    When I go to "admin/people"
    Then the response status code should be 403

  Scenario: Verify that author can use draggable views
    Given I am logged in as a user with the "author" role
    Then I should have the "access draggableviews" permission

  Scenario: Verify that author can edit unpublished content, request review, but not publish
    Given I am logged in as a user with the "author" role
    And I am editing an unpublished "org_page" with the title "Behat Org Page"
    Then I should see the button "Save and Create New Draft" in the edit_actions
    And I should see the button "Save and Request Review" in the edit_actions
    And I should not see the text "Save and Publish" in the edit_actions

  Scenario: Verify that author can add new or existing Documents to content, but not edit any Documents
    Given I am logged in as a user with the "author" role
    Then I should have the "create media, update media, access media overview" permissions
    And I should not have the "update any media" permissions


