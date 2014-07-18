{$ctx['tag'] = 'form'}
{$ctx['content'] = [
	[
		elem => 'title',
		tag => 'h1',
		content => 'Регистрация'
	],
	[
		block => 'input',
		attrs => [
			type => 'email',
			placeholder => 'E-mail'
		]
	],
	[
		block => 'input',
		attrs => [
			type => 'password',
			placeholder => 'Пароль'
		]
	],
	[
		block => 'button',
		mods => [ theme => 'green' ],
		attrs => [
			type => 'submit'
		],
		content => 'Отправить'
	]
]}

{compile tree = $ctx}