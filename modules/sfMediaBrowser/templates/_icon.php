<div class="icon">
  <?php echo link_to(image_tag($icon), $file, array('target' => '_blank')) ?>
  <div class="name"><?php echo $file_name ?></div>
  <div class="action">
    <span class="size"><?php echo $size ?> Kb</span>
    <?php if($dimensions): ?>
    <span class="dimensions"><?php echo $dimensions[0] ?>x<?php echo $dimensions[1] ?></span>
    <?php endif ?>
    <?php echo link_to('delete', 'sf_media_browser_file_delete', array('file' => urlencode($file)), array('class' => 'delete', 'title' => sprintf('Delete file "%s"', $file_name))) ?>
  </div>
</div>