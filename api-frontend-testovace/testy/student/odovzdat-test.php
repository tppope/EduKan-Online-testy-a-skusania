﻿<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Študent: odovzdanie rozpísaného testu</title>
</head>
<body>

<script>
	let dataPoziadavky = {
		akcia: "odovzdat-test"
	}


	fetch("../../../api/testy/vypracovanie-testu.php", {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(dataPoziadavky)
	})
	.then(response => response.json())
	.then(data => console.log(data));
</script>
</body>
</html>