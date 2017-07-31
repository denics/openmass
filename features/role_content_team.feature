@api
Feature: Content Management
  As a content team member,
  I want a role to be able to create / edit / delete all content
  so I can create the best content experience for the constituents of Massachusetts.

  Scenario: Verify that content team member can only see content menu item
    Given I am logged in as a user with the "content_team" role
    And I am on "admin/content"
    Then I should see the link "Content" in the admin_menu
    And I should not see the link "Structure" in the admin_menu
    And I should not see the link "Appearance" in the admin_menu
    And I should not see the link "Extend" in the admin_menu
    And I should not see the link "Configuration" in the admin_menu
    And I should see the link "People" in the admin_menu
    And I should not see the link "Reports" in the admin_menu
    And I should not see the link "Help" in the admin_menu

  Scenario: Verify that content team user has broad permission to deal with all content and can use workbench
    Given I am logged in as a user with the "content_team" role
    Then I should have the "administer nodes, revert all revisions" permissions
    And I should have the "use workbench_tabs, view any unpublished content, view moderation states, view latest version" permissions

  Scenario: Verify that content team user does not have permission to change site code or administer users
    Given I am logged in as a user with the "content_team" role
    Then I should not have the "administer modules, administer software updates, administer themes" permissions

  Scenario: Verify that content team user can see 'Add content' button
    Given I am logged in as a user with the "content_team" role
    And I am on "admin/content"
    Then I should see the link "Add content"

  Scenario: Verify content team user cannot create / edit / delete users
    Given I am logged in as a user with the "content_team" role
    When I go to "admin/people"
    Then the response status code should be 200

  #http response 200 is a successful response
  Scenario: Verify content team user can create action content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/action"
    Then the response status code should be 200

  Scenario: Verify content team user can create basic page content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/page"
    Then the response status code should be 200

  Scenario: Verify content team user can create section landing content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/section_landing"
    Then the response status code should be 200

  Scenario: Verify content team user can create stacked layout content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/stacked_layout"
    Then the response status code should be 200

  Scenario: Verify content team user can create subtopic content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/subtopic"
    Then the response status code should be 200

  Scenario: Verify content team user can create topic content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/topic"
    Then the response status code should be 200

  Scenario: Verify content team user can create topic_page content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/topic_page"
    Then the response status code should be 200

  Scenario: Verify content team user can create contact_information content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/contact_information"
    Then the response status code should be 200

  Scenario: Verify content team user can create guide_page content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/guide_page"
    Then the response status code should be 200

  Scenario: Verify content team user can create how_to_page content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/how_to_page"
    Then the response status code should be 200

  Scenario: Verify content team user can create location content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/location"
    Then the response status code should be 200

  Scenario: Verify content team user can create org_page content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/org_page"
    Then the response status code should be 200

  Scenario: Verify content team user can create service_page content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/service_page"
    Then the response status code should be 200

  Scenario: Verify content team user can create service details content
    Given I am logged in as a user with the "content_team" role
    When I go to "node/add/service_details"
    Then the response status code should be 200

  Scenario: Verify content team user can access the menu
    Given I am logged in as a user with the "content_team" role
    When I go to "admin/structure/menu"
    Then the response status code should be 200

  Scenario: Verify content team user can access the icons
    Given I am logged in as a user with the "content_team" role
    When I go to "admin/structure/taxonomy/manage/icons/overview"
    Then the response status code should be 200

  Scenario: Verify that content team user can use draggable views
    Given I am logged in as a user with the "content_team" role
    Then I should have the "access draggableviews" permission
