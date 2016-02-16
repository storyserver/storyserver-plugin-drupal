<div id="story" xmlns="http://www.w3.org/1999/html">

  <div id="ss-story">

    <div class="ss-story-items">
      <?php foreach ($story->data->items->data as $item): ?>
      <div class="ss-story-item" id="<?=$item->data->image_id?>">
        <a class="ss-story-item-link" data-id="<?=$item->data->image_id?>"
           href="someserver.com/stories/<?=$story->data->id ?>#!/<?=$item->data->image_id?>">
          <img class="ss-story-item-thumbnail" width="350" height="350" src="<?=$item->data->formats->thumbnail->uri?>" title="<?=$item->data->title?>" alt="<?=$item->data->title?>">
        </a>
      </div>
      <?php endforeach ?>
    </div>

    <div id="ss-story-preview" class="ss-story-preview">
      <div id="ss-story-preview-title-nav" class="title-navigation">
        <h1 class="ss-story-preview-title"></h1>
        <ul class="ss-story-item-navigation">
          <li class="tooltipped tooltipped-w" aria-label="Return to Thumbnails 'G'">
            <a class="return-to-story" href="#">
              <div class="grid-icon"></div>
            </a>
          </li>
          <li class="ss-story-item-navigation-previous">
            <a class="ss-story-item-navigation-link-previous" href="#"><span class="previous"></span></a>
          </li>
          <li class="ss-story-item-navigation-next">
            <a class="ss-story-item-navigation-link-next" href="#"><span class="next"></span></a>
          </li>
        </ul>
      </div>
      <div id="ss-story-preview-image-and-metadata" class="ss-story-preview-image-and-metadata">
        <div class="ss-story-preview-image-frame horizontal">
          <div id="spinnerContainer"></div>
          <a class="ss-story-preview-link" id="" href="">
            <img class="ss-story-preview-image" data-id="" src="">
          </a>
        </div>
        <div class="ss-story-preview-metadata">
          <span class="ss-story-preview-description"></span>
          <span class="ss-story-preview-authors"></span><br>
          <span class="ss-story-preview-date"></span>
          <span class="ss-story-preview-copyright"></span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function($) {
    storyServer.init(
      {
        el: 'ss-story',
        appServer: '<?=$appServer?>',
        json: '<?=$safeJson?>'
      }
    );
  }(jQuery));
</script>