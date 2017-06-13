@api
Feature: Better Field Descriptions
  As a content team member and as a developer,
  I want to be able to provide better help text to content authors
  so they can more easily create the best content experience for the constituents of Massachusetts.

  Scenario: Verify that developer user can manage and edit better field descriptions
    Given I am logged in as a user with the "developer" role
    When I go to "admin/config/content/better_field_descriptions/bundles"
    Then the response status code should be 200
    And I should see the link "Settings" in the admin_local_tasks

  Scenario: Verify that content team user can edit better field descriptions for hardened content types
    Given I am logged in as a user with the "content_team" role
    When I go to "admin/config/content/better_field_descriptions/bundles"
    Then the response status code should be 200
    And I should not see the link "Settings" in the layout_container
    And I should see "Contact Information" in the "#edit-bundles" element
    And I should see "Guide Page" in the "#edit-bundles" element
    And I should see "How-To Page" in the "#edit-bundles" element
    And I should see "Location Page" in the "#edit-bundles" element
    And I should see "Organization Landing Page" in the "#edit-bundles" element
    And I should see "Service Detail Page" in the "#edit-bundles" element
    And I should see "Service Page" in the "#edit-bundles" element
    And I should see "Topic Page" in the "#edit-bundles" element

  # This scenario is commented out because it does not roll back the alteration to config.
  # But it is kept for future reference, in case functionality needs testing.
#  Scenario: Verify that content team user can add better field description
#    Given I am logged in as a user with the "content_team" role
#    And I am at "admin/config/content/better_field_descriptions/bundles"
#    And I fill in "edit-bundles-topic-page-field-topic-ref-content-cards-description" with "Behat description"
#    And I select the radio button "Between title and input" with the id "edit-bundles-topic-page-field-topic-ref-content-cards-position-2"
#    And I press "Save configuration"
#    When I am editing an unpublished "topic_page" with the title "Behat Topic Page"
#    Then I should see text matching "Behat description"
