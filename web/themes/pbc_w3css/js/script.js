/**
 * @file
 * This file is to add any custom js for the drupal8 w3css subtheme.
 */

(function ($) {

  'use strict';

  Drupal.behaviors.customBehavior = {
      // Perform jQuery as normal in here.
    attach: function(context,settings) {
      // Show the mobile menu on click horizontal.
      $(context)
        .find('#main-navigation-h .left-sidebar-nav')
        .once('#main-navigation-h .left-sidebar-nav')
        .on(
          'click', function () {
            if ($('#left-sidebar').is(':hidden')) {
              document.getElementById('left-sidebar')
                .setAttribute('style','display: block !important');
              var shls = document.getElementById('show-hide-left-sidebar');
              shls.className = shls.className.replace('left-sidebar-nav','left-sidebar-nav-displayed');
            }
            else {
              document.getElementById('left-sidebar')
                .setAttribute('style', 'display: none !important');
              var shls = document.getElementById('show-hide-left-sidebar');
              shls.className = shls.className.replace('left-sidebar-nav-displayed','left-sidebar-nav');
            }
          }
        );

    }
  };

})(jQuery);
