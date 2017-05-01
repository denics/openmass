(function ($) {
  'use strict';

  // ---- begin formstack reset ------ //
  // see: https://github.com/base2arthur/bootstrap-the-formstack

  // Update select elements to match Mayflower
  $('select.fsField').each(function (el, i) {

    var $this = $(this);
    var $label = $this.siblings('label[for=" + $this.attr(\'id\') + "]');

    // add class to label
    $label.addClass('ma__select-box__label');

    // add class to field div container
    // $(this).parent().addClass('ma__feedback-form__type');

    // wrap label + input with section.ma__select-box.js-dropdown
    $this.add($label).wrapAll('<section class="ma__select-box js-dropdown"/>');

    // 1. add classes to the select element
    // 2. wrap select element with div.ma__select-box__field
    $(this).addClass('ma__select-box__select js-dropdown-select')
    .wrap('<div class="ma__select-box__field" />');

    // get the text value of the first option (used below)
    var firstOptionText = $(this).find('option').first().text();

    // create the div.ma__select-box__link + child spans,
    // this is what is rendered visually in place of the select
    $(this).after('<div class="ma__select-box__link">'
      + '<span class="js-dropdown-link">'
      + firstOptionText
      + '</span><span class="ma__select-box__icon"></span></div>');

  });

  $(document).on('change', 'select.js-dropdown-select', function (e) {
    $('.ma__select-box__link .js-dropdown-link').text(e.target.value);
  });


  // Map radio form elements to what Mayflower is expecting
  $('.fsOptionLabel > input[type="radio"]').each(function (el, i) {
    $(this)
      .parent().wrap('<span class="ma__input-radio" />');

    // move radio input out from parent label,
    // necessary for custom :before pseudoelement in scss
    $(this)
      .insertBefore($(this).parent());
  });

  // HOMEPAGE -- //
  $('#fsSubmit2504022').addClass('ma__feedback-form__controls');
  $('.fsPage textarea').attr('rows', 5);

  // MULTI FORM-- //
  $('#fs-go-back').css('display', 'none').attr('aria-hidden', 'true');
  $('.fsSubmitButton').addClass('ma__button ma__button--small');

  $('.fsForm').prepend('<hr align="left">');

  // MULTI FORM-- //
  $('#fs-go-back').css('display', 'none').attr('aria-hidden', 'true');
  $('.fsSubmitButton').addClass('ma__button ma__button--small');

})(jQuery);
