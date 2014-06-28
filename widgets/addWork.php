<h2><?php echo $ToDo->getTranslation("label.addWork"); ?></h2>
<form class="form-horizontal" role="form" action="index.php" method="post">
	<input type="hidden" class="form-control" name="task" id="task" value="add">
	
	<div class="form-group">
		<label for="inputName" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.name"); ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="nimetus" id="inputName" placeholder="<?php echo $ToDo->getTranslation("label.work.name"); ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="inputDesc" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.desc"); ?></label>
		<div class="col-sm-10">
			<textarea class="form-control" name="kirjeldus" id="inputDesc" placeholder="<?php echo $ToDo->getTranslation("label.work.desc"); ?>"></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="inputPriority" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.priority"); ?></label>
		<div class="col-sm-2">
			<select name="priority" class="form-control" id="inputPriority">
				<option value="0"><?php echo $ToDo->getTranslation("label.option.choose"); ?></option>
				<option value="1"><?php echo $ToDo->getTranslation("label.option.normal"); ?></option>
				<option value="2"><?php echo $ToDo->getTranslation("label.option.important"); ?></option>
				<option value="3"><?php echo $ToDo->getTranslation("label.option.critical"); ?></option>
			</select>
		</div>
		
		<label for="inputPriority" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.deadline"); ?></label>
		<div class="col-sm-2">
			<input type="datetime" class="form-control" name="deadline" id="inputDeadline">
		</div>
		
		<label for="inputStatus" class="col-sm-2 control-label"><?php echo $ToDo->getTranslation("label.work.status"); ?></label>
		<div class="col-sm-2">
			<select name="status" class="form-control" id="inputStatus">
				<option value="0"><?php echo $ToDo->getTranslation("label.option.choose"); ?></option>
				<option value="1"><?php echo $ToDo->getTranslation("label.option.new"); ?></option>
				<option value="2"><?php echo $ToDo->getTranslation("label.option.open"); ?></option>
				<option value="3"><?php echo $ToDo->getTranslation("label.option.completed"); ?></option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-success"><?php echo $ToDo->getTranslation("label.button.save"); ?></button>
		</div>
	</div>
</form>