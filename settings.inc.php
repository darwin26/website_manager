<?php

// this addons will be reinstalled if new website is added
$REX['ADDON']['website_manager']['settings']['reinstall_addons'] = array(); // e.g.: array('image_manager', 'metainfo');

// this plugins will be reinstalled if new website is added
$REX['ADDON']['website_manager']['settings']['reinstall_plugins'] = array(); // e.g.: array(array('be_utilities', 'hide_startarticle'), )

// if false if admins won't be allowed to delete websites
$REX['ADDON']['website_manager']['settings']['allow_website_delete'] = true;

// if true link to frontend will be shown in meta menu of redaxo backend
$REX['ADDON']['website_manager']['settings']['show_metamenu_frontend_link'] = true;

// if true name of the website including a link to the frontend will be shown in redaxo header
$REX['ADDON']['website_manager']['settings']['show_website_name_frontend_link'] = true;

// if true a color bar will be shown in redaxo header
$REX['ADDON']['website_manager']['settings']['show_color_bar'] = true;

// if true favicon will be colorized too
$REX['ADDON']['website_manager']['settings']['colorize_favicon'] = true;

