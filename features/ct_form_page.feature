@api
Feature: Form Page Content type
  As a MassGov alpha content editor,
  I want to be able to add form pages,
  so that I can add interactive forms to the site.

  Scenario: Verify that the form page content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "form_page" content has the correct fields

  Scenario: Verify that pathauto patterns are applied to Form Page nodes.
    Given I am viewing an "form_page" content with the title "Test Form Page"
    Then I am on "forms/test-form-page"