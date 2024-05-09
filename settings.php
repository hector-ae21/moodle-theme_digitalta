<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme settings
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// This is used for performance, we don't need to know about these settings on every page in Moodle, only when                      
// we are looking at the admin settings pages.                                                                                      
if ($ADMIN->fulltree) {

    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.                         
    $settings = new theme_boost_admin_settingspage_tabs('themesettingdta', get_string('configtitle', 'theme_dta'));

    // Each page is a tab - the first is the "General" tab.                                                                         
    $page = new admin_settingpage('theme_dta_general', get_string('generalsettings', 'theme_dta'));

    // Replicate the preset setting from boost.                                                                                     
    $name = 'theme_dta/preset';
    $title = get_string('preset', 'theme_dta');
    $description = get_string('preset_desc', 'theme_dta');
    $default = 'default.scss';

    // We list files in our own file area to add to the drop down. We will provide our own function to                              
    // load all the presets from the correct paths.                                                                                 
    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_dta', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets from Boost.                                                                                   
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.                                                                                                        
    $name = 'theme_dta/presetfiles';
    $title = get_string('presetfiles', 'theme_dta');
    $description = get_string('presetfiles_desc', 'theme_dta');

    $setting = new admin_setting_configstoredfile(
        $name,
        $title,
        $description,
        'preset',
        0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss'))
    );
    $page->add($setting);


    // We use an empty default value because the default colour should come from the preset.                                        
    $name = 'theme_dta/primarycolor';
    $title = get_string('config::primarycolor', 'theme_dta');
    $description = get_string('config::primarycolor_desc', 'theme_dta');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#1382c5');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_dta/secondarycolor';
    $title = get_string('config::secondarycolor', 'theme_dta');
    $description = get_string('config::secondarycolor_desc', 'theme_dta');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#062f4a');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_dta/tertiarycolor';
    $title = get_string('config::tertiarycolor', 'theme_dta');
    $description = get_string('config::tertiarycolor_desc', 'theme_dta');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#58595b');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_dta/accentcolor';
    $title = get_string('config::accentcolor', 'theme_dta');
    $description = get_string('config::accentcolor_desc', 'theme_dta');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f27b10');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_dta/accentcoloralt';
    $title = get_string('config::accentcoloralt', 'theme_dta');
    $description = get_string('config::accentcoloralt_desc', 'theme_dta');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d5490c');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!                                                                         
    $settings->add($page);

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_dta_advanced', get_string('advancedsettings', 'theme_dta'));

    // Raw SCSS to include before the content.                                                                                      
    $setting = new admin_setting_configtextarea(
        'theme_dta/scsspre',
        get_string('rawscsspre', 'theme_dta'),
        get_string('rawscsspre_desc', 'theme_dta'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.                                                                                       
    $setting = new admin_setting_configtextarea(
        'theme_dta/scss',
        get_string('rawscss', 'theme_dta'),
        get_string('rawscss_desc', 'theme_dta'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    $page = new admin_settingpage('theme_dta_navbar', get_string('config::navbar_page', 'theme_dta'));

    $page->add(new admin_setting_configcheckbox(
        'theme_dta/enabled_navbar',
        get_string('config::custom_navbar', 'theme_dta'),
        get_string('config::custom_navbar_desc', 'theme_dta'),
        1
    ));

    $page->add(new admin_setting_configtext(
        'theme_dta/navbar_teacheracademy_url',
        get_string('config::navbar_teacheracademy_url', 'theme_dta'),
        get_string('config::navbar_teacheracademy_url_desc', 'theme_dta'),
        "$CFG->wwwroot/local/dta/pages/teacheracademy/index.php",
        PARAM_URL
    ));

    $settings->hide_if('theme_dta/navbar_teacheracademy_url', 'theme_dta/enabled_navbar');

    $page->add(new admin_setting_configtext(
        'theme_dta/navbar_themes_url',
        get_string('config::themes_url', 'theme_dta'),
        get_string('config::themes_url_desc', 'theme_dta'),
        "",
        PARAM_URL
    ));

    $settings->hide_if('theme_dta/navbar_themes_url', 'theme_dta/enabled_navbar');

    $page->add(new admin_setting_configtext(
        'theme_dta/navbar_experiences_url',
        get_string('config::navbar_experiences_url', 'theme_dta'),
        get_string('config::navbar_experiences_url_desc', 'theme_dta'),
        "$CFG->wwwroot/local/dta/pages/experiences/dashboard.php",
        PARAM_URL
    ));

    $settings->hide_if('theme_dta/navbar_experiences_url', 'theme_dta/enabled_navbar');


    $page->add(new admin_setting_configtext(
        'theme_dta/navbar_cases_url',
        get_string('config::navbar_cases_url', 'theme_dta'),
        get_string('config::navbar_cases_url_desc', 'theme_dta'),
        "$CFG->wwwroot/local/dta/pages/cases/repository.php",
        PARAM_URL
    ));

    $settings->hide_if('theme_dta/navbar_cases_url', 'theme_dta/enabled_navbar');

    $page->add(new admin_setting_configtext(
        'theme_dta/navbar_resourcerepository_url',
        get_string('config::navbar_resourcerepository_url', 'theme_dta'),
        get_string('config::navbar_resourcerepository_url_desc', 'theme_dta'),
        '',
        PARAM_URL
    ));

    $settings->hide_if('theme_dta/navbar_resourcerepository_url', 'theme_dta/enabled_navbar');

    $page->add(new admin_setting_configcheckbox(
        'theme_dta/enabled_custom_usermenu',
        get_string('config::custom_usermenu', 'theme_dta'),
        get_string('config::custom_usermenu_desc', 'theme_dta'),
        1
    ));

    $page->add(new admin_setting_configtext(
        'theme_dta/profile_url',
        get_string('config::profile_url', 'theme_dta'),
        get_string('config::profile_url_desc', 'theme_dta'),
        "$CFG->wwwroot/local/dta/pages/profile/index.php",
        PARAM_TEXT
    ));

    $settings->hide_if("theme_dta/profile_url", 'theme_dta/enabled_custom_usermenu');

    $settings->add($page);
}
