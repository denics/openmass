@api
Feature: Paragraph type definitions
  As a MassGov alpha content editor,
  I want to be able to add content for actions (a bedrock of the alpha release) for pre-determined journeys,
  so that I can help Bay Staters get the best information they need to fulfill basic tasks.

  Scenario: Verify that the action_area paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "action_area" paragraph has the fields:
      | field                 | widget             |
      | field-area-action-ref | Paragraphs Classic |

  Scenario: Verify that the action_step paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "action_step" paragraph has the fields:
      | field                 | widget                    |
      | field-para-icon-term  | Select list               |
      | field-title           | Textfield                 |
      | field-content         | Text area (multiple rows) |

  Scenario: Verify that the action_step_numbered paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "action_step_numbered" paragraph has the fields:
      | field         | widget                    |
      | field-title   | Textfield                 |
      | field-content | Text area (multiple rows) |

  Scenario: Verify that the action_step_numbered_list paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "action_step_numbered_list" paragraph has the fields:
      | field                            | widget                    |
      | field-action-step-numbered-items | Paragraphs Classic        |

  Scenario: Verify that the action_set paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "action_set" paragraph has the fields:
      | field                  | widget                    |
      | field-related-content  | Autocomplete              |
      | field-featured-content | Autocomplete              |
      | field-image            | Entity browser            |

  Scenario: Verify that the callout_link paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "callout_link" paragraph has the fields:
      | field      | widget |
      | field-link | Link   |

  Scenario: Verify that the callout_button paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "callout_button" paragraph has the fields:
      | field      | widget |
      | field-link | Link   |

  Scenario: Verify that the callout_alert paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "callout_alert" paragraph has the fields:
      | field      | widget |
      | field-link | Link   |

  Scenario: Verify that the emergency_alert paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "emergency_alert" paragraph has the fields:
      | field                           | widget             |
      | field-emergency-alert-content   | Paragraphs Classic |
      | field-emergency-alert-link      | Link               |
      | field-emergency-alert-message   | Textfield          |
      | field-emergency-alert-timestamp | Date and Time      |

  Scenario: Verify that the file_download paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "file_download" paragraph has the fields:
      | field           | widget         |
      | field-downloads | Entity browser |

  Scenario: Verify that the icon paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "icon" paragraph has the fields:
      | field                | widget         |
      | field-title          | Textfield      |
      | field-para-icon-term | Select List    |

  Scenario: Verify that the full_bleed paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "full_bleed" paragraph has the fields:
      | field                 | widget                    |
      | field-full-bleed-ref  | Paragraphs Classic        |

  Scenario: Verify that the iframe paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "iframe" paragraph has the fields:
      | field        | widget       |
      | field-url    | Link         |
      | field-height | Number field |

  Scenario: Verify that the quick_action paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "quick_action" paragraph has the fields:
      | field             | widget     |
      | field-link        | Link       |

  Scenario: Verify that the rich_text paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "rich_text" paragraph has the fields:
      | field      | widget                    |
      | field-body | Text area (multiple rows) |

  Scenario: Verify that the action_set paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "search_band" paragraph has the fields:
      | field                  | widget                    |
      | field-image            | Entity browser            |
      | field-caption          | Textfield                 |
      | field-name             | Textfield                 |
      | field-description      | Textfield                 |
      | field-link-six         | Link                      |
      | field-title            | Textfield                 |

  Scenario: Verify that the stat paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "stat" paragraph has the fields:
      | field             | widget        |
      | field-stat        | Textfield     |
      | field-description | Textfield     |
      | field-alignment   | Select list   |

  Scenario: Verify that the slideshow paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "slideshow" paragraph has the fields:
      | field            | widget             |
      | field-slideshow  | Entity browser     |

  Scenario: Verify that the subhead paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "subhead" paragraph has the fields:
      | field       | widget        |
      | field-title | Textfield     |

  Scenario: Verify that the map paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "map" paragraph has the fields:
      | field       | widget                   |
      | field-map   | Google Map Field default |

  Scenario: Verify that the video paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "video" paragraph has the fields:
      | field                  | widget       |
      | field-video-caption    | Textfield    |
      | field-video-id         | Textfield    |
      | field-video-source     | Select list  |

  Scenario: Verify that the contact group paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "contact_group" paragraph has the fields:
      | field                      | widget              |
      | field-title                | Textfield           |
      | field-contact-info         | Paragraphs Classic  |
      | field-contact-group-layout | Select list         |

  Scenario: Verify that the contact info paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "contact_info" paragraph has the fields:
      | field         | widget      |
      | field-type    | Select list |
      | field-label   | Textfield   |
      | field-phone   | Telephone number       |
      | field-email   | Email       |
      | field-link    | Link        |
      | field-address | Text area (multiple rows)     |
      | field-caption | Textfield   |

  Scenario: Verify that the related_link paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "related_link" paragraph has the fields:
      | field             | widget     |
      | field-link        | Link       |

  Scenario: Verify that the hours paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "hours" paragraph has the fields:
      | field                   | widget              |
      | field-hours-description | Textfield           |
      | field-hours-group-title | Textfield           |
      | field-hours-structured  | Office hours (list) |

  Scenario: Verify that the pull_quote paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "pull_quote" paragraph has the fields:
      | field       | widget                    |
      | field-quote | Text area (multiple rows) |
      | field-name  | Textfield                 |
      | field-title | Textfield                 |

  Scenario: Verify that the completion_time paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "completion_time" paragraph has the fields:
      | field             | widget     |
      | field-time        | Textfield  |

  Scenario: Verify that the icon_links paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "icon_links" paragraph has the fields:
      | field            | widget              |
      | field-icon-link  | Paragraphs Classic  |

  Scenario: Verify that the icon_link paragraph type has the correct field configuration
    Given I am logged in as a user with the "administrator" role
    Then the "icon_link" paragraph has the fields:
      | field                | widget      |
      | field-para-icon-term | Select list |
      | field-link-single    | Link        |
