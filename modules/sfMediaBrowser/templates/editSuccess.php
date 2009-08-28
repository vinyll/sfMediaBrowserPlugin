<h1>Editing file "<?php echo $file ?>"</h1>

<?php echo link_to('Back to list', 'sf_media_browser') ?>

<form method="post" action="<?php echo url_for('sf_media_browser_rename', array('type' => 'file')) ?>">
  <?php echo $rename_form ?>
  <input type="submit" value="Save" />
</form>
