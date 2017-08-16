@api
Feature: Regulation Page Content type
  As a MassGov alpha content editor,
  I want to be able to add regulation pages,
  so that I can inform people about regulations and enable them to take action for it.

  Scenario: Verify that the regulation content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "regulation" content has the correct fields

  Scenario: Verify that pathauto patterns are applied to regulation node.
    Given I am viewing an "regulation" content with the title "Test Regulation"
    Then I am on "regulations/test-regulation"
