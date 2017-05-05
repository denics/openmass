@api
Feature: Emergency Alert Publisher
  As a emergency alert publisher member,
  I want a role to be able to create / edit / delete only alert content types
  so I can manage the alerts on the site.

  Scenario: Verify that emergency alert pub member can only see content menu item
    Given I am logged in as a user with the "emergency_alert_publisher" role
    And I am on "admin/content"
    Then I should see the link "Content" in the admin_menu
    And I should not see the link "Structure" in the admin_menu
    And I should not see the link "Appearance" in the admin_menu
    And I should not see the link "Extend" in the admin_menu
    And I should not see the link "Configuration" in the admin_menu
    And I should not see the link "People" in the admin_menu
    And I should not see the link "Reports" in the admin_menu
    And I should not see the link "Help" in the admin_menu

  Scenario: Verify that emergency alert pub team user can see 'Add content' button
    Given I am logged in as a user with the "emergency_alert_publisher" role
    And I am on "admin/content"
    Then I should see the link "Add content"

  Scenario: Verify emergency alert pub team user cannot create / edit / delete users
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "admin/people"
    Then the response status code should be 403

  Scenario: Verify emergency alert pub team user cannot create action content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/action"
    Then the response status code should be 403

  Scenario: Verify emergency alert pub team user cannot create basic page content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/page"
    Then the response status code should be 403

  Scenario: Verify emergency alert pub team user cannot create section landing content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/section_landing"
    Then the response status code should be 403

  Scenario: Verify emergency alert pub team user cannot create stacked layout content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/stacked_layout"
    Then the response status code should be 403

  Scenario: Verify emergency alert pub team user cannot create subtopic content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/subtopic"
    Then the response status code should be 403

  Scenario: Verify emergency alert pub team user cannot create topic content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/topic"
    Then the response status code should be 403
    
  #http response 200 is a successful response
  Scenario: Verify emergency alert pub team user can create action content
    Given I am logged in as a user with the "emergency_alert_publisher" role
    When I go to "node/add/emergency_alerts"
    Then the response status code should be 200






