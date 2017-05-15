/**
 * @file
 * Extends Drupal object with mass custom js objects
 *
 * Loads google custom search results page FORM + RESULTS (loads once)
 * Using Mass.gov custom search engine at cse.google.com
 * - api v2 js code
 * - header and mobile nav search forms js in mass_search.forms.js
 *
 * Improves accessibility (a11y) to google custom search dynamic content with Drupal.announce().
 */

(function (Drupal) {
  'use strict';

  // ****** Mobile Search button should open mobile menu ******
  var mobileSearchButton = document.querySelector('.ma__header__search .ma__header-search .ma__button-search');

  if (mobileSearchButton !== null) {
    mobileSearchButton.addEventListener('click', function (event) {
      event.preventDefault();
      document.querySelector('body').classList.toggle('show-menu');
    });
  }
  // Adds labels to all GCSE search inputs on the page
  function adjustStandardGCSEsearchForAccessibility() {
    var inputContainer = document.querySelectorAll('.ma__search-banner__form td.gsc-input');
    var inputLabel;
    var actualInput;
    for (var i = 0, j = inputContainer.length; i < j; i++) {
      inputLabel = document.createElement('label');
      inputLabel.classList.add('visually-hidden');
      inputLabel.textContent = 'Search';
      actualInput = inputContainer[i].querySelector('input');
      // Add placeholder
      actualInput.setAttribute('placeholder', 'Search...');

      // Add label to search input
      inputLabel.setAttribute('for', actualInput.id);
      inputContainer[i].insertBefore(inputLabel, actualInput);
    }
  }

  function adjustMarkupToSearchPage() {
    var suggestedLinksHeadline = document.createElement('h2');
    var searchResultsHeadline = document.createElement('h2');

    suggestedLinksHeadline.textContent = 'Suggested Links';
    suggestedLinksHeadline.classList.add('ma__search-heading');
    searchResultsHeadline.textContent = 'Search Results';
    searchResultsHeadline.classList.add('ma__search-heading');
  }

  function adjustMarkupOnHomepageSearchBox() {
    var homeSearchContainer = document.querySelector('.ma__search-banner__container .cse-search-band-search-form');
    var inputContainer = homeSearchContainer.querySelector('.gsc-input');
    var actualInput = inputContainer.querySelector('input');
    var searchButton = homeSearchContainer.querySelector('.gsc-search-button');

    // Add/remove classes to have less google styles and more mayflower styles.
    inputContainer.classList.add('ma__search-banner__input');
    actualInput.classList.remove('gsc-input');
    searchButton.classList.add('ma__search-banner__button');
    searchButton.classList.remove('gsc-search-button');
  }

  function onGCSEload() {
    adjustStandardGCSEsearchForAccessibility();

    if (document.body.classList.contains('search-results-page')) {
      adjustMarkupToSearchPage();
    }
    else if (document.body.classList.contains('is-front')) {
      adjustMarkupOnHomepageSearchBox();
    }
    else {
      // do nothing
    }
  }

  window.__gcse = {parsetags: 'onload', callback: onGCSEload};


  var cx = '010551267445528504028:ivl9x2rf5e8';

  var gcse = document.createElement('script');
  gcse.type = 'text/javascript';
  gcse.async = true;
  // got from jsfiddle off of cse site, http://jsfiddle.net/devnook/yJDWv/4/
  gcse.src = (document.location.protocol === 'https:' ? 'https:' : 'http:') +
      '//www.google.com/cse/cse.js?cx=' + cx;
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(gcse, s);

})(Drupal);



