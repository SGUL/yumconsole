<?php
$servers = file("serverslist", FILE_IGNORE_NEW_LINES);
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!--link rel="icon" href="../../favicon.ico"-->

    <title>Yum Updates</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme.css" rel="stylesheet">
    <link rel="stylesheet" href="jquery-ui.css">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!--script src="bootstrap/assets/js/ie-emulation-modes-warning.js"></script-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body role="document">


<script src="jquery.min.js">
<?php
//$output=file_get_contents("./output/$server.txt");
//$x = split(",", $output);
//$return = $x[0];
//$updates = rtrim($x[1]);
//$number = $x[2];
?>


</script>
    <div class="container theme-showcase" role="main">

     

    

      <div class="page-header">
        <h1>Yum Console</h1>
	<!--button id="refresh-all-button" type="button" class="btn btn-sm btn-success refresh-all-button">Update All</button-->
	<button type="submit" value="Refresh" name="Test" class="glyphicon glyphicon-refresh refresh-all-button"></span></button>
	
      </div>
      <div class="row">

	<?php 	
		$i = 1;
		echo '<div class="col-sm-4">';
		foreach ($servers as $server) {
		?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $server;?></h3>
				</div>
				<div class="panel-body">
					<?php
						$output=file_get_contents("./output/$server.txt");
						$x = split(",", $output);
						$return = $x[0];
						$updates = rtrim($x[1]);
						$number = $x[2];
						$details = false;
						if ($return==0 && $updates=="NO") {
							$button = "success";
							$text = "Up to date";
						} else if ($return==0 && $updates=="YES") {
							$button = "danger";
							$text = "Update ($number)";
							$details = true;
						} else if ($return==255) {
							$button = "warning";
							$text = "Check connection";
						} else {
                                                        $button = "default";
                                                        $text = "Error";
                                                }
					?>
					<button id="upd-<?php echo $server;?>" name="upd-<?php echo $server;?>" class="btn-server btn btn-sm btn-<?php echo $button;?>"><?php echo $text;?></button>
					<button id="refresh-<?php echo $server;?>" name="refresh-<?php echo $server;?>" class="refresh-button glyphicon glyphicon-refresh"></button>
					<button style="<?php if (!$details) echo 'display:none'; else 'display:inline';?>" id="details-<?php echo $server;?>" name="details-<?php echo $server;?>" class="details-button glyphicon glyphicon-list"></button>
					<span id="loading-<?php echo $server;?>" style='display:none'><img src="loading.gif"></img></span>
				</div>
			</div>
		<?php
			if ($i % 2 == 0) { echo '</div class="col-sm-4"><div class="col-sm-4">'; }
			$i++;
		}
		echo '</div class="col-sm-4">';
	?>
      </div>
	


    </div> <!-- /container -->

<div id="dialog" title="Yum Updates"></div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="jquery.min.js"></script>
    <script src="jquery-ui.js"></script>
    <script>
	function jq( myid ) {
 
    return "#" + myid.replace( /(:|\.|\[|\]|,)/g, "\\$1" );
 
}

	$(".details-button").click(function(event){
		event.preventDefault();
		var server = $(event.target).attr('id');
		server=server.substring(8);
		
		$.ajax({
                         type: 'GET',
                         url: 'get-details.php',
                         data: 'server='+server,
                         server: server,
			 success: function(data) {
				$(function() {
				    $("#dialog").html(data);
				    $( "#dialog" ).dialog({title: server});
  });
			}});
		

	});

	function refresh(server,event) {


		// Display "loading"
                $(jq('loading-'+server)).show();
		 $.ajax({
                         type: 'GET',
                         url: 'yum-list.php',
                         data: 'server='+server,
			 server: server,
                         success: function(data) {
			var button ="default";
			var text = "Error";
			var server = this.server;

                             var x = data.split(",");
                             var ret = x[0];
                             var updates = x[1].trim();
                             var count = x[2];
				var details = false;
                                             if (ret==0 && updates=="NO") {
                                                        button = "success";
                                                        text = "Up to date";
                                                } else if (ret==0 && updates=="YES") {
                                                        button = "danger";
                                                        text = "Update ("+count+")";
							details=true;
                                                } else if (ret==255) {
                                                        button = "warning";
                                                        text = "Check connection";
                                                }  ; 
			var btn = $(jq('upd-'+server));	
			var btndet = $(jq('details-'+server));	
		
			if (details) {
				btndet.css('display','inline');
			} else {
				btndet.css('display','none');

			}

			btn.removeClass();
			btn.addClass('btn-server btn btn-sm btn-'+button);
			btn.text(text);
                	$(jq('loading-'+server)).hide();
			
                    	}}); 

	}

	$(".refresh-all-button").click(function(event){
	
		var servers = <?php echo json_encode($servers);?>

		$(servers).each(function(index,data) {

			refresh(data,null);
		} );
		
	
	});

	$(".refresh-button").click(function(event){

		event.preventDefault();
                var server = $(event.target).attr('id').substring(8);
		refresh(server,event);
	});
	
	
        $(".btn-server").click(function(event) {
		event.preventDefault();
		var server = $(event.target).attr('id').substring(4);
		


		if (!($(event.target).hasClass('btn-success'))) {
                	$(jq('loading-'+server)).show();
			$.ajax({
	           	 type: 'GET',
		         url: 'yum-update.php',
		         data: 'server='+server,
			 server: server,
			 success: function(data) {

			var button ="default";
                        var text = "Error";
                        var server = this.server;
				var details = false;

                             var x = data.split(",");
                             var ret = x[0];
                             var updates = x[1].trim();
                             var count = x[2];

                                             if (ret==0 && updates=="NO") {
                                                        button = "success";
                                                        text = "Up to date";
                                                } else if (ret==0 && updates=="YES") {
                                                        button = "danger";
                                                        text = "Update ("+count+")";
							details = true;
                                                } else if (ret==255) {
                                                        button = "warning";
                                                        text = "Check connection";
                                                }  ; 
                        var btn = $(jq('upd-'+server)); 

			 var btndet = $(jq('details-'+server));  
                
                        if (details) {
                                btndet.css('display','inline');
                        } else {
                                btndet.css('display',':none');

                        }


                        btn.removeClass();
                        btn.addClass('btn-server btn btn-sm btn-'+button);
                        btn.text(text);
                	$(jq('loading-'+server)).hide();
                        
                        }}); 


        }});
	
	$("#refresh-page").click(function(event) {

		alert("Refreshing");
	});

    </script>
    <!--script src="bootstrap/js/bootstrap.min.js"></script-->
    <!--script src="bootstrap/js/docs.min.js"></script-->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--script src="bootstrap/js/ie10-viewport-bug-workaround.js"></script-->
  </body>
</html>
