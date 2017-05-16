@api
Feature: editor Role
  As an editor,
  I want a role to be able to create / edit / delete / publish certain targeted types of content
  so I can create the best content experience for the constituents of Massachusetts.

  Scenario: Verify that content team member can only see content menu item
    Given I am logged in as a user with the "editor" role
    And I am on "admin/content"
    Then I should see the link "Content" in the admin_menu
    And I should not see the link "Structure" in the admin_menu
    And I should not see the link "Appearance" in the admin_menu
    And I should not see the link "Extend" in the admin_menu
    And I should not see the link "Configuration" in the admin_menu
    And I should not see the link "People" in the admin_menu
    And I should not see the link "Reports" in the admin_menu
    And I should not see the link "Help" in the admin_menu

  Scenario: Verify that editor does not have permission to change site code or administer users
    Given I am logged in as a user with the "editor" role
    Then I should not have the "administer modules, administer software updates, administer themes, administer users" permissions

  Scenario: Verify that editor can see 'Add content' button
    Given I am logged in as a user with the "editor" role
    And I am on "admin/content"
    Then I should see the link "Add content"

  #http response 200 is a successful response
  Scenario: Verify editor can create contact_information content
    Given I am logged in as a user with the "editor" role
    When I go to "node/add/contact_information"
    Then the response status code should be 200

#  Scenario: Verify editor can create guide_page content
#    Given I am logged in as a user with the "editor" role
#    When I go to "node/add/guide_page"
#    Then the response status code should be 200

#  Scenario: Verify editor can create how_to_page content
#    Given I am logged in as a user with the "editor" role
#    When I go to "node/add/how_to_page"
#    Then the response status code should be 200

#  Scenario: Verify editor can create location content
#    Given I am logged in as a user with the "editor" role
#    When I go to "node/add/location"
#    Then the response status code should be 200

  Scenario: Verify editor can create org_page content
    Given I am logged in as a user with the "editor" role
    When I go to "node/add/org_page"
    Then the response status code should be 200

#  Scenario: Verify editor can create service_page content
#    Given I am logged in as a user with the "editor" role
#    When I go to "node/add/service_page"
#    Then the response status code should be 200

  Scenario: Verify editor cannot create / edit / delete users
    Given I am logged in as a user with the "editor" role
    When I go to "admin/people"
    Then the response status code should be 403

  Scenario: Verify that editor can use draggable views
    Given I am logged in as a user with the "editor" role
    Then I should have the "access draggableviews" permission
