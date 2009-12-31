<div class="icon">
  <?php echo link_to(image_tag($file->getIcon()), $file->getUrl(), array('target' => '_blank')) ?>
</div>
<label class="name"><?php echo $file->getName() ?></label>
<div class="action">
  <span class="size"><?php echo $file->getSize() ?> Kb</span>
  <?php if($file->isImage()): ?>
  <span class="dimensions"><?php echo $file->getWidth() ?>x<?php echo $file->getHeight() ?></span>
  <?php endif ?>
  <?php echo link_to('delete', 'sf_media_browser_file_delete', array('file' => urlencode($file->getUrl())),
                array('class' => 'delete', 'title' => sprintf('Delete file "%s"', $file->getName()))
            ) ?>
</div>
