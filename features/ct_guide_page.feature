@api
Feature: Guide Page Content type
  As a MassGov alpha content editor,
  I want to be able to add guide pages,
  so that I can inform people about organizations and services.

  Scenario: Verify that the guide page content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then the content type "guide_page" has the fields:
      | field                           | tag        | type      | multivalue |
      | field-guide-page-lede           | textarea   |           | false      |
      | field-guide-page-bg-wide        | input      | submit    | false      |
      | field-guide-page-related-guides | input      | text      | false      |
      | field-guide-page-sections       | paragraphs |           | false      |

  Scenario: Verify that the contact info content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "guide_page" content has the correct fields
    And "guide_section" paragraph has the correct fields
    And "guide_section_3up" paragraph has the correct fields

  Scenario: Verify that the category metatag exists and has the correct value.
    Given I create a "guide_page" content:
      | title            | Test guide page |
      | moderation_state | published       |
    And I add a "guide_section" paragraph in the "field_guide_page_sections" field with values:
      | field_guide_section_name | Test title   |
      | field_guide_section_body | Test content |
    When I visit the newest page
    Then I should see a "category" meta tag of "services"
