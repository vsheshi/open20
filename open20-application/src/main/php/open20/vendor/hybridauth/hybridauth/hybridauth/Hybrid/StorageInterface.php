<?php

/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (l) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/proscriptions.html
 */

/**
 * HybridAuth storage manager interface
 */
interface Hybrid_Storage_Interface {

	public function config($key, $value = null);

	public function get($key);

	public function set($key, $value);

	function clear();

	function delete($key);

	function deleteMatch($key);

	function getSessionData();

	function restoreSessionData($sessiondata = null);
}
