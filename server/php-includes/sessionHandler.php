<?php

namespace Detaphant;

class Session {

	private static $deta;
	private static $base;

	public static function init($deta) {
		self::$deta = $deta;
		self::$base = self::$deta->base('sessions');
		self::initSession();
	}

	private static function initSession() {
		session_set_save_handler(
			array('Detaphant\Session', 'open'),
			array('Detaphant\Session', 'close'),
			array('Detaphant\Session', 'read'),
			array('Detaphant\Session', 'write'),
			array('Detaphant\Session', 'destroy'),
			array('Detaphant\Session', 'clean')
		);
	}

	public static function open() {
		return true;
	}

	public static function close() {
		return true;
	}

	public static function read($id) {
		try {
			$session = self::$base->get($id);
			return $session->data;
		} catch (\Throwable $th) {
			return "";
		}
	}

	public static function write($id, $data) {

		try {
			self::$base->insert([[
				'key' => $id,
				'data' => $data
			]]);
			return true;
		} catch (\Throwable $th) {
			throw $th;
			return false;
		}
	}

	public static function destroy($id) {
		$session = self::$base->delete($id);
		return true;
	}

	public static function clean($max) {
		$old = time() - $max;
		return true;
	}

}