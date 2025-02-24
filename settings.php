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
 * @package   theme_digitalta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
                                                                                   
if ($ADMIN->fulltree) {
             
    $settings = new theme_boost_admin_settingspage_tabs('themesettingdigitalta', get_string('configtitle', 'theme_digitalta'));

    $page = new admin_settingpage('theme_digitalta_navbar', get_string('config:navbar_page', 'theme_digitalta'));

    $page->add(new admin_setting_configcheckbox(
        'theme_digitalta/enabled_navbar',
        get_string('config:custom_navbar', 'theme_digitalta'),
        get_string('config:custom_navbar_desc', 'theme_digitalta'),
        1
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_digitalta/enabled_custom_usermenu',
        get_string('config:custom_usermenu', 'theme_digitalta'),
        get_string('config:custom_usermenu_desc', 'theme_digitalta'),
        1
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_digitalta/survey_link_enabled',
        get_string('config:survey_link_enabled', 'theme_digitalta'),
        get_string('config:survey_link_enabled_desc', 'theme_digitalta'),
        0
    ));

    $page->add(new admin_setting_configtext(
        'theme_digitalta/survey_link_url',
        get_string('config:survey_link_url', 'theme_digitalta'),
        get_string('config:survey_link_url_desc', 'theme_digitalta'),
        null,
        PARAM_URL
    ));

    $settings->add($page);
    $page = new admin_settingpage('theme_digitalta_colors', get_string('config:colors_page', 'theme_digitalta'));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_digital_technology',
        get_string('theme:digital_technology', 'local_digitalta'),
        '',
        '#F95C5A'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_classroom_management',
        get_string('theme:classroom_management', 'local_digitalta'),
        '',
        '#FFB300'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_communication_and_relationship_building',
        get_string('theme:communication_and_relationship_building', 'local_digitalta'),
        '',
        '#9D90CD'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_diversity_and_inclusion',
        get_string('theme:diversity_and_inclusion', 'local_digitalta'),
        '',
        '#4074D7'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_professional_collaboration_and_development',
        get_string('theme:professional_collaboration_and_development', 'local_digitalta'),
        '',
        '#33C189'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_school_culture',
        get_string('theme:school_culture', 'local_digitalta'),
        '',
        '#2B6F03'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_curriculum_planning_and_development',
        get_string('theme:curriculum_planning_and_development', 'local_digitalta'),
        '',
        '#61DFD8'
    ));

    $page->add(new admin_setting_configcolourpicker(
        'theme_digitalta/color_theme_others',
        get_string('theme:others', 'local_digitalta'),
        '',
        '#B08217'
    ));

    $settings->add($page);

}
