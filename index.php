<!DOCTYPE html>
<html lang="et">
<?php
session_start();
include("libraries/config.php");
$configLoader = new configLoader();

$_SESSION["default_lang"] = "en";
if(isset($_REQUEST["lang"])) {
	$lang = $_SESSION["lang"] = $_REQUEST["lang"];
} elseif(isset($_SESSION["lang"])) {
	$lang = $_SESSION["lang"];
} else {
	$lang = $_SESSION["default_lang"];
}

include("libraries/main.php");
$ToDo = new ToDoMain($configLoader->config["DB"]["host"], $configLoader->config["DB"]["user"], $configLoader->config["DB"]["pass"], $configLoader->config["DB"]["database"]);
$ToDo->setLang($lang);

$msg = isset($_REQUEST["msg"]) ? $_REQUEST["msg"] : null;
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "home";

if(isset($_REQUEST["task"])) {
	if($_REQUEST["task"] == "add") {
		$addedID = $ToDo->addWork();
		
		if(!is_numeric($addedID)) {
			$msg = $ToDo->getTranslation("error.work.add");
			$_SESSION["msg"] = $msg;
			if(isset($_REQUEST["return"])) {
				$return = base64_decode($_REQUEST["return"]);
				header('Location: '.$return);
				die;
			}
		}
	} elseif($_REQUEST["task"] == "edit") {
		$workID = $ToDo->editWork();
		
		$msg = $ToDo->getTranslation("error.work.edited");
		$_SESSION["msg"] = $msg;
		
		if(isset($_REQUEST["return"])) {
			$return = base64_decode($_REQUEST["return"]);
			header('Location: '.$return);
			die;
		}
		
	} elseif($_REQUEST["task"] == "delete") {
		$workID = $ToDo->deleteWork();
		
		$msg = $ToDo->getTranslation("error.work.deleted");
		$_SESSION["msg"] = $msg;
		
		if(isset($_REQUEST["return"])) {
			$return = base64_decode($_REQUEST["return"]);
			header('Location: '.$return);
			die;
		}
	}
}

$q = isset($_REQUEST["q"]) ? $_REQUEST["q"] : null;

$langURL = "index.php?page=".$page;
if(isset($_REQUEST["status"])) { 	$langURL .= "&status=".$_REQUEST["status"]; }
if(isset($_REQUEST["priority"])) { 	$langURL .= "&priority=".$_REQUEST["priority"]; }
if(isset($_REQUEST["work"])) { 	$langURL .= "&work=".$_REQUEST["work"]; }
if(isset($_REQUEST["task"])) { 	$langURL .= "&task=".$_REQUEST["task"]; }
if(isset($_REQUEST["q"])) { 	$langURL .= "&q=".$_REQUEST["q"]; }
if(isset($_REQUEST["return"])) { 	$langURL .= "&return=".$_REQUEST["return"]; }

?>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="ico/favicon.ico">

    <title><?php echo $configLoader->config["SITE"]["name"]; ?> <?php echo $ToDo->getTranslation("label.ver"); ?> <?php echo $configLoader->config["SITE"]["version"]; ?> </title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/jumbotron.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="./"><?php echo $configLoader->config["SITE"]["name"]; ?> <?php echo $ToDo->getTranslation("label.ver"); ?> <?php echo $configLoader->config["SITE"]["version"]; ?> </a>
			</div>
			<div class="navbar-collapse collapse">

				<ul class="nav navbar-nav navbar-right">
					<li <?php echo ($lang == "et") ? "class='active'" : ""; ?>><a href="<?php echo $langURL; ?>&lang=et"><?php echo $ToDo->getTranslation("label.lang.et"); ?></a></li>
					<li <?php echo ($lang == "en") ? "class='active'" : ""; ?>><a href="<?php echo $langURL; ?>&lang=en"><?php echo $ToDo->getTranslation("label.lang.en"); ?></a></li>
				</ul>				
				
				<form class="navbar-form navbar-right" role="form">
					<input type="hidden" name="page" value="list">
					<div class="form-group">
						<input type="text" name="q" value="<?php echo !empty($q) ? $q : null; ?>" placeholder="<?php echo $ToDo->getTranslation("label.search.keyword"); ?>" class="form-control">
					</div>
					<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-search"></span> <?php echo $ToDo->getTranslation("label.button.search"); ?></button>
				</form>
			</div><!--/.navbar-collapse -->
		</div>
    </div>

    <div class="container">
		<?php if(!empty($_SESSION["msg"])) { ?>
			<div class="row" id="infoRow">
				<br>
				<div class="col-md-12">
					<div class="alert alert-danger">
						<?php 
						echo $_SESSION["msg"]; 
						// empty message
						$_SESSION["msg"] = null;
						?>
					</div>
				</div>
			</div>
			<script>
			setTimeout(function() {
				$('#infoRow').fadeOut('slow');
			}, 3000);
			</script>
		<?php } ?>
		
		<?php include("pages/".$page.".php"); ?>
		
		<hr>

		<footer>
			<p>&copy; <?php echo $configLoader->config["SITE"]["author"]." ".date("Y"); ?> </p>
		</footer>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
