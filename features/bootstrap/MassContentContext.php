<?php
/**
 * @file
 *
 */

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Context\MinkContext;
use Drupal\DrupalExtension\Context\DrupalContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Defines content features specific to Mass.gov.
 */
class MassContentContext extends RawDrupalContext {

  /**
   * @var Object[] Array of action nodes keyed on 'title'.
   */
  private $action = [];

  /**
   * @var Object[] Array of subtopic nodes keyed on 'title'.
   */
  private $subtopic = [];

  /**
   * @var Object[] Array of topic nodes keyed on 'title'.
   */
  private $topic = [];

  /**
   * @var Object[] Array of section landing nodes keyed on 'title'.
   */
  private $section_landing = [];

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
   * Create all default content.
   *
   * @Given default test content exists
   */
  public function createDefaultTestContent() {
    $vocabularies = [
      'icons' => $this->defaultIcons(),
    ];
    foreach ($vocabularies as $vocabulary => $terms) {
      foreach ($terms as $term) {
        $term += ['vocabulary_machine_name' => $vocabulary];
        $this->drupalContext->termCreate((object) $term);
      }
    }

    $types = [
      'section_landing' => $this->defaultSectionLandings(),
      'topic' => $this->defaultTopics(),
      'subtopic' => $this->defaultSubtopics(),
      'action' => $this->defaultActions(),
    ];

    foreach ($types as $type => $nodes) {
      foreach ($nodes as $node) {
        $node += ['type' => $type];
        $this->createNode($node);
      }
    }

    // Now that all the data structures exist, we need to go back through and
    // add content dependencies.
    $this->updateNodes('subtopic');
    $this->updateNodes('topic');
  }

  /**
   * Get a list of fields that have content dependencies.
   *
   * @return Array
   *   An array of fields with content dependencies keyed by node type.
   */
  public function relationshipFields() {
    return [
      'action' => [],
      'subtopic' => [
        'field_featured_content',
      ],
      'topic' => [
        'field_common_content',
      ],
      'section_landing' => [],
    ];
  }

  /**
   * Create default test actions.
   *
   * The Action Parent field is required, thus Subtopics must be created before
   * these actions can be instantiated.
   *
   * @Given test actions exist
   */
  public function defaultActions() {
    return [
      [
        'title' => 'Behat Test: Get a State Park Pass',
        'field_action_parent' => 'Behat Test: State Parks & Recreation',
      ],
      [
        'title' => 'Behat Test: Reserve a Campsite (external)',
        'field_external-url' => 'http://www.google.com',
        'field_action_parent' => 'Behat Test: Nature & Outdoor Activities',
      ],
      [
        'title' => 'Behat Test: Find Scenic Viewing Areas',
        'field_action_parent' => 'Behat Test: Nature & Outdoor Activities',
      ],
      [
        'title' => 'Behat Test: Find Horseback Riding Trails (external)',
        'field_external-url' => 'http://www.google.com',
        'field_action_parent' => 'Behat Test: Nature & Outdoor Activities',
      ],
      [
        'title' => 'Behat Test: Download a Trail Map',
        'field_action_parent' => 'Behat Test: Nature & Outdoor Activities',
      ],
      [
        'title' => 'Behat Test: Get a Boating License',
        'field_action_parent' => 'Behat Test: Recreational Licenses & Permits',
      ],
      [
        'title' => 'Behat Test: Get a Fishing License (external)',
        'field_external-url' => 'http://www.google.com',
        'field_action_parent' => 'Behat Test: Recreational Licenses & Permits',
      ],
      [
        'title' => 'Behat Test: Get a Hunting License',
        'field_action_parent' => 'Behat Test: Recreational Licenses & Permits',
      ],
      [
        'title' => 'Behat Test: Post a Job',
        'field_action_parent' => 'Behat Test: Search Jobs',
      ],
      [
        'title' => 'Behat Test: Find a State Job',
        'field_action_parent' => 'Behat Test: Search Jobs',
      ],
    ];
  }

  /**
   * Create default icons terms for use in tests of
   * content types: Section Landing, Topic
   * paragraphs: Action Step.
   *
   * Note: the fields that reference this vocabulary are required, so
   * these terms need to be created before the nodes/paragraphs.
   *
   * @Given icons vocabulary exists
   */
  public function defaultIcons() {
    return [
      [
        'name' => 'Behat Test: Family',
        'field_sprite_name' => 'family',
      ],
      [
        'name' => 'Behat Test: Apple',
        'field_sprite_name' => 'apple',
      ],
      [
        'name' => 'Behat Test: Camping',
        'field_sprite_name' => 'camping',
      ]
    ];
  }

