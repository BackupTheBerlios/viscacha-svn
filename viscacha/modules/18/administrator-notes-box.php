$notes = file_get_contents('admin/data/notes.php');
?>
<div class="border">
 <h3><img id="img_admin_menu_qnotes" name="collapse" src="admin/html/images/plus.gif" alt=""> Administrator Notes</h3>
 <div class="boxbody" id="part_admin_menu_qnotes" style="margin: auto; text-align: center;">
  <form action="admin.php?action=index&job=save_notes&location=<?php echo urlencode('admin.php?action=frames&job=menu'); ?>" method="post" style="margin: 0px;">
   <textarea name="notes" rows="5" cols="30" style="width: 156px; text-align: left;"><?php echo $notes; ?></textarea>
   <input type="submit" value="Save" />
  </form>
 </div>
</div>
<?php