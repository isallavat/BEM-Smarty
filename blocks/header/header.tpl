{$ctx['tag'] = 'header'}
{$ctx['content'] = [
	elem => 'logo',	
	content => [
		block => 'link',
		content => 'Logo link'
	]
]}

{compile tree = $ctx}