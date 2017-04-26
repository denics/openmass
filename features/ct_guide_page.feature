@api
Feature: Contact Info Content type
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
