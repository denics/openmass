<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\user\Entity\User;
use Drupal\DrupalExtension\Context\MinkContext;
use Drupal\DrupalExtension\Context\DrupalContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext
{

  /** @var String The directory to save screenshots and html to. */
  private $debug_dir;

  /**
   * @var MinkContext
   */
  private $minkContext;

  /**
   * Context from the scope environment, which gives us access to the current
   * logged-in user.
   *
   * @var \Behat\MinkExtension\Context\MinkContext
   */
  protected $drupalContext;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct()
  {
  }

  /**
   * Grab the html of the page and save it.
   *
   * @Then save the html for the page
   * @Then save the html for the page with prefix :prefix
   *
   * @param string $prefix A string to prepend to the filename.
   */
  public function saveHtml($prefix = 'html') {
    $html_data = $this->getSession()->getDriver()->getContent();
    $filename = $this->debug_dir . $prefix . '-' .  time() . '.html';
    file_put_contents($filename, $html_data);
  }

  /**
   * @BeforeScenario
   */
  public function gatherContexts(BeforeScenarioScope $scope)
  {
    $environment = $scope->getEnvironment();
    $this->drupalContext = $environment->getContext(DrupalContext::class);
    $this->minkContext = $environment->getContext(MinkContext::class);
  }

  /**
   * @Then I should not see two feedback forms
   */
  public function iShouldNotSeeTwoFeedbackForms()
  {
    $general = '#Online Form - Feedback - Multi Page#i';
    $homepage = '#Online Form - Feedback - Homepage#i';
    $actual = $this->getSession()->getPage()->getText();

    if (preg_match($general, $actual) === 1) {
      $this->assertSession()->pageTextNotMatches($homepage);
    }

    if (preg_match($homepage, $actual) === 1) {
      $this->assertSession()->pageTextNotMatches($general);
    }
  }

  /**
   * Asserts a content type has a title and
   * fields provided in the form of a given type:
   * | field               | tag      | type  | multivalue | required |
   * | body                | textarea |       | true       |  true    |
   * | field-subheadline   | input    | text  | false      |  false   |
   * | field-author        | input    | text  | false      |  true    |
   * | field-summary       | textarea |       | true       |  false   |
   * | field-full-text     | textarea |       | true       |  false   |
   * | field-ref-sections  | select   |       | false      |  false   |
   *
   * Assumes fields are targeted with #edit-<fieldname>. For example,
   * "body" checks for the existence of the element, "#edit-body". Note, for
   * almost everything this will begin with "field-", like "field-tags".
   *
   * @Then the content type :content_type has the fields:
   *
   * @param String $content_type
   * @param TableNode $fieldsTable
   */
  public function assertFields($content_type, TableNode $fieldsTable) {
    $this->minkContext->visitPath('node/add/' . $content_type);
    // All content types have a title.
    $this->minkContext->assertElementOnPage('#edit-title-0-value');
    $this->checkFields($fieldsTable);
  }

  /**
   * Asserts a vocabulary has a name and
   * fields provided in the form of a given type:
   * | field               | tag      | type  | multivalue | required |
   * | body                | textarea |       | true       |  true    |
   * | field-subheadline   | input    | text  | false      |  false   |
   * | field-author        | input    | text  | false      |  true    |
   * | field-summary       | textarea |       | true       |  false   |
   * | field-full-text     | textarea |       | true       |  false   |
   * | field-ref-sections  | select   |       | false      |  false   |
   *
   * Assumes fields are targeted with #edit-<fieldname>. For example,
   * "body" checks for the existence of the element, "#edit-body". Note, for
   * almost everything this will begin with "field-", like "field-tags".
   *
   * @Then the taxonomy vocabulary :vocabulary_name has the fields:
   *
   * @param String $content_type
   * @param TableNode $fieldsTable
   */
  public function assertTaxonomyFields($vocabulary_name, TableNode $fieldsTable) {
    $this->minkContext->visitPath('admin/structure/taxonomy/manage/' . $vocabulary_name . '/add');
    // All taxonomy terms have a name.
    $this->minkContext->assertElementOnPage('#edit-name-0-value');
    // Check the fields using same logic as nodes
    $this->checkFields($fieldsTable);
  }

  /**
   * After navigation to a form, check that the listed form fields
   * have the listed attributes.
   * Called by assertFields() and assertTaxonomyFields().
   *
   * @param \Behat\Gherkin\Node\TableNode $fieldsTable
   */
  protected function checkFields(TableNode $fieldsTable) {
    foreach ($fieldsTable->getHash() as $row) {
      // Get all IDs that start with our field name. D8 prints fields
      // differently than D7, so this is necessary.
      $css_selector = '[id^=edit-' . $row['field'] . ']';

      $this->minkContext->assertElementOnPage($css_selector);
      $this->assertFieldType('edit-' . $row['field'], $row['tag'], $row['type']);
      $this->assertFieldMultivalue($row['field'], filter_var($row['multivalue'], FILTER_VALIDATE_BOOLEAN));
      if (isset($row['required'])) {
        $this->assertFieldRequired($row['field'], $row['tag'], filter_var($row['required'], FILTER_VALIDATE_BOOLEAN));
      }
    }
  }

  /**
   * Assert a paragraph has fields configured to the correct form widgets.
   *
   * @Then the :paragraph_type paragraph has the fields:
   * | field      | widget      |
   * | field-name | Textfield   |
   *
   * @param String $paragraph_type
   * @param TableNode $fieldsTable
   */
  public function assertParagraphFields($paragraph_type, TableNode $fieldsTable) {
    $this->minkContext->visitPath('admin/structure/paragraphs_type/' . $paragraph_type . '/form-display');

    foreach ($fieldsTable->getHash() as $row) {
      $id = 'edit-fields-' . $row['field'] . '-type';
      $this->minkContext->assertElementOnPage("[id^={$id}]");

      $widget = $this->getSession()->getPage()->find('css', "#{$id} option[selected='selected']")->getText();

      if (strtolower($widget) !== strtolower($row['widget'])) {
        throw new Exception(sprintf("Field %s has \"%s\" widget but should have \"%s\".", $row['field'], $widget, $row['widget']));
      }
    }
  }


  /**
   * Checks whether field is required.
   *
   * @param string $field
   * @param string $tag
   * @param bool $required
   * @throws \Exception
   */
  public function assertFieldRequired($field, $tag, $required) {
    $element_selector = $tag.'[id^=edit-' . $field . ']';
    $element = $this->getSession()->getPage()->find('css', $element_selector);
    if (NULL == $element) {
      throw new Exception(sprintf('Could not find %s to determine whether it is required',$field));
    }
    $element_is_required = $element->hasAttribute('required');
    if ($required && !$element_is_required) {
      throw new Exception(sprintf('Field %s should be required and is not', $field));
    }
    if (!$required && $element_is_required) {
      throw new Exception(sprintf('Field %s is required and should not be', $field));
    }

  }

  /**
   * @param $field
   * @param $multivalue
   * @throws \Exception
   */
  public function assertFieldMultivalue($field, $multivalue) {
    // Make a CSS selector for the "add more" button
    $add_more = str_replace('-', '_', $field) . '_add_more';
    $element = $this->getSession()->getPage()->find('css', '[name=' . $add_more . ']');

    // If the field is supposed to be multivalue but isn't, throw an error.
    if ($multivalue && is_null($element)) {
      throw new Exception(sprintf("Field %s is not multivalue but should be", $field));
    }

    // If the field is not supposed to be multivalue but is, throw an error.
    if (!$multivalue && !is_null($element)) {
      throw new Exception(sprintf("Field %s is multivalue but should not be", $field));
    }
  }

  /**
   * Test a field on the current page to see if it matches
   * the expected HTML field type.
   *
   * @Then the ":field" field is ":tag"
   * @Then the ":field" field is ":tag" with type ":type"
   *
   * @param string $field
   * @param string $expectedTag
   * @param string $expectedType
   * @throws \Exception
   */
  public function assertFieldType($field, $expectedTag, $expectedType = '') {
    $callback = 'assert' . ucfirst($expectedTag);
    if (!method_exists($this, $callback)) {
      throw new Exception(sprintf('%s is not a field tag we know how to validate.',
        $expectedTag));
    }
    $this->$callback($field, $expectedType);
  }
  /**
   * Verify the field is a textarea.
   *
   * @param $field
   * @param $expectedType
   * @throws Exception
   */
  public function assertTextarea($field, $expectedType) {
    $element = $this->getSession()->getPage()->find('css', '[id^=' . $field . '-wrapper]');
    if (NULL == $element->find('css', 'textarea.form-textarea')) {
      throw new Exception(sprintf("Couldn't find %s of type textarea.", $field));
    }
  }
  /**
   * Verify the field is an input field of the given type.
   *
   * @param $field
   * @param $expectedType
   * @throws Exception
   */
  public function assertInput($field, $expectedType) {
    $element = $this->getSession()->getPage()->find('css', '[id^=' . $field . ']');
    if (NULL == $element || NULL == $element->find('css', 'input[type="' . $expectedType . '"]')) {
      throw new Exception(sprintf("Couldn't find %s of type %s", $field, $expectedType));
    }
  }

  /**
   * Verify the field is an checkbox field of the given type.
   *
   * @param $field
   * @param $expectedType
   * @throws Exception
   */
  public function assertTextfield($field, $expectedType) {
    $element = $this->getSession()->getPage()->find('css', '[id^=' . $field . '-wrapper]');
    if (NULL == $element || NULL == $element->find('css', 'input[type="' . $expectedType . '"]')) {
      throw new Exception(sprintf("Couldn't find %s of type %s", $field, $expectedType));
    }
  }

  /**
   * Verify the field is an input field of the given type.
   *
   * @param $field
   * @param $expectedType
   * @throws Exception
   */
  public function assertFile($field, $expectedType) {
    $element = $this->getSession()->getPage()->find('css', '[id^=' . $field . '-wrapper]');
    if (NULL == $element || NULL == $element->find('css', 'input[type="file"]')) {
      throw new Exception(sprintf("Couldn't find %s of type %s", $field, $expectedType));
    }
  }
  /**
   * Verify the field is a select list.
   *
   * @param $field
   * @param $expectedType
   * @throws Exception
   */
  public function assertSelect($field, $expectedType) {
    $element = $this->getSession()->getPage()->find('css', '[id^=' . $field . '-wrapper]');
    if (NULL == $element->find('css', 'select.form-select')) {
      throw new Exception(sprintf("Couldn't find %s of type select.", $field));
    }
  }

  /**
   * Verify the field is a paragraph field.
   *
   * @param $field
   * @param $expectedType
   * @throws Exception
   */
  public function assertParagraphs($field, $expectedType = '') {
    $element = $this->getSession()->getPage()->find('css', '[id^=' . $field . '-wrapper]');

    if (NULL == $element || NULL == $element->find('css', '[id^=' . $field . '-add-more-add-more-button-' . $expectedType . ']')) {
      throw new Exception(sprintf("Couldn't find %s of paragraph type %s", $field, $field . '-add-more-add-more-button-' . $expectedType));
    }
  }

  /**
   *
   * @Then the :region region contains the following links:
   *
   * @param String $region
   * @param TableNode $links
   *
   * @throws \Exception
   */
  public function assertRegionLinks($region, $links) {
    $session = $this->getSession();
    $regionObj = $session->getPage()->find('region', $region);
    foreach ($links->getHash() as $row) {
      /*$this->minkContext->assertElementOnPage('#edit-' . $row['field'] );
      $this->assertFieldType('#edit-' . $row['field'], $row['tag'], $row['type']);*/
      $link = $regionObj->findLink($row['link']);
      if (empty($link)) {
        throw new \Exception(sprintf('The link "%s" was not found in the "%s" region on the page %s', $row['link'], $region, $this->getSession()->getCurrentUrl()));
      }
    }
  }

  /**
   * @Then :content_type content can appear in the :menu menu
   */
  public function assertPlaceInMenu($content_type, $menu) {
    // Visit the content type page and open to the menu section.
    // Visit the content type page and open to the menu section.
    $this->getSession()->visit(sprintf('/admin/structure/types/manage/%s#edit-menu', $content_type));
    // See if the box is checked for that menu.
    $selector = sprintf("#edit-menu-options-%s[checked=checked]", $menu);
    $element = $this->getSession()->getPage()->find('css', $selector);
    if (is_null($element)) {
      throw new \Exception(sprintf('Content of type "%s" cannot be placed in the menu "%s"', $content_type, $menu));
    }
  }

  /**
   * Verify that the current user has a particular set of permissions.
   *
   * @Then I should have the :permission permission(s)
   *
   * @param string $permission
   *   The name of a single permission, or a comma-separated list of multiple
   *   permissions.
   *
   * @return void
   *
   * @throws \Exception
   */
  public function hasPermissions($permission)
  {
    if (!$permission) { return; }
    /** @var User $account */
    $account = $this->getLoggedInUser();
    $permissions = array_map('trim', array_filter(explode(',', $permission)));
    list($present, $missing) = $this->matchPermissions($account, $permissions);
    if (!empty($missing)) {
      throw new \Exception(sprintf('User is missing the following permissions: %s', implode(', ', $missing)));
    }
  }

  /**
   * Verify that the current user does not have a particular set of
   * permissions.
   *
   * @Then I should not have the :permission permission(s)
   *
   * @param string $permission
   *   The name of a single permission, or a comma-separated list of multiple
   *   permissions.
   *
   * @return void
   *
   * @throws \Exception
   */
  public function doesNotHavePermissions($permission)
  {
    if (!$permission) { return; }
    /** @var User $account */
    $account = $this->getLoggedInUser();
    $permissions = array_map('trim', array_filter(explode(',', $permission)));
    list($present, $missing) = $this->matchPermissions($account, $permissions);
    if (!empty($present)) {
      throw new \Exception(sprintf('User has the following permissions: %s', implode(', ', $present)));
    }
  }
  /**
   * Check a user's access to a list of permissions.
   *
   * @param \Drupal\user\Entity\User $account
   * @param array $permissions
   *
   * @return array
   *   First index is an array of the given permissions that the user has;
   *   second index is an array of the ones they don't have.
   */
  protected function matchPermissions(User $account, array $permissions)
  {
    $present = [];
    $missing = [];
    foreach ($permissions as $p) {
      if ($account->hasPermission($p)) {
        $present[] = $p;
      }
      else {
        $missing[] = $p;
      }
    }
    return [$present, $missing];
  }
  /**
   * Get the Drupal user entity for the current user.
   *
   * @return User
   * @throws \Exception
   */
  public function getLoggedInUser()
  {
    if (empty($this->drupalContext->user->uid)) {
      throw new \Exception('No user available.');
    }
    /** @var User $account */
    $account = User::load($this->drupalContext->user->uid);
    if (!$account) {
      throw new \Exception(sprintf('User %d could not be loaded.', $this->drupalContext->user->uid));
    }
    return $account;
  }

  /**
   * @Then :content_type content has the correct fields
   */
  public function assertContentTypeFields($content_type) {
    $this->minkContext->visitPath('node/add/' . $content_type);
    // All content types have a title.
    $this->minkContext->assertElementOnPage('#edit-title-0-value');

    $fields = [];

    // Fields for each content type.
    switch ($content_type) {
      case "contact_information":
        $fields = array (
          array (
            'field' => 'field-display-title',
            'tag' => 'input',
            'type' => 'text',
          ),
          array (
            'field' => 'field-ref-address',
            'tag' => 'paragraphs',
            'type' => 'address',
          ),
          array (
            'field' => 'field-ref-fax-number',
            'tag' => 'paragraphs',
            'type' => 'fax-number',
          ),
          array (
            'field' => 'field-ref-links',
            'tag' => 'paragraphs',
            'type' => 'links',
          ),
          array (
            'field' => 'field-ref-phone-number',
            'tag' => 'paragraphs',
            'type' => 'phone-number',
          ),
        );
        break;
      case "guide_page":
        $fields = array (
          array (
            'field' => 'field-guide-page-lede',
            'tag' => 'textarea',
            'type' => '',
          ),
          array (
            'field' => 'field-guide-page-bg-wide',
            'tag' => 'input',
            'type' => 'submit',
          ),
          array (
            'field' => 'field-guide-page-related-guides',
            'tag' => 'input',
            'type' => 'text',
          ),
          array (
            'field' => 'field-guide-page-sections',
            'tag' => 'paragraphs',
            'type' => 'guide-section-3up',
          ),
          array (
            'field' => 'field-guide-page-sections',
            'tag' => 'paragraphs',
            'type' => 'guide-section',
          ),
        );
        break;
    }
    foreach ($fields as $row) {
      // Get all IDs that start with our field name. D8 prints fields
      // differently than D7, so this is necessary.
      $css_selector = '[id^=edit-' . $row['field'] . ']';

      $this->minkContext->assertElementOnPage($css_selector);
      $this->assertFieldType('edit-' . $row['field'], $row['tag'], $row['type']);
    }
  }

  /**
   * @Then :paragraph_type paragraph has the correct fields
   */
  public function assertParagraphTypeFields($paragraph_type) {
    $this->minkContext->visitPath('admin/structure/paragraphs_type/' . $paragraph_type . '/form-display');

    $fields = [];

    // Fields for each content type.
    switch ($paragraph_type) {
      case "address":
        $fields = array (
          array (
            'field' => 'field-address-text',
            'widget' => 'Text area (multiple rows)',
          ),
          array (
            'field' => 'field-label',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-lat-long',
            'widget' => 'Google Map Field default',
          ),
        );
        break;
      case "fax_number":
        $fields = array (
          array (
            'field' => 'field-caption',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-label',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-fax',
            'widget' => 'Telephone number',
          ),
        );
        break;
      case "phone_number":
        $fields = array (
          array (
            'field' => 'field-caption',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-label',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-phone',
            'widget' => 'Telephone number',
          ),
        );
        break;
      case "links":
        $fields = array (
          array (
            'field' => 'field-link-single',
            'widget' => 'Link',
          ),
          array (
            'field' => 'field-label',
            'widget' => 'Textfield',
          ),
        );
        break;
      case "guide_section_3up":
        $fields = array (
          array (
            'field' => 'field-guide-section-alert-text',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-heading-1',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-body-first',
            'widget' => 'Text area (multiple rows)',
          ),
          array (
            'field' => 'field-guide-section-heading-2',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-body-second',
            'widget' => 'Text area (multiple rows)',
          ),
          array (
            'field' => 'field-guide-section-heading-3',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-body-third',
            'widget' => 'Text area (multiple rows)',
          ),
          array (
            'field' => 'field-guide-section-downloads',
            'widget' => 'Inline entity form - Complex',
          ),
          array (
            'field' => 'field-guide-section-links-4',
            'widget' => 'Link',
          ),
          array (
            'field' => 'field-guide-section-link',
            'widget' => 'Link',
          ),
          array (
            'field' => 'field-guide-section-time',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-name',
            'widget' => 'Textfield',
          ),
        );
        break;
      case "guide_section":
        $fields = array (
          array (
            'field' => 'field-guide-section-alert-text',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-downloads',
            'widget' => 'Inline entity form - Complex',
          ),
          array (
            'field' => 'field-guide-section-links-4',
            'widget' => 'Link',
          ),
          array (
            'field' => 'field-guide-section-link',
            'widget' => 'Link',
          ),
          array (
            'field' => 'field-guide-section-stat',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-label',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-time',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-name',
            'widget' => 'Textfield',
          ),
          array (
            'field' => 'field-guide-section-body',
            'widget' => 'Text area (multiple rows)',
          ),
          array (
            'field' => 'field-guide-ref-contacts-3',
            'widget' => 'Autocomplete',
          ),
        );
        break;
    }
    foreach ($fields as $row) {
      $id = 'edit-fields-' . $row['field'] . '-type';
      $this->minkContext->assertElementOnPage("[id^={$id}]");

      $widget = $this->getSession()->getPage()->find('css', "#{$id} option[selected='selected']")->getText();

      if (strtolower($widget) !== strtolower($row['widget'])) {
        throw new Exception(sprintf("Field %s has \"%s\" widget but should have \"%s\".", $row['field'], $widget, $row['widget']));
      }
    }
  }
}
