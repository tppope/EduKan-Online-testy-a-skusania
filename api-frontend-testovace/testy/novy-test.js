function vytvorNovyTest(dataTestu) {
	fetch("../../api/testy/novy-test.php")
	.then(response => response.json())
	.then(data => console.log(data));
}