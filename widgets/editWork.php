<?php 
$workID = isset($_REQUEST["work"]) ? $_REQUEST["work"] : null;
if(empty($workID)) {
	echo "<meta http-equiv='refresh' content='0;url=index.php?msg=".$ToDo->getTranslation("error.work.notselected")."'>";
	exit;
}
$work = $ToDo->getWorkData($workID); 

$priorities = $ToDo->getPriorityList();
$statuses = $ToDo->getStatusList();
?>
<h2><?php echo $ToDo->getTranslation("label.editWork"); ?></h2>
<form class="form-horizontal" role="form" action="index.php" method="post">
	<?php if(isset($_REQUEST["return"])) { ?>
		<input type="hidden" class="form-control" name="return" id="return" value="<?php echo $_REQUEST["return"]; ?>">
	<?php } ?>
	<input type="hidden" class="form-control" name="workID" id="workID" value="<?php echo $workID; ?>">
	<input type="hidden" class="form-control" name="task" id="task" value="edit">
	
	<div class="form-group">
		<label class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.id"); ?></label>
		<div class="col-sm-4">
			<p class="form-control-static"><?php echo $work->work_id; ?></p>
		</div>
		
		<label class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.created_date"); ?></label>
		<div class="col-sm-4">
			<p class="form-control-static"><?php echo $work->work_created; ?></p>
		</div>
	</div>
	
	<div class="form-group">
		<label for="inputName" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.name"); ?></label>
		<div class="col-sm-10">
			<input type="text" value="<?php echo $work->work_name; ?>" class="form-control" name="nimetus" id="inputName" placeholder="<?php echo $ToDo->getTranslation("label.work.name"); ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="inputDesc" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.desc"); ?></label>
		<div class="col-sm-10">
			<textarea class="form-control" name="kirjeldus" id="inputDesc" rows="10" placeholder="<?php echo $ToDo->getTranslation("label.work.desc"); ?>"><?php echo $work->work_desc; ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="inputPriority" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.priority"); ?></label>
		<div class="col-sm-2">
			<select name="priority" class="form-control" id="inputPriority">
				<?php foreach($priorities as $priorityKey => $priorityName) { ?>
					<option value="<?php echo $priorityKey; ?>" <?php echo ($priorityKey == $work->work_priority) ? "selected" : ""; ?>><?php echo $priorityName; ?></option>
				<?php } ?>
			</select>
		</div>
		
		<label for="inputPriority" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.deadline"); ?></label>
		<div class="col-sm-2">
			<input type="datetime" value="<?php echo $work->work_deadline; ?>" class="form-control" name="deadline" id="inputDeadline">
		</div>
		
		<label for="inputStatus" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.status"); ?></label>
		<div class="col-sm-2">
			<select name="status" class="form-control" id="inputStatus">
				<?php foreach($statuses as $statusKey => $statusName) { ?>
					<option value="<?php echo $statusKey; ?>" <?php echo ($statusKey == $work->work_status) ? "selected" : ""; ?>><?php echo $statusName; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-success"><?php echo $ToDo->getTranslation("label.button.save"); ?></button>
		</div>
	</div>
</form>