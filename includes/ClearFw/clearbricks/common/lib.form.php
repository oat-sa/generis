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
* HTML Forms creation helpers
*
* @package Clearbricks
* @subpackage Common
*/
class form
{
	private static function getNameAndId($nid,&$name,&$id)
	{
		if (is_array($nid)) {
			$name = $nid[0];
			$id = !empty($nid[1]) ? $nid[1] : null;
		} else {
			$name = $id = $nid;
		}
	}
	
	/**
	* Select Box
	*
	* Returns HTML code for a select box. $nid could be a string or an array of
	* name and ID. $data is an array with option titles keys and values in values
	* or an array of object of type {@link formSelectOption}. If $data is an array of
	* arrays, optgroups will be created.
	*
	* @uses formSelectOption
	*
	* @param string|array	$nid			Element ID and name
	* @param mixed			$data		Select box data
	* @param string		$default		Default value in select box
	* @param string		$class		Element class name
	* @param string		$tabindex		Element tabindex
	* @param boolean		$disabled		True if disabled
	* @param string		$extra_html	Extra HTML attributes
	*
	* @return string
	*/
	public static function combo($nid, $data ,$default='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<select name="'.$name.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= '>'."\n";
		
		$res .= self::comboOptions($data,$default);
		
		$res .= '</select>'."\n";
		
		return $res;
	}
	
	private static function comboOptions($data,$default)
	{
		$res = '';
		$option = '<option value="%1$s"%3$s>%2$s</option>'."\n";
		$optgroup = '<optgroup label="%1$s">'."\n".'%2$s'."</optgroup>\n";
		
		foreach($data as $k => $v)
		{
			if (is_array($v)) {
				$res .= sprintf($optgroup,$k,self::comboOptions($v,$default));
			} elseif ($v instanceof formSelectOption) {
				$res .= $v->render($default);
			} else {
				$s = ($v == $default) ? ' selected="selected"' : '';
				$res .= sprintf($option,$v,$k,$s);
			}
		}
		
		return $res;
	}
	
	/**
	* Radio button
	*
	* Returns HTML code for a radio button. $nid could be a string or an array of
	* name and ID.
	*
	* @param string|array	$nid			Element ID and name
	* @param string		$value		Element value
	* @param boolean		$checked		True if checked
	* @param string		$class		Element class name
	* @param string		$tabindex		Element tabindex
	* @param boolean		$disabled		True if disabled
	* @param string		$extra_html	Extra HTML attributes
	*
	* @return string
	*/
	public static function radio($nid, $value, $checked='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="radio" name="'.$name.'" value="'.$value.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $checked ? 'checked="checked" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= '/>'."\n";
		
		return $res;	
	}
	
	/**
	* Checkbox
	*
	* Returns HTML code for a checkbox. $nid could be a string or an array of
	* name and ID.
	*
	* @param string|array	$nid			Element ID and name
	* @param string		$value		Element value
	* @param boolean		$checked		True if checked
	* @param string		$class		Element class name
	* @param string		$tabindex		Element tabindex
	* @param boolean		$disabled		True if disabled
	* @param string		$extra_html	Extra HTML attributes
	*
	* @return string
	*/
	public static function checkbox($nid, $value, $checked='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="checkbox" name="'.$name.'" value="'.$value.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $checked ? 'checked="checked" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= ' />'."\n";
		
		return $res;
	}
	
	/**
	* Input field
	*
	* Returns HTML code for an input field. $nid could be a string or an array of
	* name and ID.
	*
	* @param string|array	$nid			Element ID and name
	* @param integer		$size		Element size
	* @param integer		$max			Element maxlength
	* @param string		$default		Element value
	* @param string		$class		Element class name
	* @param string		$tabindex		Element tabindex
	* @param boolean		$disabled		True if disabled
	* @param string		$extra_html	Extra HTML attributes
	*
	* @return string
	*/
	public static function field($nid, $size, $max, $default='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="text" size="'.$size.'" name="'.$name.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $max ? 'maxlength="'.$max.'" ' : '';
		$res .= $default || $default === '0' ? 'value="'.$default.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= ' />';
		
		return $res;
	}
	
