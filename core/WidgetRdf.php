<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\model;

interface WidgetRdf
{
    public const CLASS_URI_WIDGET                        = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass';
    public const PROPERTY_WIDGET                         = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget';
    public const PROPERTY_WIDGET_RADIO                   = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox';
    public const PROPERTY_WIDGET_COMBO                   = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox';
    public const PROPERTY_WIDGET_CHECK                   = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox';
    public const PROPERTY_WIDGET_FTE                     = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox';
    public const PROPERTY_WIDGET_SEARCH_BOX              = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#SearchTextBox';
    public const PROPERTY_WIDGET_TIMER                   = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Timer';
    public const PROPERTY_WIDGET_TREEVIEW                = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView';
    public const PROPERTY_WIDGET_LABEL                   = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Label';
    public const PROPERTY_WIDGET_CONSTRAINT_TYPE         = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes';
    public const PROPERTY_WIDGET_ID                      = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#identifier';
    public const CLASS_URI_WIDGET_RENDERER               = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetRenderer';
    public const PROPERTY_WIDGET_RENDERER_WIDGET         = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#renderedWidget';
    public const PROPERTY_WIDGET_RENDERER_MODE           = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#renderMode';
    public const PROPERTY_WIDGET_RENDERER_IMPLEMENTATION = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#implementation';
}
