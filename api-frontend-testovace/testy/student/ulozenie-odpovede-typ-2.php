<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Študent: uloženie odpovede na otázku typu 2</title>
</head>
<body>

<script>
	let dataPoziadavky = {
		akcia: "odoslat-odpoved",
		otazka_id: 3,
		typ_odpovede: "vyberova",
		odpoved: [ "jesť", "inovať" ]
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