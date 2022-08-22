/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
define(["require", "exports", "jquery", "TYPO3/CMS/Backend/FormEngine", "TYPO3/CMS/Core/Contrib/jquery.autocomplete"], function (e, t, a, n) {
  "use strict";
  return function () {
    function e(inputField,options) {
      var t = this;
      this.options = options;
      this.inputField = inputField;
      a(function () {
        t.initialize(inputField)
      });
    }

    return e.prototype.initialize = function (e) {
      var t = a(e).closest(".t3-form-suggest-container"),
        p = parseInt(this.options.minchars, 10),
        g = TYPO3.settings.ajaxUrls.icon_suggest,
        x = {
          'collections': a(e).data('collections'),
          'signature': this.options.signature
        },
        r = a(e).attr('id');
        a(e).val(a(e).data('icon-class'));
      a(e).autocomplete({
        serviceUrl: g,
        params: x,
        type: "POST",
        paramName: "value",
        dataType: "json",
        minChars: p,
        groupBy: "typeLabel",
        containerClass: "autocomplete-results",
        appendTo: t,
        forceFixPosition: !1,
        preserveInput: !0,
        showNoSuggestionNotice: !0,
        noSuggestionNotice: '<div class="autocomplete-info">No results</div>',
        minLength: p,
        preventBadQueries: !1,
        transformResult: function (e) {
          return {
            suggestions: e.map(function (e) {
              return {value: e.label, data: e}
            })
          }
        },
        formatResult: function (e) {
          return a("<div>").append(a('<a class="autocomplete-suggestion-link" href="#"><span style="display: inline-block;width: 27px;margin-right: 7px;margin-left: -24px;vertical-align: middle;">' + e.data.svg + '</span>' + e.data.label + "</a></div>").attr({
            "data-value": e.data.value,
            "data-class": e.data.class,
            "data-svg": e.data.svg
          })).html()
        },
        onSearchComplete: function () {
          t.addClass("open")
        },
        beforeRender: function (e) {
          e.attr("style", "");
          t.addClass("open")
        },
        onHide: function () {
          t.removeClass("open")
        },
        onSelect: function () {
          var i, o;
          i = t.find(".autocomplete-selected a"),
            o = "", a(e).val(i.data("class"));

            a(e).closest(".t3js-formengine-field-item").find('input[type="hidden"]').val(i.data("value"));
          a(e).closest(".t3js-formengine-field-item").find('.input-group-addon').html(i.data("svg"));


            n.Validation.markFieldAsChanged(a(e));
        }
      })
    }, e
  }()
});