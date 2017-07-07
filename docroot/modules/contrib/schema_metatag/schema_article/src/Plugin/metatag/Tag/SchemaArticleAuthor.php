<?php

namespace Drupal\schema_article\Plugin\metatag\Tag;

use \Drupal\schema_metatag\Plugin\metatag\Tag\SchemaPersonOrgBase;

/**
 * Provides a plugin for the 'author' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_article_author",
 *   label = @Translation("author"),
 *   description = @Translation("Author of the article."),
 *   name = "author",
 *   group = "schema_article",
 *   weight = 5,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaArticleAuthor extends SchemaPersonOrgBase {

  /**
   * Generate a form element for this meta tag.
   *
   * We need multiple values, so create a tree of values and
   * stored the serialized value as a string.
   */
  public function form(array $element = []) {
    $form = parent::form($element);
    $form['name']['#attribute']['placeholder'] = '[node:author:display-name]';
    $form['url']['#attributes']['placeholder'] = '[node:author:url]';
    return $form;
  }

}
