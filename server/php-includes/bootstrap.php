<?php

namespace Detaphant;

require_once realpath(__DIR__.'/../vendor/autoload.php');
require_once realpath(__DIR__.'/sessionHandler.php');
use tuefekci\deta\Deta;

class Bootstrap {

	private static $deta;

	public static function init() {
		self::$deta = new Deta();
		//Session::init(self::$deta);
	}

	private static function initAutoloader() {
	}


}

Bootstrap::init();

