<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>HTML form prevent multiple signups</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="shortcut icon" type="image/png" href="favicon.png"/>

  </head>

  <body>

  <main class="main-content">

			<div class="container">

        <div class="row justify-content-center">
       		<div class="col-12">

       			<form id="signup-form" action="" method="post">

       				<div class="form" style="margin-left: auto; margin-right: auto; max-width: 530px;">

								<select class="sp-spol">
									<option value="" disabled selected>Naziv</option>
									<option value="gospa">Gospa</option>
									<option value="gospod">Gospod</option>
								</select>

								<input oninvalid="this.setCustomValidity('Prosimo vnesite ime v pravilni obliki.')" oninput="setCustomValidity('')" name="firstname" pattern="[a-zA-ZđšžćčĐŠŽĆČ]*" placeholder="Vaše ime" class="sp-name" type="text">
								<input oninvalid="this.setCustomValidity('Prosimo vnesite priimek v pravilni obliki.')" oninput="setCustomValidity('')" name="lastname"  pattern="[a-zA-ZđšžćčĐŠŽĆČ]*" placeholder="Vaš priimek" class="sp-name" type="text" >
								<input oninvalid="this.setCustomValidity('Prosimo vnesite elektronski naslov v pravilni obliki.')" required oninput="setCustomValidity('')" type="email" class="sp-email" placeholder="Vaš elektronski naslov*..." name="email" />

                <p class="text-center"><input type="submit"  value="Prijavi se &#9656;" class="sp-submit" /></p>

             </form>
          </div>
        </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/bootstrap/js/form.js"></script>
    <script src="vendor/bootstrap/js/my.js"></script>

  </body>

</html>
