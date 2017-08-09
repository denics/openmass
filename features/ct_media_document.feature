@api
Feature: Topic Content type
  As a MassGov content editor,
  I want to be able to add metadata content.

  Scenario: Verify metadata fields exist
    Given I am logged in as a user with the "administrator" role
    When I visit "/media/add/document"
    Then I should see the text "Alternative Title"
    Then I should see the text "Geographic Area"
    Then I should see the text "Language"
    Then I should see the text "Content Type"
    Then I should see the text "Subjects"
    Then I should see the text "Additional Info"
    Then I should see the text "Related Content"
    Then I should see the text "Part Of"
    Then I should see the text "Public Access Level"
    Then I should see the text "License"
    Then I should see the text "Rights"
    Then I should see the text "Data Dictionary"
    Then I should see the text "Conforms To"
    Then I should see the text "Data Quality"
    Then I should see the text "System of Records"
    Then I should see the text "OCLC Number"
    Then I should see the text "File Migration ID"
    Then I should see the text "Legacy URL"
    And I should see the text "Checksum"
