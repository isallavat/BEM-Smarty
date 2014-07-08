<!doctype html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	<title>БЭМ+Smarty</title>
</head>
<body>
	{compile tree = [
		[
			block => 'header'
		],
		[
			block => 'content',
			content => [
				[
					elem => 'left',
					tag => 'section',
					content => [
						tag => 'h1',
						content => 'Hello world'
					]
				],
				[
					elem => 'right',
					tag => 'aside',
					content => 'Navigation'
				]
			]
		],
		[
			block => 'footer'
		]
	]}
</body>
</html>