var fs = require('fs')
,	stylus = require('stylus')
,	nib = require('nib')
,	configs = require('./configs/configs')
,	Techs = {};


var bundlesDir = './' + configs.bundlesDir
,	blocksDir = './' + configs.blocksDir;


Array.prototype.unique = function() {
    var a = this.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
};


Techs.styl = function(filename, src, dest) {
	var str = fs.readFileSync(src + '.styl', 'utf8');
	stylus(str)
		.import('nib')
		.use(nib())
		.render(function(err, css){
			if (err) throw err;
			css = '/* ' + src + '.styl begin */\n' + css + '/* ' + src + '.styl end */\n\n';
			fs.appendFileSync(dest + '/' + filename + '.css', css);
		});
}


var getDeps = function(path, ctx) {
	var depsFile = path + '.deps.json'
	,	depsPaths = []
	,	deps, paths;

	if(fs.existsSync(depsFile)) {
		deps = require(depsFile);
		deps = deps.length ? deps : [deps];

		deps.forEach(function(item) {
			if (item.block && !item.elems) {
				paths = createPath({ name: item.block, mods: item.mods });
				depsPaths = depsPaths.concat(paths);
				paths.forEach(function(_path) {
					depsPaths = depsPaths.concat(getDeps(blocksDir + '/' + _path, item.block));
				});
			}
			else if (item.elems) {
				item.elems.forEach(function(elem) {
					elem = typeof elem == 'string' ? { elem: elem } : elem;
					paths = createPath({ name: elem.elem, mods: elem.mods, parent: item.block || ctx });
					depsPaths = depsPaths.concat(paths);
					paths.forEach(function(_path) {
						depsPaths = depsPaths.concat(getDeps(blocksDir + '/' + _path, item.block || ctx));
					});
				});
			} else if (item.mods && ctx) {
				paths = createPath({ name: ctx, mods: item.mods });
				depsPaths = depsPaths.concat(paths);
				paths.forEach(function(_path) {
					depsPaths = depsPaths.concat(getDeps(blocksDir + '/' + _path, item.block || ctx));
				});
			}
		});
	}

	return depsPaths.unique();
}


var createPath = function(params) {
	var paths = []
	,	name = (params.parent ? params.parent + '__' : '') + params.name
	,	_name;

	if (params.mods) {
		for (var mod in params.mods) {
			_name = name + '_' + mod + (params.mods[mod] === true ? '' : '_' + params.mods[mod]);
			paths.push((params.parent ? params.parent + '/__' : '') + params.name + '/_' + mod + '/' + _name);
		}
	} else {
		paths.push((params.parent ? params.parent + '/__' : '') + params.name + '/' + name);
	}

	return paths;
}


var getStatic = function(filename, tech, paths, dest) {
	paths.forEach(function(path) {
		fs.existsSync(blocksDir + '/' + path + '.' + tech) && Techs[tech](filename, blocksDir + '/' + path, dest);
	});
}


fs.readdirSync(bundlesDir).forEach(function(bundle) {
	var bundleDir = bundlesDir + '/' + bundle;
	var deps = getDeps(bundleDir + '/' + bundle);

	fs.readdirSync(bundleDir).forEach(function(file) {
		if (file.search(/((\.deps\.json)|(\.tpl))$/) == -1) {
			fs.unlinkSync(bundleDir + '/' + file);
		}
	});

	configs.Techs.forEach(function(tech) {
		getStatic(bundle, 'styl', deps, bundlesDir + '/' + bundle + '/');
	});
});