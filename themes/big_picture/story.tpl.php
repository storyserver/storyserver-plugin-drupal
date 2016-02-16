<div id="ss-story">
  <ul class="ss-story-items">
  <?php foreach ($story->data->items->data as $item): ?>
    <li class="ss-story-item">
      <img src="<?=$item->data->formats->medium->uri?>" sizes="(max-width: 40em) 50vw, 100vw"
           srcset="<?=$item->data->formats->small->uri?> 640w,
           <?=$item->data->formats->medium->uri?> 960w,
          <?=$item->data->formats->large->uri?> 1200w" alt="<?=$item->data->title?>">
      <div class="ss-story-item-metadata">
        <p>
          <span class="title"><?=$item->data->title?></span> (<span class="date"><?= date('D, d M Y', strtotime($item->data->date));?></span>):
          <?=$item->data->description?>
        </p>
      </div>
    </li>
  <?php endforeach ?>
  </ul>
</div>