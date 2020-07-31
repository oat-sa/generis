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
    public const NAMESPACE = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf';

    public const CLASS_URI_WIDGET                        = self::NAMESPACE . '#WidgetClass';
    public const PROPERTY_WIDGET                         = self::NAMESPACE . '#widget';
    public const PROPERTY_WIDGET_CONSTRAINT_TYPE         = self::NAMESPACE . '#rangeConstraintTypes';
    public const PROPERTY_WIDGET_ID                      = self::NAMESPACE . '#identifier';
    public const CLASS_URI_WIDGET_RENDERER               = self::NAMESPACE . '#WidgetRenderer';
    public const PROPERTY_WIDGET_RENDERER_WIDGET         = self::NAMESPACE . '#renderedWidget';
    public const PROPERTY_WIDGET_RENDERER_MODE           = self::NAMESPACE . '#renderMode';
    public const PROPERTY_WIDGET_RENDERER_IMPLEMENTATION = self::NAMESPACE . '#implementation';
}
