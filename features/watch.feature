@api
Feature: Mass Flagging
  As an authenticated user I want to be able to watch content that is important to me
  so I can collaborate successfully.

  Scenario: Verify Authenticated user can see Watch link
    Given I am logged in as a user with the "authenticated" role
    When I go to "/"
    Then I should see the link "Watch"

  Scenario: Verify Anonymous user cannot see Watch link
    When I go to "/"
    Then I should not see the link "Watch"

  Scenario: Verify Admin can see Watch flag config
    Given I am logged in as a user with the "administrator" role
    When I go to "/admin/structure/flags"
    Then I should see the text "Watch Content"

  Scenario: Verify Admin can see Watch flag config
    Given I am logged in as a user with the "administrator" role
    When I go to "/admin/structure/flags"
    Then I should see the text "Watch Content"

  Scenario: Verify Watch flag config values
    Given I am logged in as a user with the "administrator" role
    When I go to "/admin/structure/flags/manage/watch_content"
    Then the "flag_short" field should contain "Watch"
    Then the "flag_long" field should contain "Watching content will give you email notifications when future revisions have been published."
    Then the "flag_message" field should contain "Congratulations, you have joined the watchers."
    Then the "unflag_short" field should contain "Unwatch"
    Then the "unflag_long" field should contain "Unwatch will remove you from any email notification for future revisions."
    Then the "unflag_message" field should contain "Successfully removed from watchers."

  Scenario: Verify Authenticated user can watch a node
    Given I am logged in as a user with the "authenticated" role
    When I go to "/"
    Then I click "Watch"
    Then I should see the text "Unwatch"

  Scenario: Verify Authenticated use sees confirmation message after creating node
    Given I am logged in as a user with the "administrator" role
    When I go to "/node/add/page"
    Given for "Title" I enter "Test Flag"
    When I press the "Save and Create New Draft" button
    Then I should see the text "You are now watching Basic page Test Flag."
    Then I should see the text "You will be notified of any future changes made to this content."
    Then I should see the link "Learn more about this functionality"
    Then I should see the link "stop watching this content"
