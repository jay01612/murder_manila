<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
	body, html {
		padding: 0;
		margin: 0;
		width: 1000%;
		height: 100vh; 
            height: 100vh;
		height: 100vh; 
	}
	
	}
	}
</style>
</head>
<body>
	<div class="email">
		<!-- <img src="https://i.ibb.co/c3yKXFM/mmlogo.png" width="150" height="150"> -->
		<p><h4>This is an automatic email, please do not reply.</h4></p><br>
		<h3>Dear {{ $name }}</h3>
		<p>Thanks again for your booking at <strong>Murder Manila</strong>.</p>
		<p>Your booking has been finished, this is your booking details:</p>
		<p>Name: {{ $name }}<br>
			Reference Number: {{ $referenceNumber }}<br>
			Mobile Number: {{ $mobileNumber }}<br>
			Date: {{ $dateStart }}<br>
			Time: {{ $timeStart }}<br>
			Head Count: {{ $maxPax }}<br>
			Venue: {{ $venue }}<br/>
			Amount: {{ $amount }}</p>

			<p>You can pay half or full amount to this bank information:<br>
			<strong>4574-5350-0151-1354 BDO</strong> (Banco De Oro)<br>
			Name: 360 A Event Management Corp.
			</p>
			<p>Send an email attaching the receipt of payment through this email: <i>ppotdota@gmail.com</i><br>
			</p>

	</div>
</body>
</html>