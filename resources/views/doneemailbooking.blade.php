<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
	body, html {
		padding: 0;
		margin: 0;
		width: 1000%;
		height: 100vh; 
	}
	
	}
	}
</style>
</head>
<body>
	<div class="email">
		<img src="https://i.ibb.co/c3yKXFM/mmlogo.png" width="150" height="150">
		<p><h4>This is an automatic email, please do not reply.</h4></p><br>
		<h3>Dear {{ $name }}</h3>
		<p>Thank you for playing Murder Manila {{ $theme_id }} hope you had fun and see you on your next game</p>
        <p>this is your booking details:</p>
		<p> Name: {{ $name }}<br>
            Reference Number: {{ $reference_number }}<br>
            Full Payment: {{$total_amount}}<br>
            Date: {{ $book_date }}<br>
            Time: {{ $book_time }}<br>
            Theme: {{ $theme_id}}<br>
            Head Count: {{ $maxpax }}<br>
            Venue: {{ $venue }}</p>
			
            Thank you, Have a great day !
		</p>

	</div>
</body>
</html>