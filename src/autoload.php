<?php

// Because Laravel instantiates a VarCloner instance inside the FoundationServiceProvider,
// which is loaded before package service providers, we need to register these even earlier.
(function() {
	$casters = require __DIR__.'/Casters/manifest.php';
	foreach ($casters as $caster) {
		$caster::autoload();
	}
})();
