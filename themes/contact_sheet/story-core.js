/***
 * StoryServer core module.
 *
 */
var storyServer = (function () {
  'use strict';

  var core = {};

  core.data = {};
  core.el = {};
  core.index = false;
  core.storyServer = "";
  core.appServer = "";
  core.keyId = "";
  core.formats = {};

  core.initializeTheme = function() {};

  core.init = function (options) {
    var t = this;
    t.data = JSON.parse(options.json).data;
    if(options.el) {
      t.el = document.getElementById(options.el);
    } else {
      t.el = document.getElementById('ss-story');
    }
    t.index       = options.index;
    t.appServer   = options.appServer;
    t.keyId       = options.keyId;

    t.initializeTheme();
  };

  //TODO: Create lookup/index object
  core.getIndexForStoryItem = function(storyItemId) {
    var t = this;
    for (var i = 0; i < t.data.items.data.length; i++) {
      if (t.data.items.data[i].data.image_id === storyItemId) {
        return i;
      }
    }
  };

  //Note: we want the time of day, and date the picture was taken,
  //not the time in the browsers local timezone.
  core.formatDate = function(ISO8601) {
    //Remove milliseconds and offset after hh:mm:ss
    if(ISO8601) {
      var date = ISO8601.replace(/(.*\d{2}:\d{2}:\d{2}).*/, '$1');
      try {
        date = new Date(date).toUTCString();
        //Remove seconds and the GMT or UTC suffix
        date = date.replace(/(.*):\d{2}\sGMT|UTC/, '$1');
      } catch (e) {
        date = "Bad date format";
      }
      return date;
    } else {
      return '';
    }
  };

  // Helper method to trim the caption/description of an image in
  // thumbnail view.
  core.truncate = function truncate(str, len, words) {
    var tooLong = str.length > len;
    var s_ = tooLong ? str.substr(0, len) : str;
    if (words && tooLong) {
      var index = s_.lastIndexOf(' ');
      if (index !== -1) {
        s_ = s_.substr(0, index);
      }
    }
    return tooLong ? s_ + ' &hellip;' : s_;
  };

  return core;

}());