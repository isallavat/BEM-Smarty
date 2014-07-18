<?php
	error_reporting(E_ALL & ~E_NOTICE);
	date_default_timezone_set("Europe/Moscow");

	define('SMARTY_DIR', str_replace("\\", "/", getcwd()).'/libs/smarty/');
	define('_ROOT_', $_SERVER['DOCUMENT_ROOT']);

	require_once(SMARTY_DIR . 'Smarty.class.php');
	$smarty = new Smarty();

	$smarty->template_dir = '';
	$smarty->compile_dir = 'templates_c';
	$smarty->config_dir = 'configs';
	$smarty->cache_dir = 'cache';
	$smarty->caching  = false;

	$configs = json_decode(file_get_contents(_ROOT_ . '/configs/configs.json'), true);

	//** раскомментируйте следующую строку для отображения отладочной консоли
	//$smarty->debugging = true;


	$smarty->registerPlugin('function', 'compile', 'smarty_compile');

	function smarty_compile($params) {
		$tree = parse_tree($params['tree']);
		return tree2html($tree);
	}


	function parse_tree($tree, $ctx = false) {
		$tree = !@key($tree[0]) ? array($tree) : $tree;

		foreach ($tree as $key => $branch) {
			$ctx = $branch['block'] ? $branch : $ctx;
			//$ctx = $ctx ? $ctx : $branch;
			$cls = '';
			$tpl = '';

			!$branch['tag'] && $branch['tag'] = 'div';

			if (is_array($branch)) {
				if ($branch['block']) {
					$cls = $branch['block'];
				}
				else if ($branch['block'] && $branch['elem']) {
					$cls = $branch['block'] . '__' . $branch['elem'];
				}
				else if ($branch['elem']) {
					$cls = $ctx['block'] . '__' . $branch['elem'];
				}

				if ($branch['content'] && !is_string($branch['content'])) {
					$branch['content'] = parse_tree($branch['content'], $ctx);
				}

				if ($cls && $branch['mods']) {
					foreach ($branch['mods'] as $modName => $modValue) {
						$cls .= ' ' . $cls . '_' . $modName . ($modValue === true ? '' : '_' . $modValue);
					}
				}

				if(!$ctx['used'] && $html = get_tpl($cls, $ctx)) {
					$branch = $html;
				} else {
					$branch['cls'] && $cls .= $cls ? ' ' . $branch['cls'] : $branch['cls'];
					$cls && $branch['attrs']['class'] = $cls;
				}

				$tree[$key] = $branch;
			}

		}

		return $tree;
	}


	function tree2html($tree) {
		//$tree = !@key($tree[0]) ? array($tree) : $tree;
		$resultStr = '';

		foreach ($tree as $key => $branch) {
			if (is_string($branch)) {
				$resultStr .= $branch;
			}
			else {
				if ($branch['content'] && is_array($branch['content'])) {
					$branch['content'] = tree2html($branch['content']);
				}
				$resultStr .= html_tag($branch);
			}
		}

		return $resultStr;
	}


	function html_tag($elem) {
		$singleTags = array('img', 'input', 'br', 'hr', 'link', 'meta', 'param', 'base', 'frame', 'bgsound');
		$resultStr = '';
		$attrsStr = '';
		$attrs = $elem['attrs'] ? $elem['attrs'] : array();
		$tag = $elem['tag'];
		$content = $elem['content'];

		foreach ($attrs as $key => $value) {
			$attrsStr .= ' ';

			if ($value && substr($value, 0, 1) == '{') {
				$attrsStr .= $key . '=\'' . $value . '\'';
			} else if ($value === true) {
				$attrsStr .= $key;
			} else if ($value) {
				$attrsStr .= $key . '="' . $value . '"';
			}
		}

		if (in_array($tag, $singleTags)) {
			$resultStr = '<' . $tag . $attrsStr . ' />';
		} else {
			$resultStr = '<' . $tag . $attrsStr . '>' . $content . '</' . $tag . '>';
		}

		return $resultStr;
	}


	function get_tpl($cls, $ctx) {
		global $smarty;
		global $configs;
		$names = explode(' ', $cls);

		foreach ($names as $name) {
			if (!$name) continue;
			$_name = explode('__', $name);

			$tpl = $configs['blocksDir'] . '/' . $_name[0];
			if ($_name[1]) {
				$_name[1] = explode('_', $_name[1]);
				$tpl .= '/__' . $_name[1][0];

				$_name[1][1] && $tpl .= '/_' . $_name[1][1];
			}
			$tpl .= '/' . $name . '.tpl';

			if (file_exists($tpl)) {
				$ctx['used'] = true;
				$smarty->assign('ctx', $ctx);
				return $smarty->fetch($tpl);
			}
		}
	}

	exec('node ' . _ROOT_ . '/static.js');

	$smarty->display($configs['bundlesDir'] . '/index/index.tpl');
?>