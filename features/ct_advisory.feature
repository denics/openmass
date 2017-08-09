@api
Feature: Advisory Content type
  As a MassGov alpha content editor,
  I want to be able to add advisory pages,
  so that I can inform people about advisories.

  Scenario: Verify that the advisory content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "advisory" content has the correct fields
