@api
Feature: Admin
  As a admin,
  I want to have the most powerful permission so that there is clear separation of duties in roles.

  #http response 200 is success
  Scenario: Verify admin user can add modules
    Given I am logged in as a user with the "administrator" role
    When I go to "admin/modules"
    Then the response status code should be 200

  Scenario: Verify admin user can change the theme
    Given I am logged in as a user with the "administrator" role
    When I go to "admin/appearance"
    Then the response status code should be 200

  Scenario: Verify admin user can create / edit / delete users
    Given I am logged in as a user with the "administrator" role
    When I go to "admin/people"
    Then the response status code should be 200

  Scenario: Verify admin user can run update.php
    Given I am logged in as a user with the "administrator" role
    When I go to "/update.php"
    Then the response status code should be 200

  Scenario: Verify that developer user does not have permission to change site code or administer users
    Given I am logged in as a user with the "administrator" role
    Then I should have the "administer modules, administer software updates, administer themes, administer users" permissions

  Scenario: Verify that DBlog is off
    Given I am logged in as a user with the "administrator" role
    When I go to "/admin/dblog"
    Then the response status code should be 404

  Scenario: Verify that Drupal doesn't run cron
    Given I am logged in as a user with the "administrator" role
    When I go to "/admin/config/system/cron"
    Then I should see "Never" in the "select[name='interval'] option[selected='selected']" element