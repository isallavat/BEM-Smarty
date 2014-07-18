{$ctx['tag'] = 'header'}
{$ctx['content'] = [
	elem => 'bar',
	content => [
		elem => 'container',
		content => [
			[
				elem => 'logo',
				tag => 'a',
				attrs => [ href => '#' ]
			],
			[
				elem => 'nav',
				tag => 'nav',
				content => [
					[
						block => 'link',
						tag => 'a',
						content => 'Торговля',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Блоги',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Стратегии',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Графики',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Рынки',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Люди',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Обучение',
						attrs => [ href => '#' ]
					],
					[
						block => 'link',
						tag => 'a',
						content => 'Услуги',
						attrs => [ href => '#' ]
					]
				]
			]
		]
	]
]}

{compile tree = $ctx}