<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Vytvorenie nového testu</title>
</head>
<body>

<script src="novy-test-sablona.js"></script>
<script>
	function vytvorNovyTest(dataTestu) {
		fetch("../../api/testy/novy-test.php", {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(dataTestu)
		})
		.then(response => response.json())
		.then(data => console.log(data));
	}

	vytvorNovyTest(novyTestSablona);
</script>
</body>
</html>