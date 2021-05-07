<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Študent: uloženie odpovede na otázku typu 3</title>
</head>
<body>

<script>
	let dataPoziadavky = {
		akcia: "odoslat-odpoved",
		otazka_id: 4,
		typ_odpovede: "parovacia",
		odpoved: [
			{lava: 1, prava: 3},
			{lava: 4, prava: 1}
		]
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