  /**
   * Create default test subtopics.
   *
   * Note: The "field_topic_parent" field is required, so Topics will need to be
   * created before this content can be stubbed out.
   *
   * Note: The "field_featured_content" field is optional, so content relationships
   * will need to be updated after all the stub data is created.
   *
   * @Given test subtopics exist
   */
  public function defaultSubtopics() {
    return [
      [
        'title' => 'Behat Test: Get a State Park Pass',
        'status' => 1,
        'moderation_state' => 'published',
        'field_topic_parent' => 'Behat Test: State Parks & Recreation',
        'field_lede' => 'The lede text for Nature & Outdoor Activities',
        'field_description' => 'The description text for Nature & Outdoor Activities',
        'field_featured_content' => implode(', ', [
          'Behat Test: Find a State Park',
          'Behat Test: Download a Trail Map',
        ]),
        'field_agency_links' => implode(', ', [
          'MassParks - http://www.google.com',
          'Department of Fish - http://www.google.com',
        ]),
        'field_topic_callout_links' => implode(', ', [
          'Camping - http://mass.local/subtopic/nature-outdoor-activities?filter=Camping',
          'Hiking - http://mass.local/subtopic/nature-outdoor-activities?filter=Hiking',
          'Biking - http://mass.local/subtopic/nature-outdoor-activities?filter=Biking',
        ]),
      ],
      [
        'title' => 'Behat Test: Nature & Outdoor Activities',
        'status' => 1,
        'moderation_state' => 'published',
        'field_topic_parent' => 'Behat Test: State Parks & Recreation',
        'field_lede' => 'The lede text for Nature & Outdoor Activities',
        'field_description' => 'The description text for Nature & Outdoor Activities',
        'field_featured_content' => implode(', ', [
          'Behat Test: Find a State Park',
          'Behat Test: Download a Trail Map',
        ]),
        'field_agency_links' => implode(', ', [
          'MassParks - http://www.google.com',
          'Department of Fish - http://www.google.com',
        ]),
        'field_topic_callout_links' => implode(', ', [
          'Camping - http://mass.local/subtopic/nature-outdoor-activities?filter=Camping',
          'Hiking - http://mass.local/subtopic/nature-outdoor-activities?filter=Hiking',
          'Biking - http://mass.local/subtopic/nature-outdoor-activities?filter=Biking',
        ]),
      ],
      [
        'title' => 'Behat Test: Recreational Licenses & Permits',
        'status' => 1,
        'moderation_state' => 'published',
        'field_topic_parent' => 'Behat Test: State Parks & Recreation',
        'field_lede' => 'The lede text for Recreational Licenses & Permits',
        'field_description' => 'The description text for Recreational Licenses & Permits',
        'field_featured_content' => implode(', ', [
          'Behat Test: Get a Boating License',
        ]),
        'field_agency_links' => implode(', ', [
          'Department of Agricultural Resources - http://www.google.com',
        ]),
      ],
      [
        'title' => 'Behat Test: Search Jobs',
        'status' => 1,
        'moderation_state' => 'published',
        'field_topic_parent' => 'Behat Test: Finding a Job',
        'field_lede' => 'The lede text for Search Jobs',
        'field_description' => 'The description text for Search Jobs',
        'field_featured_content' => implode(', ', [
          'Behat Test: Find a State Job',
        ]),
        'field_agency_links' => implode(', ', [
          'MassCareers - http://www.google.com',
          'MassIT - http://www.google.com'
        ]),
        'field_topic_callout_links' => implode(', ', [
          'Education - http://mass.local/subtopic/search-jobs?filter=Education',
          'Public Sector - http://mass.local/subtopic/search-jobs?filter=Public Sector',
          'Public Safety - http://mass.local/subtopic/search-jobs?filter=Public Safety',
        ]),
      ],
    ];
  }

  /**
   * Create default test topics.
   *
   * The Common Content field here is optional, and the actions may or may not
   * exist, so they should be created in a followup.
   *
   * @Given test topics exist
   */
  public function defaultTopics() {
    return [
      [
        'title' => 'Behat Test: State Parks & Recreation',
        'status' => 1,
        'moderation_state' => 'published',
        'field_section' => 'Behat Test: Visiting & Exploring',
        'field_lede' => 'Lede text for State Parks & Rec.',
        'field_icon_term' => 'Behat Test: Camping',
        'field_common_content' => implode(', ', [
          'Behat Test: Get a State Park Pass',
          'Behat Test: Download a Trail Map',
        ]),
      ],
      [
        'title' => 'Behat Test: Finding a Job',
        'status' => 1,
        'moderation_state' => 'published',
        'field_section' => 'Behat Test: Working',
        'field_lede' => 'Lede text for Finding a Job',
        'field_icon_term' => 'Behat Test: Apple',
        'field_common_content' => implode(', ', [
          'Behat Test: Post a Job',
        ]),
      ],
    ];
  }

  /**
   * Create default test section landings.
   *
   * @Given test section landings exist
   */
  public function defaultSectionLandings() {
    return [
      [
        'title' => 'Behat Test: Visiting & Exploring',
        'field_icon_term' => 'Behat Test: Family',
      ],
      [
        'title' => 'Behat Test: Working',
        'field_icon_term' => 'Behat Test: Apple',
      ],
    ];
  }

