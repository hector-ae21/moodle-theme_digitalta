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
 * @package   theme_digitalta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/theme/digitalta/locallib.php');

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_digitalta_get_pre_scss($theme) {
    global $CFG;
    $pre = "";
    $pre .= theme_boost_get_pre_scss(theme_config::load('boost'));
    $pre .= file_get_contents($CFG->dirroot . '/theme/digitalta/scss/pre.scss');
    return $pre;
}


/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_digitalta_get_extra_scss($theme) {
    global $CFG;
    $post = "";
    $post .= theme_boost_get_extra_scss(theme_config::load('boost'));
    $post_filenames = glob($CFG->dirroot . '/theme/digitalta/scss/post-*.scss');
    foreach ($post_filenames as $post_filename) {
        $post .= file_get_contents($post_filename);
    }
    return $post;
}

/**
 * Initialises the theme.
 */
function theme_digitalta_page_init(moodle_page $page)
{
    redirect_login_is_not_loggedin();
    redirect_is_not_allowed_page();
    if (get_config('theme_digitalta', 'survey_link_enabled')
            && ($survey_link = get_config('theme_digitalta', 'survey_link_url'))
            && !isguestuser()
            && isloggedin()) {
        $page->requires->js_call_amd("theme_digitalta/survey", "init", [$survey_link]);
    }
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
            $node->text = get_string('navbar:siteadmin', 'theme_digitalta');
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
        $usermenu->text = get_string('login:login', 'theme_digitalta');
    }
}

/**
 * Sets aditional primarynav sections.
 */
function set_aditional_primarynav_sections()
{
    global $PAGE;
    if (isloggedin() && !isguestuser() && get_config('theme_digitalta', 'enabled_navbar')) {
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
                if (isset($siteAdminNode) && !empty($siteAdminNode)) {
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
    return [
        'home' => [
            'label' => get_string('navbar:home', 'theme_digitalta'),
            'icon' => 'i/home',
            'node_key' => 'home',
            'link' => THEME_DIGITALTA_NAVBAR_HOME,
        ],
        'experiences' => [
            'label' => get_string('concept:experiences', 'local_digitalta'),
            'icon' => 'i/courseevent',
            'node_key' => 'experiences',
            'link' => THEME_DIGITALTA_NAVBAR_EXPERIENCES,
        ],
        'cases' => [
            'label' => get_string('concept:cases', 'local_digitalta'),
            'icon' => null,
            'node_key' => 'cases',
            'link' => THEME_DIGITALTA_NAVBAR_CASES,
        ],
        'resources' => [
            'label' => get_string('concept:resources', 'local_digitalta'),
            'icon' => 'i/open',
            'node_key' => 'resources',
            'link' => THEME_DIGITALTA_NAVBAR_RESOURCES,
        ],
        'themestags' => [
            'label' => get_string('concept:themestags', 'local_digitalta'),
            'icon' => 't/tags',
            'node_key' => 'themestags',
            'link' => THEME_DIGITALTA_NAVBAR_THEMESTAGS,
        ],
        'tutorsmentors' => [
            'label' => get_string('concept:tutorsmentors', 'local_digitalta'),
            'icon' => 'i/user',
            'node_key' => 'tutorsmentors',
            'link' => THEME_DIGITALTA_NAVBAR_TUTORSMENTORS,
        ],
        'chat' => [
            'label' => get_string('navbar:chat', 'theme_digitalta'), 
            'icon' => 't/messages',
            'node_key' => 'chat',
            'link' => THEME_DIGITALTA_NAVBAR_CHAT
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

    $home_url = THEME_DIGITALTA_NAVBAR_HOME;

    if (empty($home_url) || !isloggedin() || isguestuser()) {
        return;
    }
    $redirect_url = new moodle_url($home_url);


    $no_redirect_page_types = [
        'admin-',
        'local-digitalta',
        'edit',
        'editadvanced',
        'mod-scheduler'
    ];

    if (strpos($PAGE->pagetype, 'profile') !== false) {
        $redirect_url = new moodle_url(THEME_DIGITALTA_NAVBAR_PROFILE, ['id' => $PAGE->url->params()["id"]]);
    }

    foreach ($no_redirect_page_types as $type) {
        if (strpos($PAGE->pagetype, $type) !== false) {
            return;
        }
    }
    redirect($redirect_url);
}
