<!doctype html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<link rel="stylesheet" href="/desktop.bundles/index/index.css"/>
	<title>БЭМ+Smarty</title>
</head>
<body>
	{compile tree = [
		[
			block => 'wrapper',
			content => [
				[
					block => 'header'
				],
				[
					block => 'auth'
				]
			]
		],
		[
			block => 'footer'
		]
	]}
</body>
</html>