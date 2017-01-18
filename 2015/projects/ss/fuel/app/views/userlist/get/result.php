<input type="hidden" id="nav_id" name="nav_id" value="get" placeholder="">

<div class="tab-pane active">
	<?= $HMVC_userlist; ?>
</div>


<?= Asset::js("userlist/get.js") ?>