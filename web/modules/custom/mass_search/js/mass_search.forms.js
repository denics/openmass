/**
 * @file
 * Common JS for google custom search HEADER + MOBILE forms (loads globally)
 * Note overlap with customization of search input in mass_search.results.js.
 * Using Mass.gov custom search engine at cse.google.com
 * - api v1 js code
 * - search results page JS in mass_search.results.js
 */
(function () {
  'use strict';

  // ****** Mobile Search button should open mobile menu ******
  var mobileSearchButton = document.querySelector('.ma__header__search .ma__header-search .ma__button-search');

  if (mobileSearchButton !== null) {
    mobileSearchButton.addEventListener('click', function (event) {
      event.preventDefault();
      document.querySelector('body').classList.toggle('show-menu');
    });
  }

  if (window.google) {

    /**
     * load the google custom search module
     * - with english language
     * - with minimalist theme
     */
    google.load('search', '1', {language: 'en', nocss: true});

    /** setOnLoadCallback(callback, @BOOLEAN runOnDomLoad) */
    google.setOnLoadCallback(function () {

      /* search engine id */
      var cx = '010551267445528504028:ivl9x2rf5e8';

      /* placeholders */
      var longPlaceholder = 'What can we help you find?';
      var shortPlaceholder = 'Search...';

      /* set string for message when no results are returned */
      var noResultsString = 'Sorry, we couldn\'t find any results for your query.  Please try searching with different words.';

      /**
       * Set custom search options
       * See: https://developers.google.com/custom-search/docs/js/cselement-reference#opt_options
       */
      var customSearchOptions = {};

      /** autocomplete settings */
      var autoCompleteOptions = {
        maxCompletions: 3
      };
      customSearchOptions['autoCompleteOptions'] = autoCompleteOptions;

      // Utility function to customize search form produced by Google.
      function customizeForm(searchForm, placeholderText, formClassNames, inputClassNames) {
        // Search form classes.
        var i;
        for (i = 0; i < formClassNames.length; i++) {
          searchForm.classList.add(formClassNames[i]);
        }
        // Input box customization.
        var inputField = searchForm.querySelector('input.gsc-input');
        for (i = 0; i < inputClassNames.length; i++) {
          inputField.classList.add(inputClassNames[i]);
        }
        inputField.placeholder = placeholderText;
        // Add label to search input for accessiblity.
        var slabel = document.createElement('label');
        slabel.textContent = 'Search';
        slabel.classList.add('hidden');
        slabel.setAttribute('for', inputField.id);
        var inputCell = searchForm.querySelector('td.gsc-input');
        inputCell.insertBefore(slabel, inputField);
      }

      /** HEADER SEARCH FORM */

      /**
       * Creates an instance of the CustomSearchControl object,
       * which represents a Custom Search Element. Calling this
       * constructor initializes the Custom Search service and UI.
       */
      var headerSearchControl = new google.search.CustomSearchControl(cx, customSearchOptions);

      /**
       * set search results set size
       * options:
       * - integer between 1-20,
       * - google.search.Search. SMALL||LARGE _RESULTSET (google determines usually 8||16)
       * - google.search.Search.FILETERED_CSE_RESULTSET (google determines, up to 10results, 10 pages)
       */
      headerSearchControl.setResultSetSize(20);

      /**
       * Draw header search form with draw options
       * See .draw() at: https://developers.google.com/custom-search/docs/js/cselement-reference#csedrawoptions-el
       */
      var headerOptions = new google.search.DrawOptions();

      /**
       * only draw search form (results are handled in mass_search
       * module route teamplte mass-search.html.twig )
       *
       * set search results route and search term query
       */
      headerOptions.enableSearchboxOnly('/search', 'q');

      /**
       * enable autocomplete (see options above: autoCompleteOptions)
       */
      headerOptions.setAutoComplete(true);

      /**
       * Displays the search form (when it exists, IE not on results page).
       * Calling this method is the final step in activating a Custom Search
       * Element object, and it produces the UI and search containers.
       *
       * .draw(selector, options)
       */
      var headerSearchExists = document.getElementById('cse-header-search-form');

      if (headerSearchExists) {
        headerSearchControl.draw('cse-header-search-form', headerOptions);
        customizeForm(document.querySelector('#cse-header-search-form form'), longPlaceholder, ['ma__form', 'js-header-search-form'], ['ma__header-search__input']);
      }

      // Customize "no results" message
      headerSearchControl.setNoResultsString(noResultsString);

      /** MOBILE SEARCH FORM */

      /**
       * Creates an instance of the CustomSearchControl object,
       * which represents a Custom Search Element. Calling this
       * constructor initializes the Custom Search service and UI.
       */
      var mobileSearchControl = new google.search.CustomSearchControl(cx, customSearchOptions);

      /**
       * set search results set size
       * options:
       * - integer between 1-20,
       * - google.search.Search. SMALL||LARGE _RESULTSET (google determines usually 8||16)
       * - google.search.Search.FILETERED_CSE_RESULTSET (google determines, up to 10results, 10 pages)
       */
      mobileSearchControl.setResultSetSize(20);

      /**
       * Draw mobile search form with draw options
       * See .draw() at: https://developers.google.com/custom-search/docs/js/cselement-reference#csedrawoptions-el
       */
      var mobileOptions = new google.search.DrawOptions();

      /**
       * only draw search form (results are handled in mass_search
       * module route teamplte mass-search.html.twig )
       *
       * set search results route and search term query
       */
      mobileOptions.enableSearchboxOnly('/search', 'q');

      /**
       * enable autocomplete (see options above: autoCompleteOptions)
       */

      mobileOptions.setAutoComplete(true);

      /**
       * Displays the search form. Calling this method is the final step
       * in activating a Custom Search Element object, and it produces the
       * UI and search containers.
       *
       * .draw(selector, options)
       */
      mobileSearchControl.draw('cse-search-form-mobile', mobileOptions);

      // Customize "no results" message
      mobileSearchControl.setNoResultsString(noResultsString);

      // Customize the form
      customizeForm(document.querySelector('#cse-search-form-mobile form'), shortPlaceholder, ['ma__form'], []);

        /**
       * Creates an instance of the CustomSearchControl object,
       * which represents a Custom Search Element. Calling this
       * constructor initializes the Custom Search service and UI.
       */
      var searchBandSearchControl = new google.search.CustomSearchControl(cx, customSearchOptions);

      /**
       * set search results set size
       * options:
       * - integer between 1-20,
       * - google.search.Search. SMALL||LARGE _RESULTSET (google determines usually 8||16)
       * - google.search.Search.FILETERED_CSE_RESULTSET (google determines, up to 10results, 10 pages)
       */
      searchBandSearchControl.setResultSetSize(20);

      /**
       * Draw header search form with draw options
       * See .draw() at: https://developers.google.com/custom-search/docs/js/cselement-reference#csedrawoptions-el
       */
      var searchBandOptions = new google.search.DrawOptions();

      /**
       * only draw search form (results are handled in mass_search
       * module route teamplte mass-search.html.twig )
       *
       * set search results route and search term query
       */
      searchBandOptions.enableSearchboxOnly('/search', 'q');

      /**
       * enable autocomplete (see options above: autoCompleteOptions)
       */
      searchBandOptions.setAutoComplete(true);

      /**
       * Displays the search form (when it exists, IE not on results page).
       * Calling this method is the final step in activating a Custom Search
       * Element object, and it produces the UI and search containers.
       *
       * .draw(selector, options)
       */
      var searchBandSearchExists = document.getElementById('cse-search-band-search-form');

      if (searchBandSearchExists) {
        searchBandSearchControl.draw('cse-search-band-search-form', searchBandOptions);
      }

      // Customize "no results" message
      searchBandSearchControl.setNoResultsString(noResultsString);

      // Remove class on form for search banner <form>.
      var bannerSearchForm = document.querySelector('.ma__search-banner form.gsc-search-box');
      if (bannerSearchForm !== null) {
        // Add standard customizations to form: hidden label and placeholder.
        customizeForm(bannerSearchForm, shortPlaceholder, [], []);
        bannerSearchForm.classList.remove('gsc-search-box');
      }

      // Add classes to form parent for search banner <form>.
      var bannerSearchFormParent = document.querySelector('#cse-search-band-search-form');
      if (bannerSearchFormParent !== null) {
        bannerSearchFormParent.classList.add('ma__search-banner__form');
      }

      // Add classes to <input>.
      var bannerInputField = document.querySelector('.ma__search-banner td.gsc-input');
      if (bannerInputField !== null) {
        bannerInputField.classList.add('ma__search-banner__input');
        var bannerTextInput = document.querySelector('.ma__search-banner td.gsc-input input');
        bannerTextInput.classList.remove('gsc-input');
      }

      // Add classes to <button>.
      var bannerSubmitField = document.querySelector('.ma__search-banner td.gsc-search-button');
      if (bannerSubmitField !== null) {
        var bannerSubmitInput = document.querySelector('.ma__search-banner td.gsc-search-button input');
        bannerSubmitField.classList.add('ma__search-banner__button');
        bannerSubmitField.classList.remove('gsc-search-button');
        bannerSubmitInput.placeholder = 'Search...';
      }

    }, true);
  }
}());
