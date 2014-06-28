<?php 
$priority = isset($_REQUEST["priority"]) ? $_REQUEST["priority"] : "all"; 
$status = isset($_REQUEST["status"]) ? $_REQUEST["status"] : "all";
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "home";
$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;
$lk = isset($_REQUEST["lk"]) ? $_REQUEST["lk"] : 1;

$limit = 20;
$limitstart = ($lk-1) * 20;

$rows = $ToDo->getWorksList($status, $limitstart, $limit, $priority, $q); 

$priorities = $ToDo->getPriorityList();
$statuses = $ToDo->getStatusList();

$return = base64_encode(str_replace("/todo/", "", $_SERVER["REQUEST_URI"]));

?>

<h2><?php echo $ToDo->getTranslation("label.works.all"); ?> <span class="badge"><?php echo $ToDo->getWorksCount($status,$priority, $q); ?></span></h2>

<table class="table table-striped">
	<tr>
		<th>#</th>
		<th><?php echo $ToDo->getTranslation("label.work.name"); ?></th>
		<th><?php echo $ToDo->getTranslation("label.work.desc"); ?></th>
		<th>
			
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
					<?php echo $ToDo->getTranslation("label.work.priority"); ?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li <?php echo ($priority == "all") ? "class='active'" : ""; ?>><a href="?page=<?php echo $page; ?>&priority=all&status=<?php echo $status; ?>">
						<?php echo $ToDo->getTranslation("label.option.all"); ?>
						<span class="badge pull-right"><?php echo $ToDo->getWorksCount($status,"all", $q); ?></span>
					</a></li>
					<li class="divider"></li>
					<?php foreach($priorities as $priorityKey => $priorityName) { ?>
						<?php if($priorityKey != "0") {?>
						<li <?php echo ($priority == $priorityKey) ? "class='active'" : ""; ?>>
							<a href="?page=<?php echo $page; ?>&priority=<?php echo $priorityKey; ?>&status=<?php echo $status; ?>">
								<?php echo $priorityName; ?>
								<span class="badge pull-right"><?php echo $ToDo->getWorksCount($status,$priorityKey, $q); ?></span>
							</a>
						</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
			
		</th>
		<th><?php echo $ToDo->getTranslation("label.work.deadline"); ?></th>
		<th>
			<div class="btn-group">
				<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
					<?php echo $ToDo->getTranslation("label.work.status"); ?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li <?php echo ($status == "all") ? "class='active'" : ""; ?>><a href="?page=<?php echo $page; ?>&priority=<?php echo $priority; ?>&status=all">
						<?php echo $ToDo->getTranslation("label.option.all"); ?>
						<span class="badge pull-right"><?php echo $ToDo->getWorksCount("all",$priority, $q); ?></span>
					</a></li>
					<li class="divider"></li>
					<?php foreach($statuses as $statusKey => $statusName) { ?>
						<?php if($statusKey != "0") {?>
						<li <?php echo ($status == $statusKey) ? "class='active'" : ""; ?>><a href="?page=<?php echo $page; ?>&priority=<?php echo $priority; ?>&status=<?php echo $statusKey; ?>">
							<?php echo $statusName; ?>
							<span class="badge pull-right"><?php echo $ToDo->getWorksCount($statusKey,$priority, $q); ?></span>
						</a></li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		
		</th>
		<th class="col-md-2"><?php echo $ToDo->getTranslation("label.options"); ?></th>
	</tr>
	<?php if(!empty($rows)) { ?>
		<?php foreach($rows as $row) { ?>
		<tr>
			<td><?php echo $row->work_id; ?></td>
			<td><?php echo $row->work_name; ?></td>
			<td><?php echo $row->work_desc; ?></td>
			<td><?php echo $priorities[$row->work_priority]; ?></td>
			<td><?php echo $row->work_deadline; ?></td>
			<td><?php echo $statuses[$row->work_status]; ?></td>
			<td>
				<a href="?page=edit&work=<?php echo $row->work_id; ?>&return=<?php echo $return; ?>" class="btn btn-primary btn-xs"><?php echo $ToDo->getTranslation("label.button.edit"); ?></a>
				<a href="#confirm-delete<?php echo $row->work_id; ?>" data-toggle="modal" data-target="#confirm-delete<?php echo $row->work_id; ?>" class="btn btn-danger btn-xs"><?php echo $ToDo->getTranslation("label.button.delete"); ?></a>
				
				<div class="modal fade" id="confirm-delete<?php echo $row->work_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<?php echo $ToDo->getTranslation("label.delete.confirm.header"); ?>
							</div>
							<div class="modal-body">
								<?php echo $ToDo->getTranslation("error.confirm.delete"); ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $ToDo->getTranslation("label.button.cancel"); ?></button>
								<a href="?page=home&task=delete&workID=<?php echo $row->work_id; ?>&return=<?php echo $return; ?>" class="btn btn-danger danger"><?php echo $ToDo->getTranslation("label.button.delete"); ?></a>
							</div>
						</div>
					</div>
				</div>
			</td>
		</tr>
		<?php } ?>
	<?php } ?>
</table>

<?php 
$hasNext = $ToDo->hasNext($status,$priority, $q);
$hasPrev = $ToDo->hasPrev();
?>
<ul class="pager">
	<?php if($hasPrev) {?>
	<li><a href="?page=<?php echo $page; ?>&priority=<?php echo $priority; ?>&status=<?php echo $status; ?>&lk=<?php echo $lk-1;?>"><span class="glyphicon glyphicon-arrow-left"></span> <?php echo $ToDo->getTranslation("label.page.prev"); ?></a></li>
	<?php } ?>
	<?php if($hasNext) {?>
	<li><a href="?page=<?php echo $page; ?>&priority=<?php echo $priority; ?>&status=<?php echo $status; ?>&lk=<?php echo $lk+1;?>"><?php echo $ToDo->getTranslation("label.page.next"); ?> <span class="glyphicon glyphicon-arrow-right"></span></a></li>
	<?php } ?>
</ul>
