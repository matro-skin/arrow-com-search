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
			<div id="vue-parts-search"></div>
		</div>
	</div>

	<!-- PUT THIS -->
	<script>const partsSearchConfig = { url: '/api', drivers: [ 'ArrowCom', 'Element14' ] }</script>
	<script src="dist/js/partsSearch.js?t=<?php echo filemtime(__DIR__ . '/dist/js/partsSearch.js') ?>"></script>
	<!-- / END -->

</body>
</html>
