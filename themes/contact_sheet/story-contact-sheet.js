/***
 * StoryServer dashboard theme. change me
 *
 */
var storyServer = (function(core, $) {
  'use strict';

  var opts = {
    lines: 12, // The number of lines to draw
    length: 8, // The length of each line
    width: 4, // The line thickness
    radius: 12, // The radius of the inner circle
    corners: 1, // Corner roundness (0..1)
    rotate: 0, // The rotation offset
    direction: 1, // 1: clockwise, -1: counterclockwise
    color: '#EEE', // #rgb or #rrggbb or array of colors
    speed: 1, // Rounds per second
    trail: 60, // Afterglow percentage
    shadow: false, // Whether to render a shadow
    hwaccel: false, // Whether to use hardware acceleration
    className: 'spinner', // The CSS class to assign to the spinner
    zIndex: 2e9, // The z-index (defaults to 2000000000)
    top: '50%', // Top position relative to parent
    left: '50%' // Left position relative to parent
  };

  var spinner;
  var spinnerTarget;
  var spinnerDelay;

  var startSpinner = function(e) {
    if(!spinnerTarget) {
      spinnerTarget = document.getElementById('spinnerContainer');
    }

    spinnerDelay = setTimeout(function () {
      $(spinnerTarget).show();
      spinner.spin(spinnerTarget);
    }, 300);
  };

  var stopSpinner = function(e) {
    if(spinnerDelay) {
      clearTimeout(spinnerDelay);
    }

    if(spinnerTarget) {
      $(spinnerTarget).hide();
    }

    spinner.stop();
  };

  var htmlEncode = function(value){
    //create a in-memory div, set it's inner text(which jQuery automatically encodes)
    //then grab the encoded contents back out.  The div never exists on the page.
    return $('<div/>').text(value).html();
  };

  var htmlDecode = function(value){
    return $('<div/>').html(value).text();
  };

  var stagingImage = $("<img />");

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Event handlers

  $(document).on('click', '.ss-story-item-link', function(){
    var id = $(this).attr('data-id');
    $.event.trigger({
      type: "showPreview",
      id: id,
      index: core.getIndexForStoryItem(id)
    });
    return false;
  });

  $(stagingImage).on('load', function(){
    var id = $(this).attr('data-id');
    var index = core.getIndexForStoryItem(id);
    $.event.trigger({
      type: "stagingImageLoaded",
      index: index
    });
  });


  $('.ss-story-preview-image').on('load', function(){
    var id = $(this).attr('data-id');
    var index = core.getIndexForStoryItem(id);
    $.event.trigger({
      type: "previewImageLoaded",
      index: index
    });
  });

  $(document).on('click.storyServer', '.ss-story-preview-link', function(){
    var t = this;
    $.event.trigger({
      type: "showStory",
      id: $(t).attr("data-id")
    });
    return false;
  });

  $(document).on('click.storyServer', '.return-to-story', function(){
    $.event.trigger({
      type: "showStory",
      id: $('.ss-story-preview-link').attr("data-id")
    });
    return false;
  });

  $(document).on('click.storyServer', '.ss-story-item-navigation-link-previous', function(){
    navigate("left");
    return false;
  });

  $(document).on('click.storyServer', '.ss-story-item-navigation-link-next', function(){
    navigate("right");
    return false;
  });

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Show methods

  var showStory = function(e) {
    $('.node-type-mt-gallery .field-name-body').show();
    $(".ss-story-items").show();
  };

  var showPreview = function (e) {
    var index = e.index;
    var item = core.data.items.data[index].data;
    $('.node-type-mt-gallery .field-name-body').hide();
    $( "body" ).scrollTop();

    $(stagingImage)
      .attr("data-id",item.image_id)
      .attr('src', item.formats.preview.uri);

    $("#ss-story-preview").show();
  };

  // We've pulled this out, because it should only be shown if the image is loaded.
  // It will be triggered on the stagingImage load event.
  var showPreviewImageAndMetadata = function showPreviewImageAndMetadata(e) {
    var index = e.index;
    var item = core.data.items.data[index].data;

    var imageFrame = $(".ss-story-preview-image-frame");
    if(item.orientation === 'vertical') {
      $(imageFrame).removeClass("horizontal").addClass("vertical");
    } else {
      $(imageFrame).removeClass("vertical").addClass("horizontal");
    }

    $('.ss-story-preview-image')
      .attr("data-id",item.image_id)
      .attr('src', item.formats.preview.uri);

    $(".ss-story-preview-link").attr("data-id",item.image_id);
    $(".ss-story-preview-title").html(item.title);
    $(".ss-story-preview-description").html(item.description || "");
    $(".ss-story-preview-date").html(core.formatDate(item.date));
    $(".ss-story-preview-copyright").html(item.copyright || "");
  };

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Hide methods

  var hideStory = function(e) {
    $(".ss-story-items").hide();
  };

  var hidePreview = function(e) {
    $("#ss-story-preview").hide();
    $(".ss-story-preview-link").attr("data-id", "");
    $(".ss-story-preview-title").html("");
    $(".ss-story-preview-description").html("");
    $(".ss-story-preview-date").html("");
    $(".ss-story-preview-copyright").html("");
    $('.ss-story-preview-image').attr('src', core.appServer + '/preview-placeholder.jpg');
  };

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Key bindings in preview mode.

  var addPreviewKeyBindings = function(e) {

    $(document).on("keyup.storyServer", function(e) {

      switch(e.keyCode) {
        case 37 : // left
          navigate("left");
          break;
        case 39 : // right
          navigate("right");
          break;
        case 71 : // g - back to grid.
          navigate("back");
          break;
      }
    });

    $("#ss-story-preview").on('swipeleft', function(){
      navigate("right");
    })
      .on('swiperight', function(){
        navigate("left");
      });
  };

  // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  // Navigation

  var navigate = function(direction) {
    var id = $(".ss-story-preview-link").attr("data-id");
    var index = core.getIndexForStoryItem(id);
    switch(direction) {
      case "left" :
        if(index > 0) {
          $(".ss-story-item-navigation-link-next").removeClass("inactive");
          if (index === 1) {
            $(".ss-story-item-navigation-link-previous").addClass("inactive");
          } else {
            $(".ss-story-item-navigation-link-previous").removeClass("inactive");
          }
          $.event.trigger({
            type: "previewNavigate",
            index: index - 1,
            id: core.data.items.data[index - 1].data.image_id,
            time: new Date()
          });
        } else {
          $(".ss-story-item-navigation-link-previous").addClass("inactive");
        }
        break;

      case "right" :
        if(index < core.data.items.data.length - 1) {
          $(".ss-story-item-navigation-link-previous").removeClass("inactive");
          if(index === core.data.items.data.length -2) {
            $(".ss-story-item-navigation-link-next").addClass("inactive");
          } else {
            $(".ss-story-item-navigation-link-next").removeClass("inactive");
          }
          $.event.trigger({
            type: "previewNavigate",
            index: index + 1,
            id: core.data.items.data[index + 1].data.image_id,
            time: new Date()
          });
        } else {
          $(".ss-story-item-navigation-link-next").addClass("inactive");
        }
        break;

      case "back" :
        $.event.trigger({
          type: "showStory",
          id: id
        });
        break;
    }
  };

  var removePreviewKeyBindings = function(e) {
    $(document).off("keyup.storyServer");
    $("#ss-story-preview").off('swipeleft').off('swiperight');
  };

  // This is completely independent of any image load event or layout
  // handlers above. It simply caches images.
  var preloadPreviousNext = function(e) {
    var current = e.index;
    if(core.data.items.data.length > 1) {
      if (current > 0) {
        // cache previous
        $("<img />").attr("src", core.data.items.data[current - 1].data.formats.preview.uri);
      }
      if (current < core.data.items.data.length - 1) {
        // cache next
        $("<img />").attr("src", core.data.items.data[current + 1].data.formats.preview.uri);
      }
    }
  };

  var fromNavigation = false;

  var updatePreviewUrl = function(e) {
    if(!e.fromBrowserButton) {
      fromNavigation = true;
      location.hash = "!" + e.id;
    }
  };

  var updateStoryUrl = function(e) {
    if(!e.fromBrowserButton) {
      fromNavigation = true;
      if(e.id) {
        location.hash = e.id;
        document.getElementById(e.id).scrollIntoView();
      }
    }
  };

  /**
   * hashChanged event handler - we have to coordinate with the updateStoryUrl and
   * updatePreviewUrl methods above, and so we include the extra event parameter
   * `fromBrowserButton` to prevent the Url from being updated twice.
   * Unfortunately we can't add extra data to the hashChanged event from the
   * native browser handler, and so we rely on the private variable `fromNavigation`
   *
   * @param e
   */
  var hashChanged = function(e) {
    //console.log(e);
    if (fromNavigation) {
      fromNavigation = false;
    } else {
      if (location.hash.indexOf("!") >= 0) {
        var storyItemId = location.hash.substring(2);
        var index = core.getIndexForStoryItem(storyItemId);
        $.event.trigger({
          type: "showPreview",
          id: storyItemId,
          index: index,
          fromBrowserButton: true
        });
      } else {
        $.event.trigger({
          type: "showStory",
          fromBrowserButton: true
        });
      }
    }
  };

  //var setReturnLink = function(e) {
  //  //console.log(e.type);
  //  if(e.type === "showPreview") {
  //    $('.ss-return_link').attr("href", core.appServer + "/" + core.data.url);
  //  } else {
  //    $('.ss-return_link').attr("href", core.appServer);
  //  }
  //};

  //// Story show-metadata option change
  //$('#show-metadata').change(function() {
  //  var cookieValue;
  //  if($(this).is(":checked")) {
  //    $(".ss-story-item").addClass("show-metadata");
  //    cookieValue = true;
  //  } else {
  //    $(".ss-story-item").removeClass("show-metadata");
  //    cookieValue = false;
  //  }
  //
  //  if ((typeof Cookies !== "undefined") && Cookies.enabled) {
  //    Cookies.set('show-metadata', cookieValue, {expires: Infinity});
  //  }
  //});

  var init = function() {

    //Create spinner object
    spinner = new Spinner(opts);

    $.detectSwipe.preventDefault = false;

    //Define  event handlers
    $(window).on("hashchange", hashChanged);
    $(document).on("showPreview", hideStory);
    $(document).on("showPreview", startSpinner);
    $(document).on("showPreview", showPreview);
    $(document).on("showPreview", updatePreviewUrl);
    //$(document).on("showPreview", setReturnLink);
    $(document).on("showPreview", addPreviewKeyBindings);
    $(document).on("showPreview", preloadPreviousNext);
    $(document).on("stagingImageLoaded", showPreviewImageAndMetadata);
    $(document).on("stagingImageLoaded", stopSpinner);
    //$(document).on("previewImageLoaded", stopSpinner);
    $(document).on("previewNavigate", startSpinner);
    $(document).on("previewNavigate", showPreview);
    $(document).on("previewNavigate", updatePreviewUrl);
    $(document).on("previewNavigate", preloadPreviousNext);
    $(document).on("showStory", stopSpinner);
    $(document).on("showStory", removePreviewKeyBindings);
    $(document).on("showStory", hidePreview);
    $(document).on("showStory", showStory);
    //$(document).on("showStory", setReturnLink);
    $(document).on("showStory", updateStoryUrl);

    if ((typeof Cookies !== "undefined") && Cookies.enabled) {
      var showMetadata = Cookies.get('show-metadata');
      if (showMetadata === 'true') {
        $(".ss-story-item").addClass("show-metadata");
        $('#show-metadata').prop('checked', true);
      } else {
        $(".ss-story-item").removeClass("show-metadata");
        $('#show-metadata').prop('checked', false);
      }
    }

    if (location.hash.indexOf("!") >= 0) {
      var storyItemId = location.hash.substring(2);
      var index = core.getIndexForStoryItem(storyItemId);
      $.event.trigger({
        type: "showPreview",
        id: storyItemId,
        index: index
      });
    } else {
      $.event.trigger({
        type: "showStory"
      });
    }
  };

  core.initializeTheme = function()
  {
    //Load the index page, or initialize the story.
    //Index pages are a special case. They don't require event handlers or
    //initialization since they will only ever link to a story.
    if(core.index === true) {
      showIndex(); //Not currently implemented in the dashboard theme.
    } else {
      init();
    }
  };

  return core;

}(storyServer, jQuery));

(function($) {
  storyServer.init(
    {
      el: 'ss-story',
      appServer: storyServerSettings.appServer,
      json: storyServerSettings.json
    }
  );
}(jQuery));