<div class="icon">
  <?php echo link_to(image_tag($file->getIcon()), $file->getWebPath(), array('target' => '_blank')) ?>
  <div class="name"><?php echo $file->getName() ?></div>
  <div class="action">
    <span class="size"><?php echo $file->getSize() ?> Kb</span>
    <?php if($file->isImage()): ?>
    <span class="dimensions"><?php echo $file->getWidth() ?>x<?php echo $file->getHeight() ?></span>
    <?php endif ?>
    <span class="dimensions">
      <?php //echo link_to('edit', 'sf_media_browser_edit', array('file' => urlencode($file->getFile()))) ?>
    </span>
    <?php echo link_to('delete', 'sf_media_browser_file_delete', array('file' => urlencode($file->getWebPath())),
                  array('class' => 'delete', 'title' => sprintf('Delete file "%s"', $file->getName()))
              ) ?>
  </div>
</div>
