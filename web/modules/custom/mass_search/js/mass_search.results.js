/**
 * @file
 * Extends Drupal object with mass custom js objects
 *
 * Loads google custom search results page FORM + RESULTS (loads once)
 * Note overlap with customization of search input in mass_search.forms.js.
 * Using Mass.gov custom search engine at cse.google.com
 * - api v1 js code
 * - header and mobile nav search forms js in mass_search.forms.js
 *
 * Improves accessibility (a11y) to google custom search dynamic content with Drupal.announce().
 */

(function (Drupal) {
  'use strict';

  /**
   * Extends Drupal object with mass custom js object
   * @namespace
   */
  Drupal.mass = Drupal.mass || {};

  // Contains custom methods related to mass_search.
  Drupal.mass.search = Drupal.mass.search || {};

  // Contains custom methods to improve mass_search accessibility (a11y).
  Drupal.mass.search.a11y = Drupal.mass.search.a11y || {};

  // Contains custom generic helper methods
  Drupal.mass.helpers = Drupal.mass.helpers || {};

  /* set no results message for announcement and visual display */
  var noResultsString = 'Sorry, we couldn\'t find any results for your query.  Please try searching with different words.';

  // Confirm that we have access to the google object which is returned by
  // mass_search js external library google.com/jsapi
  if (window.google) {

    /**
     * Loads the google custom search module
     * - with english language
     * - with minimalist theme
     *
     * @return google.search module
     */
    google.load('search', '1', {language: 'en', style: google.loader.themes.MINIMALIST});
  }

  /**
   * Attaches the custom search execution js to the custom search route.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Loads search form, executes search, loads search results, and accessibility helpers.
   */
  Drupal.behaviors.massSearchResults = {};
  Drupal.behaviors.massSearchResults.attach = function (context) {
    function debounce(fn, delay) {
      var timer = null;
      return function () {
        var context = this;
        var args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          fn.apply(context, args);
        }, delay);
      };
    }

    function sizeSuggestedBlocks() {
      var $promos = jQuery('.gsc-webResult.gsc-result.gsc-promotion');
      $promos.height('auto');
      var heights = $promos.map(function (i, el) {
        return jQuery(el).height();
      });
      var maxHeight = Math.max.apply(null, heights);
      $promos.height(maxHeight);
    }

    // If google.search module is loaded
    if (window.google.search) {

      // add class to <body> so we can target autocomplete table in css for this page
      document.body.setAttribute('class', 'search-results-page');



      // Match heights of suggested blocks
      jQuery(window).resize(debounce(sizeSuggestedBlocks, 500));
      sizeSuggestedBlocks();

      /**
       * setOnLoadCallback(callback, @BOOLEAN runOnDomLoad)
       */
      google.setOnLoadCallback(function () {

        // Define google custom search engine id.
        var cx = '010551267445528504028:ivl9x2rf5e8';

        // Set custom search options.
        var customSearchOptions = {};

        // Disable search results orderby functionality.
        customSearchOptions['enableOrderBy'] = false;

        // Set autocomplete settings.
        var autoCompleteOptions = {
          maxCompletions: 3
        };
        customSearchOptions['autoCompleteOptions'] = autoCompleteOptions;

        /**
         * Creates an instance of the CustomSearchControl object,
         * which represents a Custom Search Element. Calling this
         * constructor initializes the Custom Search service and UI.
         */
        var resultsPageSearchControl = new google.search.CustomSearchControl(cx, customSearchOptions);

        /**
         * set search results set size
         * options:
         * - integer between 1-20,
         * - google.search.Search. SMALL||LARGE _RESULTSET (google determines usually 8||16)
         * - google.search.Search.FILETERED_CSE_RESULTSET (google determines, up to 10results, 10 pages)
         */
        resultsPageSearchControl.setResultSetSize(20);

        // Specify the callback method to call upon completion of the search (defined below).
        resultsPageSearchControl.setSearchCompleteCallback(null, Drupal.mass.search.a11y.announceSearchComplete);

        // Draws search form with draw options.
        var resultsOptions = new google.search.DrawOptions();

        // Set search form element.
        // See markup in mass_search module templates/mass_search.html.twig
        resultsOptions.setSearchFormRoot('cse-search-results-form');

        // Enable autocomplete (see options above: autoCompleteOptions).
        resultsOptions.setAutoComplete(true);

        // Display the search form + search results.
        // .draw(selector, options)
        resultsPageSearchControl.draw('cse-search-results', resultsOptions);

        // Customize "no results" message
        resultsPageSearchControl.setNoResultsString(noResultsString);

        // Customize the form.
        var searchForm = document.querySelector('#cse-search-results-form form');
        if (searchForm !== null) {
          searchForm.classList.add('ma__form');
          // Input box customization.
          var inputField = searchForm.querySelector('input.gsc-input');
          inputField.placeholder = 'What can we help you find?';
          // Add label to search input for accessiblity.
          var slabel = document.createElement('label');
          slabel.textContent = 'Search';
          slabel.classList.add('hidden');
          slabel.setAttribute('for', inputField.id);
          var inputCell = searchForm.querySelector('td.gsc-input');
          inputCell.insertBefore(slabel, inputField);
        }

          // Get array of the url querystring params.
        var urlParams = Drupal.mass.helpers.parseParamsFromUrl();

        // Define param for the search query.
        var queryParamName = 'q';

        // Execute the search
        if (urlParams[queryParamName]) {
          resultsPageSearchControl.execute(urlParams[queryParamName]);
        }

        // Set initial sizing of promoted blocks.
        // This is awful but it's seems that we don't have a handle on a decent callback.
        setTimeout(function () { sizeSuggestedBlocks(); }, 2000);

      }, true); // google.search onLoadCallback
    } // endif window.google.search

  }; // Drupal.behaviors.massSearchResults.attach

  /**
   * Drupal.mass.search.a11y.announceSearchComplete
   * Announces the status of search results page dynamic content on completed search:
   * 1. Confirms new content has loaded
   * 2a. No results message (if there are not results returned)
   * 2b. Contents of search results (if there are results returned):
   * -- 3. Number of promoted results (if any)
   * -- 4. Page # of search results
   *
   * Prepends both the first promoted result (if any) [5.] and regular result [6.] with visually hidden heading landmarks.
   *
   * Invoked by setSearchCompleteCallback above.
   */
  Drupal.mass.search.a11y.announceSearchComplete = function () {
    // Query dom for search results container.
    // Used below as context for finding child nodes and inserting heading landmarks
    var searchResults = document.querySelector('div.gsc-results');

    // Begin composing announcement.
    var announcement = 'New content loaded.  '; // 1.

    // Query dom for no results container.
    var noResults = searchResults.querySelectorAll('div.gs-no-results-result').length;

    if (noResults) {
      announcement += noResultsString; // 2a.
    }
    else { // There are results
      announcement += 'Now showing '; // 2b.

      // Query dom for promoted search results + determine quantity.
      var promotions = searchResults.querySelectorAll('div.gsc-promotion');
      var numPromotions = (promotions.length) ? promotions.length : 0;

      // If there are promoted results.
      if (numPromotions) {
        // Append promotions message content to announcement.
        announcement += numPromotions + ' suggested links and '; // 3.

        // Prepend first promoted result with visually hidden <h2> landmark.
        var promotedResultsHeading = document.createElement('h2');
        promotedResultsHeading.setAttribute('class', 'ma__search-heading');
        promotedResultsHeading.textContent = 'Suggested Links';
        searchResults.insertBefore(promotedResultsHeading, promotions[0]); // 5.
      }

      // Determine what results page we are one
      var currentPageNode = searchResults.querySelector('div.gsc-cursor-page.gsc-cursor-current-page');
      var currentPage = currentPageNode.textContent;

      currentPageNode.setAttribute('aria-selected', 'true');

      // Add current pagination link text for screen readers.

      // Create text which will describe current result page link.
      var currentPageDescribedBy = document.createElement('div');
      currentPageDescribedBy.setAttribute('id', 'ma-current-search-page');
      currentPageDescribedBy.setAttribute('class', 'visually-hidden');
      currentPageDescribedBy.textContent = 'current results page';
      searchResults.appendChild(currentPageDescribedBy);
      currentPageNode.setAttribute('aria-describedby', 'ma-current-search-page');

      // Create heading which describes search results page navigation group.
      var resultsPageLinksDescribedBy = document.createElement('h2');
      resultsPageLinksDescribedBy.setAttribute('id', 'ma-search-results-navigation');
      resultsPageLinksDescribedBy.setAttribute('class', 'visually-hidden');
      resultsPageLinksDescribedBy.textContent = 'Search Results Navigation';

      // Insert search results page navigation heading before navigation links group.
      var searchResultsNavigation = searchResults.querySelector('div.gsc-cursor');
      var firstNavigationDiv = searchResultsNavigation.firstElementChild;
      searchResultsNavigation.insertBefore(resultsPageLinksDescribedBy, firstNavigationDiv);
      searchResultsNavigation.setAttribute('aria-describedby', 'ma-search-results-navigation');

      // Append regular results message content, with the context of the current page, to announcement.
      announcement += 'search results page ' + currentPage; // 4.

      // Query dom for first regular search result container.
      var regularResults = searchResults.querySelector('div.gsc-webResult.gsc-result:not(.gsc-promotion)');
      // Prepend regular results with visually hidden <h2> landmark.
      var regularResultsHeading = document.createElement('h2');
      regularResultsHeading.setAttribute('class', 'ma__search-heading');
      regularResultsHeading.textContent = 'Search Results';
      searchResults.insertBefore(regularResultsHeading, regularResults); // 6.
    }

    // Make post-search execution announcement.
    Drupal.announce(Drupal.t(announcement), 'polite');
  };

  /**
   * Parses URL parameters
   * @return {object} params - object of parameters from querystring
   */
  Drupal.mass.helpers.parseParamsFromUrl = function () {
    var params = {};
    var parts = window.location.search.substr(1).split('&');
    for (var i = 0; i < parts.length; i++) {
      var keyValuePair = parts[i].split('=');
      var key = decodeURIComponent(keyValuePair[0]);
      params[key] = keyValuePair[1] ?
          decodeURIComponent(keyValuePair[1].replace(/\+/g, ' ')) :
          keyValuePair[1];
    }
    return params;
  };
})(Drupal);
