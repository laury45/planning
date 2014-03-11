<!DOCTYPE HTML>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>formulaire d'authentification</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Le styles -->
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<style type="text/css">
	body {
		padding-top: 60px;
		padding-bottom: 40px;
	}

	.sidebar-nav {
		padding: 9px 0;
	}

	@media (max-width: 980px) {
		/* Enable use of floated navbar text */
		.navbar-text.pull-right {
			float: none;
			padding-left: 5px;
			padding-right: 5px;
		}
	}
	</style>
	<link href="css/bootstrap-responsive.css" rel="stylesheet">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <![endif]-->

      <!-- Fav and touch icons -->
      <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
      <link rel="stylesheet" type="text/css" media="screen"
      href="http://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">

      <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
      <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
      <link rel="shortcut icon" href="ico/favicon.png">
  </head>

  <body>

  	<div class="navbar navbar-inverse navbar-fixed-top">
  		<div class="navbar-inner">
  			<div class="container-fluid">
  				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
  					<span class="icon-bar"></span>
  					<span class="icon-bar"></span>
  					<span class="icon-bar"></span>
  				</button>
  				<a class="brand" href="#">Accueuil</a>
  				<div class="nav-collapse collapse">
  				</div><!--/.nav-collapse -->
  			</div>

  		</div>
  	</div>

  	<?php
  	session_start();
/*if(empty($_SESSION['login']))
{
 header('Location: auth1.php');
 exit();
}*/


require_once('connect.php');
$dsn="mysql:dbname=".BASE.";host=".SERVER;
try
{
	$connexion=new PDO($dsn,USER,PASSWD);

}
catch(PDOException $e)
{
	printf("echec de la connexion :%s\n", $e->getMessage());
	exit();
}

$errorMessage= '';

if(!empty($_POST))
{
  //les identifiants sont transmis ?
	if(!empty($_POST['activite']) && !empty($_POST['heure']) && !empty($_POST['dateact']))
	{

		$sql2="SELECT * from planning where login=:login and heure=:heure and dateact=:dateact"; 
		$date=date('Y-m-d',strtotime($_POST['dateact'])); 
		$stmt2=$connexion->prepare($sql2);
		$stmt2->bindParam(':login', $_SESSION['login']);
		$stmt2->bindParam(':heure', $_POST['heure']);
		$stmt2->bindParam(':dateact', $date);
		$stmt2->execute();
		$res=$stmt2->rowCount(); 
		//echo $res;
		if($res==0){


			$sql="INSERT INTO planning VALUES (:login,:activite,:heure,:dateact)";
			$stmt=$connexion->prepare($sql);
			$stmt->bindParam(':login', $_SESSION['login']);
			$stmt->bindParam(':activite', $_POST['activite']);
			$stmt->bindParam(':heure', $_POST['heure']);


		//echo $_POST['dateact']; 
			$stmt->bindParam(':dateact', $date);
			$stmt->execute();

			if (!$stmt)
			{
				echo "Problème d'insertion";
			}
		}
		else{
			echo "vous avez déjà une activité à la même heure" ; 

		}


	}
	else
	{
		$errorMessage='veuillez remplir le formulaire';
	}	

	$sql3="SELECT activite, heure, dateact from planning where login=:login"; 
  //$sql4="SELECT heure    from planning where login=:login"; 
  //$sql5="SELECT dateact  from planning where login=:login"; 

	$stmt3=$connexion->prepare($sql3); 
  //$stmt4=$connexion->prepare($sql4); 
  //$stmt5=$connexion->prepare($sql5); 
  //$stmt3->bindParam(':login', $_SESSION['login']);
	$tab=array(':login' => $_SESSION['login']);
	$stmt3->execute($tab);
  //$stmt4->execute(array(':login' => $_SESSION['login']));
  //$stmt5->execute(array(':login' => $_SESSION['login']));
 // array_multisort($stmt3[0], SORT_ASC, $stmt3[1], $stmt3[2]);
  //sort($tab['dateact']); 
	?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span4">
				<?php	
				foreach($stmt3 as $res3){



					echo $res3['dateact']." : "."</br>".$res3['heure']."  ".$res3['activite']."</br>";
					echo "</br>"; 





				}
				?>
			
		</div>
		<?php
	}
	?>




	<div class="span8">
		
			<?php if(!empty($errorMessage))
			{
				echo $errorMessage;
			}
			?>
			<form action="form_journee.php" method="post">
				<fieldset>
					<legend> selectionnez votre heure et votre activite</legend>

					<p>
						<label for="activite">Activité: </label>
						<select name="activite" size="1">
							<option >java</option>
							<option >python</option>
							<option >anglais</option>
							<option >cafe</option>
							<option >repos</option>
							<option >php</option>
						</select>
					</p>

					<p> 
						<label for="dateact"></label>




						<p>Date:</br> 
							<div id="datetimepicker" class="input-append date">
								<input type="text" id="datepicker" name="dateact"></input>
								<span class="add-on">
									<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
								</span>
							</div>
							<script type="text/javascript"
							src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js">
							</script> 
							<script type="text/javascript"
							src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js">
							</script>
							<script type="text/javascript"
							src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.min.js">
							</script>
							<script type="text/javascript"
							src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.pt-BR.js">
							</script>

							<script type="text/javascript">
							$('#datetimepicker').datetimepicker({
								format: 'yyyy/MM/dd',
								startDate: '-3d'
							});
						</script><!--<input type="text" id="datepicker" name="dateact"></p>-->





					</span>
				</p>
			</p>

			<p>
				<label for="heure">Heure: </label>
				<select name="heure" size="1">
					<option value="08:00">08:00</option>
					<option value="09:00">09:00</option>
					<option value="10:00">10:00</option>
					<option value="11:00">11:00</option>
					<option value="12:00">12:00</option>
					<option value="13:00">13:00</option>
					<option value="14:00">14:00</option>
					<option value="15:00">15:00</option>
					<option value="16:00">16:00</option>
					<option value="17:00">17:00</option>
					<option value="18:00">18:00</option>
					<option value="19:00">19:00</option>
					<option value="20:00">20:00</option>
				</select>
				<br/>
				<input type="submit" value="envoyer" />
			</p>


		</fieldset>
	</form>

	<form action="deconnect.php" method="POST">
		<input type='submit' value="Logout">
	</form>
</div>
</div>
</div>





<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<script src="js/jquery.js"></script>
	<script src="js/bootstrap-transition.js"></script>
	<script src="js/bootstrap-alert.js"></script>
	<script src="js/bootstrap-modal.js"></script>
	<script src="js/bootstrap-dropdown.js"></script>
	<script src="js/bootstrap-scrollspy.js"></script>
	<script src="js/bootstrap-tab.js"></script>
	<script src="js/bootstrap-tooltip.js"></script>
	<script src="js/bootstrap-popover.js"></script>
	<script src="js/bootstrap-button.js"></script>
	<script src="js/bootstrap-collapse.js"></script>
	<script src="js/bootstrap-carousel.js"></script>
	<script src="js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="{{ asset('js/jquery-1.9.1.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/Myproj.js') }}"></script>

</body>
</html>

<?php

?>