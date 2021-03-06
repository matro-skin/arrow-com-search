<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>arrow.com search api</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" >
</head>
<body>
	<div class="container">
		<div class="p-5">

			<!-- SEARCH FORM -->
			<div id="vue-parts-search"></div>
			<!-- / END -->

		</div>
	</div>

	<!-- FOOTER -->
	<script>const partsSearchConfig = { url: '/api', drivers: [ 'ArrowCom', 'Element14' ], input: false, instant: true }</script>
	<script src="dist/js/partsSearch.js?t=<?php echo filemtime(__DIR__ . '/dist/js/partsSearch.js') ?>"></script>
	<!-- / END -->

</body>
</html>
