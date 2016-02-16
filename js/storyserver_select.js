(function($) {
  Drupal.behaviors.storyServerSelect = {
    attach: function (context, settings) {
      if(context && context.doctype) {
        var host = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '');
        var $select = $(".storyserver-id");
        var $name = $(".storyserver-name");

        var $reset = $('.storyserver-remove-story');
        $reset.on('click', function(event){
          $select.empty();
          $select.val('').trigger("change");
          $select.trigger("select2:select");
          event.preventDefault();
        });

        function formatNames(data) {
          if (data.loading) return data.text;
          return data.name;
        }

        function formatNameSelection(data) {
          return data.name || data.text;
        }

        $select.select2({
            placeholder: "Select a story...",
            ajax: {
              url: host + '/storyserver/story_names',
              dataType: 'json',
              delay: 250,
              data: function (params) {
                return {
                  storyname: params.term, // search term - needed to change from q to storyname because of Drupal collision.
                  page: params.page
                };
              },
              processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                  results: data.items,
                  pagination: {
                    more: (params.page * 30) < data.total
                  }
                };
              },
              cache: false
            },
            escapeMarkup: function (markup) {
              return markup;
            }, // let our custom formatter work
            minimumInputLength: 2,
            templateResult: formatNames, // omitted for brevity, see the source of this page
            templateSelection: formatNameSelection // omitted for brevity, see the source of this page
          }
        ).on("select2:select", function (e) {
            if (typeof e.params !== "undefined" && typeof e.params.data !== "undefined") {
              $name.val(e.params.data.name);
            } else {
              $name.val('');
            }
          });
      }
    }
  };
})(jQuery);