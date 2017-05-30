@api
Feature: Location Details Page Content type
  As a MassGov alpha content editor,
  I want to be able to add location detail pages,
  so that I can inform people about additional information regarding locations.

  Scenario: Verify that the service details content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "location_details" content has the correct fields
    And "section" paragraph has the correct fields
