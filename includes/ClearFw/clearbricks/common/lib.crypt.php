<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Clearbricks.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

/**
* Functions to handle passwords or sensitive data
*
* @package Clearbricks
* @subpackage Common
*/
class crypt
{
	/**
	* SHA1 or MD5 + HMAC
	*
	* Returns an HMAC encoded value of <var>$data</var>, using the said <var>$key</var>
	* and <var>$hashfunc</var> as hash method (sha1 or md5 are accepted.)
	*
	* @param	string	$key			Hash key
	* @param	string	$data		Data
	* @param	string	$hashfunc		Hash function (md5 or sha1)
	* @return string
	*/
	public static function hmac($key,$data,$hashfunc='sha1')
	{
		$blocksize=64;
		if ($hashfunc != 'sha1') {
			$hashfunc = 'md5';
		}
		
		if (strlen($key)>$blocksize) {
			$key=pack('H*', $hashfunc($key));
		}
		
		$key=str_pad($key,$blocksize,chr(0x00));
		$ipad=str_repeat(chr(0x36),$blocksize);
		$opad=str_repeat(chr(0x5c),$blocksize);
		$hmac = pack('H*',$hashfunc(($key^$opad).pack('H*',$hashfunc(($key^$ipad).$data))));
		return bin2hex($hmac);
	}
	
	/**
	* Password generator
	*
	* Returns an 8 characters random password.
	*
	* @todo Add a length param
	*
	* @return	string
	*/
	public static function createPassword()
	{
		$pwd = array();
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$chars2 = '$!@';
		
		foreach (range(0,7) as $i) {
			$pwd[] = $chars[rand(0,strlen($chars)-1)];
		}
		
		$pos1 = array_rand(array(0,1,2,3));
		$pos2 = array_rand(array(4,5,6,7));
		$pwd[$pos1] = $chars2[rand(0,strlen($chars2)-1)];
		$pwd[$pos2] = $chars2[rand(0,strlen($chars2)-1)];
		
		return implode('',$pwd);
	}
}
?>