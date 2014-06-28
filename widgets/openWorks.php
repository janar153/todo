<?php $rows = $ToDo->getWorksList(2, 0, 3); ?>
<h2><?php echo $ToDo->getTranslation("label.works.open"); ?> <span class="badge"><?php echo $ToDo->getWorksCount(2); ?></span></h2>
<p>
	<div class="list-group">
		<?php if(!empty($rows)) { ?>
			<?php foreach($rows as $row) { ?>
				<a href="?page=edit&work=<?php echo $row->work_id; ?>" class="list-group-item">
					<span class="badge"><?php echo $row->work_deadline; ?></span>
					<h4 class="list-group-item-heading"><?php echo $row->work_name = (strlen($row->work_name) > 50) ? substr($row->work_name,0,47).'...' : $row->work_name; ?></h4>
					<p class="list-group-item-text"><?php echo $row->work_desc = (strlen($row->work_desc) > 100) ? substr($row->work_desc,0,97).'...' : $row->work_desc; ?></p>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
</p>
<p><a class="btn btn-default" href="?page=list&status=2" role="button"><?php echo $ToDo->getTranslation("label.button.viewall"); ?></a></p>