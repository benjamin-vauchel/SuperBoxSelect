<?php
/**
 * SuperBoxSelect Build Script
 *
 * Copyright 2011 Benjamin Vauchel <contact@omycode.fr>
 *
 * SuperBoxSelect is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * SuperBoxSelect is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * SuperBoxSelect; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package superboxselect
 * @subpackage build
 */
/**
 * Build SuperBoxSelect Package
 *
 * Description: Build script for SuperBoxSelect package
 * @package superboxselect
 * @subpackage build
 */

/* Set package info be sure to set all of these */
define('PKG_NAME','SuperBoxSelect');
define('PKG_NAME_LOWER','superboxselect');
define('PKG_VERSION','1.0.1');
define('PKG_RELEASE','rc1');
define('PKG_CATEGORY','SuperBoxSelect');

/******************************************
 * Work begins here
 * ****************************************/

/* set start time */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    /* note that the next two must not have a trailing slash */
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'data' => $root . '_build/data/',
    'docs' => $root . 'core/components/superboxselect/docs/',
    'tv_php'=> $root . 'core/model/modx/processors/element/tv/renders/mgr/input/superboxselect.class.php',
    'tv_tpl'=> $root . 'manager/templates/default/element/tv/renders/input/superboxselect.tpl',
);
unset($root);

/* Instantiate MODx -- if this require fails, check your
 * _build/build.config.php file
 */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* load builder */
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');


/* create category  The category is required and will automatically
 * have the name of your package
 */

$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_CATEGORY);

/* Create Category attributes array dynamically
 * based on which elements are present
 */

$attr = array(xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
);

 /* Add TV  */
$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['TemplateVars'] = array(
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'name',
);
$modx->log(modX::LOG_LEVEL_INFO,'Adding in Template Variables.');
/* note: Template Variables' default properties are set in transport.tvs.php */
$templatevariables = include $sources['data'].'transport.tvs.php';
if (is_array($templatevariables)) {
    $category->addMany($templatevariables, 'TemplateVars');
} else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templatevariables failed.'); }

/* create a vehicle for the category and all the things
 * we've added to it.
 */
$vehicle = $builder->createVehicle($category,$attr);

/* Add TV sources */
$vehicle->resolve('file',array(
    'source' => $sources['tv_php'],
    'target' => "return MODX_CORE_PATH . 'model/modx/processors/element/tv/renders/mgr/input/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['tv_tpl'],
    'target' => "return MODX_BASE_PATH . 'manager/templates/default/element/tv/renders/input/';",
));

/* This section transfers every file in the local 
 superboxselects/superboxselect/core directory to the
 target site's core/superboxselect directory on install.
 If the core has been renamed or moved, they will still
 go to the right place.
 */
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

/* This section transfers every file in the local
 superboxselects/superboxselect/assets directory to the
 target site's assets/superboxselect directory on install.
 If the assets dir. has been renamed or moved, they will still
 go to the right place.
 */
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));

/* Put the category vehicle (with all the stuff we added to the
 * category) into the package 
 */
$builder->putVehicle($vehicle);


/* Next-to-last step - pack in the license file, readme.txt, changelog,
 * and setup options 
 */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt')
));

/* Last step - zip up the package */
$builder->pack();

/* report how long it took */
$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(xPDO::LOG_LEVEL_INFO, "Package Built.");
$modx->log(xPDO::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
exit();
