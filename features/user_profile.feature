@api
Feature: User Profile
  As a MassGov manager,
  I want to ensure that the user profile has the right fields,
  so that the website can use those default values.

  Scenario: Verify that MassDocs fields are in user profile.
    Given I am logged in as a user with the "administrator" role
    And I am on "user/1/edit"
    Then I should see "MassDocs Default Values"
    And I should see "Uploading organization or department"
    And I should see "Author(s)"
    And I should see "Contact Name"
    And I should see "Contact Information"
