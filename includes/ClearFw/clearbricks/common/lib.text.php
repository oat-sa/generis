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
* Text utilities
*
* @package Clearbricks
* @subpackage Common
*/
class text
{
	/**
	* Check email address
	*
	* Returns true if $email is a valid email address.
	*
	* @copyright Cal Henderson
	* @license http://creativecommons.org/licenses/by-sa/2.5/ CC-BY-SA
	* @link http://www.iamcal.com/publish/articles/php/parsing_email/
	*
	* @param string	$email	Email string
	* @return boolean
	*/
	public static function isEmail($email)
	{
		$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
		$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
		$atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
		$quoted_pair = '\\x5c[\\x00-\\x7f]';
		$domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
		$quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
		$domain_ref = $atom;
		$sub_domain = "($domain_ref|$domain_literal)";
		$word = "($atom|$quoted_string)";
		$domain = "$sub_domain(\\x2e$sub_domain)*";
		$local_part = "$word(\\x2e$word)*";
		$addr_spec = "$local_part\\x40$domain";
		
		return (boolean) preg_match("!^$addr_spec$!", $email);
	}
	
	/**
	* Accents replacement
	*
	* Replaces some occidental accentuated characters by their ASCII
	* representation.
	*
	* @param	string	$str		String to deaccent
	* @return	string
	*/
	public static function deaccent($str)
	{
		$pattern['A'] = '\x{00C0}-\x{00C5}';
		$pattern['AE'] = '\x{00C6}';
		$pattern['C'] = '\x{00C7}';
		$pattern['D'] = '\x{00D0}';
		$pattern['E'] = '\x{00C8}-\x{00CB}';
		$pattern['I'] = '\x{00CC}-\x{00CF}';
		$pattern['N'] = '\x{00D1}';
		$pattern['O'] = '\x{00D2}-\x{00D6}\x{00D8}';
		$pattern['OE'] = '\x{0152}';
		$pattern['S'] = '\x{0160}';
		$pattern['U'] = '\x{00D9}-\x{00DC}';
		$pattern['Y'] = '\x{00DD}';
		$pattern['Z'] = '\x{017D}';
		
		$pattern['a'] = '\x{00E0}-\x{00E5}';
		$pattern['ae'] = '\x{00E6}';
		$pattern['c'] = '\x{00E7}';
		$pattern['d'] = '\x{00F0}';
		$pattern['e'] = '\x{00E8}-\x{00EB}';
		$pattern['i'] = '\x{00EC}-\x{00EF}';
		$pattern['n'] = '\x{00F1}';
		$pattern['o'] = '\x{00F2}-\x{00F6}\x{00F8}';
		$pattern['oe'] = '\x{0153}';
		$pattern['s'] = '\x{0161}';
		$pattern['u'] = '\x{00F9}-\x{00FC}';
		$pattern['y'] = '\x{00FD}\x{00FF}';
		$pattern['z'] = '\x{017E}';
		
		$pattern['ss'] = '\x{00DF}';
		
		foreach ($pattern as $r => $p) {
			$str = preg_replace('/['.$p.']/u',$r,$str);
		}
		
		return $str;
	}
	
	/**
	* String to URL
	*
	* Transforms a string to a proper URL.
	*
	* @param string	$str			String to transform
	* @param boolean	$with_slashes	Keep slashes in URL
	* @return string
	*/
	public static function str2URL($str,$with_slashes=true)
	{
		$str = self::deaccent($str);
		$str = preg_replace('/[^A-Za-z0-9_\s\'\:\/[\]-]/','',$str);
		
		return self::tidyURL($str,$with_slashes);
	}
	
	/**
	* URL cleanup
	*
	* @param string	$str			URL to tidy
	* @param boolean	$keep_slashes	Keep slashes in URL
	* @param boolean	$keep_spaces	Keep spaces in URL
	* @return string
	*/
	public static function tidyURL($str,$keep_slashes=true,$keep_spaces=false)
	{
		$str = strip_tags($str);
		$str = str_replace(array('?','&','#','=','+','<','>','"','%'),'',$str);
		$str = str_replace("'",' ',$str);
		$str = preg_replace('/[\s]+/',' ',trim($str));
		
		if (!$keep_slashes) {
			$str = str_replace('/','-',$str);
		}
		
		if (!$keep_spaces) {
			$str = str_replace(' ','-',$str);
		}
		
		$str = preg_replace('/[-]+/','-',$str);
		
		# Remove path changes in URL
		$str = preg_replace('%^/%','',$str);
		$str = preg_replace('%\.+/%','',$str);
		
		return $str;
	}
	
	/**
	* Cut string
	*
	* Returns a cuted string on spaced at given length $l.
	*
	* @param	string	$str		String to cut
	* @param	integer	$l		Length to keep
	* @return	string
	*/
	public static function cutString($str,$l)
	{
		$s = preg_split('/([\s]+)/u',$str,-1,PREG_SPLIT_DELIM_CAPTURE);
		
		$res = '';
		$L = 0;
		
		if (mb_strlen($s[0]) >= $l) {
			return mb_substr($s[0],0,$l);
		}
		
		foreach ($s as $v)
		{
			$L = $L+mb_strlen($v);
			
			if ($L > $l) {
				break;
			} else {
				$res .= $v;
			}
		}
		
		return trim($res);
	}
	
