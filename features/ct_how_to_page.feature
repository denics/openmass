@api
Feature: How To Page Content type
  As a MassGov alpha content editor,
  I want to be able to add how to pages,
  so that I can inform people about how to complete tasks.

  Scenario: Verify that the how to page content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "how_to_page" content has the correct fields
    And "method" paragraph has the correct fields
    And "next_step" paragraph has the correct fields