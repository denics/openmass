@api
Feature: Mass Flagging
  As an authenticated user I can escalate content that needs remediation
  so the end user can view the best content possible.

  Scenario: Verify Authenticated user can see Flag link
    Given I am logged in as a user with the "authenticated" role
    When I go to "/"
    Then I should see the link "Flag"

  Scenario: Verify Anonymous user cannot see Flag link
    When I go to "/"
    Then I should not see the link "Flag"

  Scenario: Verify Admin can see Watch flag config
    Given I am logged in as a user with the "administrator" role
    When I go to "/admin/structure/contact"
    Then I should see the text "Flag Content"

  Scenario: Verify Watch flag config values
    Given I am logged in as a user with the "administrator" role
    When I go to "admin/structure/contact/manage/flag_content"
    Then the "label" field should contain "Flag Content"
    Then the "recipients" field should contain "MassITDigitalServices@mass.gov"
    Then the "message" field should contain "You have successfully flagged the selected content."
    Then the "redirect" field should contain "/"

  Scenario: Verify Authenticated user can flag a node
    Given I am logged in as a user with the "authenticated" role
    When I go to "/"
    Then I click "Flag"
    # Switch once #1074 gets merged in.
    Then I should see the text "Content Being Flagged"
    # Then I should see the text "Flag this page"
    Then the "Content Being Flagged" field should contain "Home (3666)"
    When I press the "Send message" button
    Then I should not see the text "You have successfully flagged the selected content."
    Given for "Reason For Flagging" I enter "Test feedback"
    When I press the "Send message" button
    Then I should see the text "You have successfully flagged the selected content."