	/**
	* Split words
	*
	* Returns an array of words from a given string.
	*
	* @param string	$str		Words to split
	* @return array
	*/
	public static function splitWords($str)
	{
		$non_word = '\x{0000}-\x{002F}\x{003A}-\x{0040}\x{005b}-\x{0060}\x{007B}-\x{007E}\x{00A0}-\x{00BF}\s';
		if (preg_match_all('/([^'.$non_word.']{3,})/msu',html::clean($str),$match)) {
			foreach ($match[1] as $i => $v) {
				$match[1][$i] = mb_strtolower($v);
			}
			return $match[1];
		}
		return array();
	}
	
	/**
	* Encoding detection
	*
	* Returns the encoding (in lowercase) of given $str.
	*
	* @param string	$str		String
	* @return string
	*/
	public static function detectEncoding($str)
	{
		return strtolower(mb_detect_encoding($str.' ',
			'UTF-8,ISO-8859-1,ISO-8859-2,ISO-8859-3,'.
			'ISO-8859-4,ISO-8859-5,ISO-8859-6,ISO-8859-7,ISO-8859-8,'.
			'ISO-8859-9,ISO-8859-10,ISO-8859-13,ISO-8859-14,ISO-8859-15'));
	}
	
	/**
	* UTF8 conversions
	*
	* Returns an UTF-8 converted string. If $encoding is not specified, the
	* function will try to detect encoding.
	*
	* @param string	$str		String to convert
	* @param string	$encoding	Optionnal "from" encoding
	* @return string
	*/
	public static function toUTF8($str,$encoding=null)
	{
		if (!$encoding) {
			$encoding = self::detectEncoding($str);
		}
		
		if ($encoding != 'utf-8') {
			$str = iconv($encoding,'UTF-8',$str);
		}
		
		return $str;
	}
	
	/**
	* Find bad UTF8 tokens
	*
	* Locates the first bad byte in a UTF-8 string returning it's
	* byte index in the string
	* PCRE Pattern to locate bad bytes in a UTF-8 string
	* Comes from W3 FAQ: Multilingual Forms
	* Note: modified to include full ASCII range including control chars
	*
	* @copyright Harry Fuecks
	* @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU LGPL 2.1
	* @link http://phputf8.sourceforge.net
	*
	* @param string	$str		String to search
	* @return integer|false
	*/
	public static function utf8badFind($str)
	{
		$UTF8_BAD =
		'([\x00-\x7F]'.                          # ASCII (including control chars)
		'|[\xC2-\xDF][\x80-\xBF]'.               # non-overlong 2-byte
		'|\xE0[\xA0-\xBF][\x80-\xBF]'.           # excluding overlongs
		'|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.    # straight 3-byte
		'|\xED[\x80-\x9F][\x80-\xBF]'.           # excluding surrogates
		'|\xF0[\x90-\xBF][\x80-\xBF]{2}'.        # planes 1-3
		'|[\xF1-\xF3][\x80-\xBF]{3}'.            # planes 4-15
		'|\xF4[\x80-\x8F][\x80-\xBF]{2}'.        # plane 16
		'|(.{1}))';                              # invalid byte
		$pos = 0;
		$badList = array();
		
		while (preg_match('/'.$UTF8_BAD.'/S', $str, $matches)) {
			$bytes = strlen($matches[0]);
			if ( isset($matches[2])) {
				return $pos;
			}
			$pos += $bytes;
			$str = substr($str,$bytes);
		}
		return false;
	}
	
	/**
	* UTF8 cleanup
	*
	* Replaces non utf8 bytes in $str by $repl.
	*
	* @copyright Harry Fuecks
	* @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html GNU LGPL 2.1
	* @link http://phputf8.sourceforge.net
	*
	* @param string	$str		String to clean
	* @param string	$repl	Replacement string
	* @return string
	*/
	public static function cleanUTF8($str,$repl='?')
	{
		while (($bad_index = self::utf8badFind($str)) !== false) {
			$str = substr_replace($str,$repl,$bad_index,1);
		}
		
		return $str;
	}
	
	/**
	* BOM removal
	*
	* Removes BOM from the begining of a string if present.
	*
	* @param string	$str		String to clean
	* @return string
	*/
	public static function removeBOM($str)
	{
		if (substr_count($str,'﻿')) {
			return str_replace('﻿','',$str); 
		}
		
		return $str;
	}
	
	/**
	* Quoted printable conversion
	*
	* Encodes given str to quoted printable
	*
	* @param string	$str		String to encode
	* @return string
	*/
	public static function QPEncode($str)
	{
		$res = '';
		
		foreach (preg_split("/\r?\n/msu", $str) as $line)
		{
			$l = '';
			preg_match_all('/./',$line,$m);
			
			foreach ($m[0] as $c)
			{
				$a = ord($c);
				
				if ($a < 32 || $a == 61 || $a > 126) {
					$c = sprintf('=%02X',$a);
				}
				
				$l .= $c;
			}
			
			$res .= $l."\r\n";
		}
		return $res;
	}
}
?>