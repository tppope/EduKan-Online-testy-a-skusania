<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Študent: uloženie odpovede na otázku typu 1</title>
</head>
<body>

<script>
	let dataPoziadavky = {
		akcia: "odoslat-odpoved",
		otazka_id: 1,
		typ_odpovede: "textova",
		odpoved: "Fakulta elektrotechniky a informatiky"
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