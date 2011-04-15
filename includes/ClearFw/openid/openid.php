<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of "myWiWall".
# Copyright (c) 2008 JanRain, CRP Henri Tudor and contributors.
# All rights reserved.
#
# This file is dual-licenced under GPL v2.0 and Apache v2.0 licences
#
# ***** END LICENSE BLOCK *****
/**
 *
 * LICENSE: See the COPYING file included in this distribution.
 *
 * @package OpenID
 * @author JanRain, Inc. <openid@janrain.com>
 * @copyright 2005-2008 Janrain, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache
 */
// FIXME à refaire !
// code provenant de l'exemple de la lib openid, vaguement hacké, licence inconnue

function buildURL($p = '') {
	$index = '';
	if (defined("INDEX_FILE")) {
		$index = INDEX_FILE;
	} else {
		$index = 'index.php';
	}	
	
	$url	= HttpRequest::getPathUrl();
	$nb		= strlen($url);

    if ($nb == 0 || $url[$nb-1] != "/") {
    	$index = '/'.$index.'/';
    } else {
 		$index = $index.'/';
    }

	return "http://".$_SERVER['HTTP_HOST'].HttpRequest::getPathUrl().$index.Util::getActionString($p);	
}

/**
 * Get the URL of the current script
 */
function getServerURL()
{
    $path = $_SERVER['SCRIPT_NAME'];
    $host = $_SERVER['HTTP_HOST'];
    $port = $_SERVER['SERVER_PORT'];
    $s = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's' : '';
    if (($s && $port == "443") || (!$s && $port == "80")) {
        $p = '';
    } else {
        $p = ':' . $port;
    }

    return "http$s://$host$p$path";
}

function getServer()
{
    static $server = null;
    if (!isset($server)) {
        $server =& new Auth_OpenID_Server(getOpenIDStore(),
                                          buildURL());
    }
    return $server;
}

function setRequestInfo($info=null)
{
    if (!isset($info)) {
        unset($_SESSION['request']);
    } else {
        $_SESSION['request'] = serialize($info);
    }
}

function getRequestInfo()
{
    return isset($_SESSION['request'])
        ? unserialize($_SESSION['request'])
        : false;
}

function getOpenIDStore()
{        
    $s = new WMySqlStore(DbUtil::accessFactory());
    $s->createTables();

    return $s;
}

function link_render($url, $text=null) {
    $esc_url = htmlspecialchars($url, ENT_QUOTES);
    $text = ($text === null) ? $esc_url : $text;
    return sprintf('<a href="%s">%s</a>', $esc_url, $text);
}

function idURL($identity)
{
    return BASE_OPENID_URL. $identity;
}

?>
