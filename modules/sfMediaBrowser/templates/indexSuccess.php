<?php use_stylesheet('/sfMediaBrowserPlugin/css/list.css') ?>
<?php use_javascript('/sfMediaBrowserPlugin/js/index.js') ?>
<?php use_helper('I18N') ?>
<script type="text/javascript">
  delete_msg = "<?php echo __('Are you sure you want to delete this item ?') ?>";
</script>

<div id="sf_media_browser_forms">
  <fieldset id="sf_media_browser_upload">
    <legend><?php echo __('Upload a file') ?></legend>
    <form action="<?php echo url_for('sf_media_browser_file_create') ?>" method="post" enctype="multipart/form-data">
      <?php echo $upload_form ?>
      <input type="submit" class="submit" value="<?php echo __('Save') ?>" />
    </form>
  </fieldset>

  <fieldset id="sf_media_browser_mkdir">
    <legend><?php echo __('Create a new directory') ?></legend>
    <form action="<?php echo url_for('sf_media_browser_dir_create') ?>" method="post">
      <?php echo $dir_form ?>
      <input type="submit" class="submit" value="<?php echo __('Create') ?>" />
    </form>
  </fieldset>
  <div class="clear"></div>
</div>


<?php if($sf_user->hasFlash('error')): ?>
  <div class="error">
  <?php if($sf_user->getFlash('error') == 'directory.delete'): ?>
    <?php echo __('The directory could not be deleted.') ?>
  <?php endif ?>
  </div>
<?php elseif($sf_user->hasFlash('notice')): ?>
  <div class="notice">
  <?php if($sf_user->getFlash('notice') == 'directory.delete'): ?>
    <?php echo __('The directory was succesfully deleted.') ?>
  <?php endif ?>
  </div>
<?php endif ?>



<h2><?php echo __('Current dir') ?> : <?php echo $relative_dir ?></h2>


<ul id="sf_media_browser_list">

  <?php if($parent_dir): ?>
  <li class="up">
    <?php echo link_to(image_tag('/sfMediaBrowserPlugin/images/icons/up.png'), $current_route, array_merge($sf_data->getRaw('current_params'), array('dir' => $parent_dir))) ?>
  </li>
  <?php endif ?>

<?php foreach($dirs as $dir): ?>
  <li class="folder">
    <?php echo link_to(image_tag('/sfMediaBrowserPlugin/images/icons/folder.png'), $current_route, array_merge($sf_data->getRaw('current_params'), array('dir' => urlencode($relative_dir.'/'.$dir)))) ?>
    <div class="name"><?php echo $dir ?></div>
    <div class="action"><?php echo link_to('delete', 'sf_media_browser_dir_delete', array('sf_method' => 'delete', 'directory' => urlencode($relative_dir.'/'.$dir)), array('class' => 'delete', 'title' => sprintf('Delete folder "%s"', $dir))) ?></div>
  </li>
<?php endforeach ?>

<?php foreach($files as $file): ?>
  <li class="file">
    <?php include_component('sfMediaBrowser', 'icon', array('dir' => $real_dir, 'filename' => $file)) ?>
  </li>
<?php endforeach ?>
</ul>