  /**
   * Create a node in Drupal from an array.
   *
   * @param array $node
   * @return object
   */
  protected function createNode(Array $node) {
    $type = $node['type'];
    // If there is not a title, set one.
    $node['title'] = ($node['title']) ?: $this->randomTitle($type);

    // Strip out fields that cannot be created because of content dependencies.
    if (!empty($this->relationshipFields()[$type])) {
      foreach($this->relationshipFields()[$type] as $field) {
        unset($node[$field]);
      }
    }

    $node = $this->drupalContext->nodeCreate((object) $node);

    // Track the node as a local array entry for ease of reference.
    if (is_array($this->{$type})) {
      $this->{$type}[$node->title] = $node;
    }

    return $node;
  }

  /**
   * Creates a paragraph of the given type, identified by the given key.
   *
   * @Given I add a :type paragraph in the :key field with values:
   */
  public function iAddAParagraph($type, $key, TableNode $fields) {
    $paragraph_values = ['type' => $type];
    foreach ($fields->getRowsHash() as $field => $value) {
      $paragraph_values[$field] = $value;
    }
    $paragraph = Paragraph::create($paragraph_values);
    $paragraph->save();

    if (empty($this->nodes)) {
      throw new \Exception('No pages have been created.');
    }
    $latest_node = end($this->nodes);

    $node = Node::load($latest_node->nid);
    $node->set($key, [
      [
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraph->getRevisionId(),
      ]
    ]);
    $node->save();
  }

  /**
   * Creates a node of the given type.
   *
   * @Given I create a :type content:
   */
  public function iCreateAContent($type, TableNode $fields) {
    $node = (object) array(
      'type' => $type,
    );
    foreach ($fields->getRowsHash() as $field => $value) {
      $node->{$field} = $value;
    }

    $this->nodeCreate($node);
  }

  /**
   * Visits the most recently-created node.
   *
   * @When I visit the newest page
   */
  public function iVisitTheNewestPage() {
    if (empty($this->nodes)) {
      throw new \Exception('No pages have been created.');
    }
    $latest_node = end($this->nodes);
    $this->minkContext->visitPath('/node/' . $latest_node->nid);
  }

  /**
   * Update reference fields on nodes of a given type.
   *
   * @param string $type
   *   The type of nodes to update.
   */
  protected function updateNodes($type) {
    if (empty($this->{$type})) {
      return;
    }

    if (empty($this->relationshipFields()[$type])) {
      return;
    }

    foreach ($this->{$type} as $title => $old_node) {
      $node = Node::load($old_node->nid);
      foreach ($this->relationshipFields()[$type] as $field) {
        if ($node->hasField($field)) {
          $refs = $this->getDefaultValue($type, $node->title->value, $field);
          foreach ($refs as $ref) {
            $node->{$field}->appendItem($ref->nid);
          }
        }
      }
      $node->save();
    }
  }

  /**
   * Get node info for an entity reference field for a given node.
   *
   * @param $type
   *   The type that has an entity reference.
   * @param $title
   *   The title of the node.
   * @param $field
   *   The entity reference node content will be saved to.
   * @return array|void
   *   The base node info for the entity reference.
   */
  private function getDefaultValue($type, $title, $field) {

    $action = $this->defaultActions();
    $subtopic = $this->defaultSubtopics();
    $topic = $this->defaultTopics();
    $section_landing = $this->defaultSectionLandings();

    $lookups = [];

    foreach ($$type as $default_node) {
      if ($default_node['title'] == $title) {
        $lookups = $default_node[$field];
      }
    }

    $lookups = explode(', ', $lookups);

    if (empty($lookups)) {
      return;
    }

    foreach ($lookups as $lookup_title) {
      foreach (array_keys($this->relationshipFields()) as $lookup_type) {
        if (array_key_exists($lookup_title, $this->{$lookup_type})) {
          $return[] = $this->{$lookup_type}[$lookup_title];
        }
      }
    }

    return $return;
  }
  /** end of really, really bad ideas... */

  /**
   * Visit a given test node.
   *
   * @When I visit the test :type :title
   *
   * @param $type The test content type
   * @param $title The test node title
   *
   * @throws Exception
   */
  public function vistsTestNode($type, $title) {
    if (empty($type)) {
      throw new \Exception('The node type must be provided.');
    }

    if (empty($title)) {
      throw new \Exception('The node title must be provided.');
    }

    if (!isset($this->{$type}[$title])) {
      throw new \Exception('Cannot load the specified node.');
    }

    $node = $this->{$type}[$title];
    $this->minkContext->visitPath('node/' . $node->nid);
  }

  /**
   * Generate a random title for a node type.
   *
   * @param $type
   * @return string
   */
  public function randomTitle($type, $prefix = 'Behat Test -')
  {
    $random = strtolower($this->drupalContext->getRandom()->name());
    $type = str_replace('_', ' ', $type);

    return ucwords("{$prefix} {$type} {$random}");
  }

}
