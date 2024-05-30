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
 * Theme functions
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_dta_get_main_scss_content($theme)
{
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();

    // Get all colors from the settings
    $colors = [
        'primarycolor' => get_config('theme_dta', 'primarycolor'),
        'secondarycolor' => get_config('theme_dta', 'secondarycolor'),
        'tertiarycolor' => get_config('theme_dta', 'tertiarycolor'),
        'accentcolor' => get_config('theme_dta', 'accentcolor'),
        'accentcoloralt' => get_config('theme_dta', 'accentcoloralt'),
    ];

    if (!empty($colors)) {
        foreach ($colors as $key => $color) {
            if (!empty($color)) {
                $scss .= '$' . $key . ': ' . $color . ";\n";
            }
        }
    }


    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.                      
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.                      
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_dta', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_dta and not theme_dta (see the line above).                
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.                                                                                
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.                                        
    $pre = file_get_contents($CFG->dirroot . '/theme/dta/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.                                    
    $post = "";
    $post_filenames = glob($CFG->dirroot . '/theme/dta/scss/post-*.scss');
    foreach ($post_filenames as $post_filename) {
        $post .= file_get_contents($post_filename);
    }

    // Combine them together.                                                                                                       
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Initialises the theme.
 */
function theme_dta_page_init()
{
    redirect_login_is_not_loggedin();
    redirect_is_not_allowed_page();
}

/**
 * Sets the default primarynav sections.
 */
function set_default_primarynav_sections()
{
    global $PAGE;
    $sections = [
        'siteadminnode' => 'i/settings',
    ];
    foreach ($sections as $section => $icon) {
        /**
         * @var $node navigation_node
         */
        $node = $PAGE->primarynav->get($section);
        if (!empty($node)) {
            $node->icon = new pix_icon($icon, '');
            $node->text = get_string('navbar::siteadmin', 'theme_dta');
        }
    }
}

/**
 * Sets the default primarynav usermenu.
 */
function set_default_primarynav_usermenu()
{
    global $PAGE;
    $usermenu = $PAGE->primarynav->get('user');
    if (!empty($usermenu)) {
        $usermenu->text = get_string('login::login', 'theme_dta');
    }
}

/**
 * Sets aditional primarynav sections.
 */
function set_aditional_primarynav_sections()
{
    global $PAGE;
    if (isloggedin() && !isguestuser() && get_config('theme_dta', 'enabled_navbar')) {
        $payload = get_sections_details();
        foreach ($payload as $key => $content) {
            if (!empty($content['link'])) {
                $node = create_navigation_node(
                    $content['label'],
                    new moodle_url($content['link']),
                    $content['node_key'],
                    new pix_icon($content['icon'], ''),
                );
                $siteAdminNode = $PAGE->primarynav->get('siteadminnode');
                if (isset($siteAdminNode)) {
                    $PAGE->primarynav->add_node($node, 'siteadminnode');
                } else {
                    $PAGE->primarynav->add_node($node);
                }
            }
        }
    }
}

/**
 * Creates a navigation node.
 *
 * @param  string          $str_title The title of the node
 * @param  moodle_url      $redirect_url The url to redirect
 * @param  string          $node_key The key of the node
 * @param  pix_icon        $pix_icon The icon of the node
 * @return navigation_node The navigation node
 */
function create_navigation_node($str_title, $redirect_url, $node_key, $pix_icon)
{
    return navigation_node::create(
        $str_title,
        $redirect_url,
        navigation_node::TYPE_SETTING,
        null,
        $node_key,
        $pix_icon
    );
}

/**
 * Gets the sections details.
 *
 * @return array The sections details
 */
function get_sections_details()
{
    global $CFG;
    return [
        'home' => [
            'label' => get_string('navbar::teacheracademy', 'theme_dta'),
            'icon' => 'i/home',
            'node_key' => 'home',
            'link' => get_config('theme_dta', 'navbar_teacheracademy_url'),
        ],
        'experiences' => [
            'label' => get_string('navbar::experiences', 'theme_dta'),
            'icon' => 'i/courseevent',
            'node_key' => 'experiences',
            'link' => get_config('theme_dta', 'navbar_experiences_url'),
        ],
        'cases' => [
            'label' => get_string('navbar::cases', 'theme_dta'),
            'icon' => null,
            'node_key' => 'cases',
            'link' => get_config('theme_dta', 'navbar_cases_url'),
        ],
        'resourcerepository' => [
            'label' => get_string('navbar::resourcerepository', 'theme_dta'),
            'icon' => 'i/open',
            'node_key' => 'resourcerepository',
            'link' => get_config('theme_dta', 'navbar_resourcerepository_url'),
        ],
        'themes' => [
            'label' => get_string('navbar::themes', 'theme_dta'),
            'icon' => 't/tags',
            'node_key' => 'themes',
            'link' => get_config('theme_dta', 'navbar_themes_url'),
        ],
        'chat' => [
            'label' => "", 
            'icon' => 't/messages',
            'node_key' => 'themes',
            'link' => "$CFG->wwwroot/local/dta/pages/chat/index.php"
        ],
    ];
}

/**
 * Redirects to login page if user is not logged in.
 */
function redirect_login_is_not_loggedin()
{
    global $PAGE;

    $no_redirect_page_types = [
        'login-',
        'password',
    ];

    if (defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
        return;
    }
    foreach ($no_redirect_page_types as $type) {
        if (strpos($PAGE->pagetype, $type) !== false) {
            return;
        }
    }

    if (!isloggedin() || isguestuser()) {
        $loginurl = new moodle_url('/login/index.php');
        redirect($loginurl);
    }
}

/**
 * Redirects to the home page if the user is not allowed to access the page.
 */
function redirect_is_not_allowed_page()
{
    global $PAGE;

    $home_url = get_config('theme_dta', "navbar_teacheracademy_url");

    if (empty($home_url) || !isloggedin() || isguestuser()) {
        return;
    }
    $redirect_url = new moodle_url($home_url);


    $no_redirect_page_types = [
        'admin-',
        'local-dta',
        'edit',
        'editadvanced'
    ];

    $profile_url = get_config('theme_dta', "profile_url");
    if (!empty($profile_url) && strpos($PAGE->pagetype, 'profile') !== false) {
        $redirect_url = new moodle_url($profile_url, ['id' => $PAGE->url->params()["id"]]);
    }


    foreach ($no_redirect_page_types as $type) {
        if (strpos($PAGE->pagetype, $type) !== false) {
            return;
        }
    }
    redirect($redirect_url);
}
