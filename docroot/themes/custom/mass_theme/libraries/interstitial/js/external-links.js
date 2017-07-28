(function ($) {
  'use strict';
  $('a[href]').click(function (e) {
    var href = $(this).attr('href');
    if (window.localStorage.noInterstitial && JSON.parse(window.localStorage.noInterstitial)) {
      return;
    }
    // regex to pass for a classic mass.gov link (portal and non-portal)
    var classicHrefRgx = /^((http(s)?:\/\/)?(www.)?mass.gov)\/(ago|anf|auditor|berkshireda|capeda|childadvocate|cjc|comptroller|courts|dor|dppc|edu|eea|elders|eohhs|eopss|essexda|essexsheriff|ethics|governor|hdc|hed|ig|informedma|lwd|massit|massworkforce|mcad|mdaa|mova|msa|mtrs|ocabr|osc|pca|perac|portal|recovery|srbtf|treasury|veterans|women|abcc|agr|bb|cgly|ClientsSecurityBoard|daplymouth|export|legis|norfolkda|opendata|better|obcbbo|smartplan)(\/.*)?$/i;

    if (href.match(classicHrefRgx)) {
      e.preventDefault();
      window.location = window.location.origin + '/leaving-pilot?continueURL=' + this.href;
    }
  });
})(jQuery);