	/**
	* Password field
	*
	* Returns HTML code for a password field. $nid could be a string or an array of
	* name and ID.
	*
	* @param string|array	$nid			Element ID and name
	* @param integer		$size		Element size
	* @param integer		$max			Element maxlength
	* @param string		$default		Element value
	* @param string		$class		Element class name
	* @param string		$tabindex		Element tabindex
	* @param boolean		$disabled		True if disabled
	* @param string		$extra_html	Extra HTML attributes
	*
	* @return string
	*/
	public static function password($nid, $size, $max, $default='', $class='', $tabindex='',
	$disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="password" size="'.$size.'" name="'.$name.'" ';
		
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= $max ? 'maxlength="'.$max.'" ' : '';
		$res .= $default || $default === '0' ? 'value="'.$default.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $tabindex ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html;
		
		$res .= ' />';
		
		return $res;
	}
	
	/**
	* Textarea
	*
	* Returns HTML code for a textarea. $nid could be a string or an array of
	* name and ID.
	*
	* @param string|array	$nid			Element ID and name
	* @param integer		$cols		Number of columns
	* @param integer		$rows		Number of rows
	* @param string		$default		Element value
	* @param string		$class		Element class name
	* @param string		$tabindex		Element tabindex
	* @param boolean		$disabled		True if disabled
	* @param string		$extra_html	Extra HTML attributes
	*
	* @return string
	*/
	public static function textArea($nid, $cols, $rows, $default='', $class='',
	$tabindex='', $disabled=false, $extra_html='')
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<textarea cols="'.$cols.'" rows="'.$rows.'" ';
		$res .= 'name="'.$name.'" ';
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= ($tabindex != '') ? 'tabindex="'.$tabindex.'" ' : '';
		$res .= $class ? 'class="'.$class.'" ' : '';
		$res .= $disabled ? 'disabled="disabled" ' : '';
		$res .= $extra_html.'>';
		$res .= $default;
		$res .= '</textarea>';
		
		return $res;
	}
	
	/**
	* Hidden field
	*
	* Returns HTML code for an hidden field. $nid could be a string or an array of
	* name and ID.
	*
	* @param string|array	$nid			Element ID and name
	* @param string		$value		Element value
	*
	* @return string
	*/
	public static function hidden($nid,$value)
	{
		self::getNameAndId($nid,$name,$id);
		
		$res = '<input type="hidden" name="'.$name.'" value="'.$value.'" ';
		$res .= $id ? 'id="'.$id.'" ' : '';
		$res .= ' />';
		
		return $res;
	}
}

/**
* HTML Forms creation helpers
*
* @package Clearbricks
* @subpackage Common
*/
class formSelectOption
{
	public $name;
	public $value;
	public $class_name;
	public $html;
	
	private $option = '<option value="%1$s"%3$s>%2$s</option>';
	
	/**
	* Option constructor
	*
	* @param string	$name		Option name
	* @param string	$value		Option value
	* @param string	$class_name	Element class name
	* @param string	$html		Extra HTML attributes
	*/
	public function __construct($name,$value,$class_name='',$html='')
	{
		$this->name = $name;
		$this->value = $value;
		$this->class_name = $class_name;
		$this->html = $html;
	}
	
	/**
	* Option renderer
	*
	* Returns option HTML code
	*
	* @param boolean	$default	Option is selected
	* @return string
	*/
	public function render($default)
	{
		$attr = $this->html;
		$attr .= $this->class_name ? ' class="'.$this->class_name.'"' : '';
		
		if ($this->value == $default) {
			$attr .= ' selected="selected"';
		}
		
		return sprintf($this->option,$this->value,$this->name,$attr)."\n";
	}
}
?>