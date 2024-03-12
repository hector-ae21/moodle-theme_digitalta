<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.

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
    $post = file_get_contents($CFG->dirroot . '/theme/dta/scss/post.scss');

    // Combine them together.                                                                                                       
    return $pre . "\n" . $scss . "\n" . $post;
}

function theme_dta_page_init()
{
    redirect_login_is_not_loggedin();
    redirect_is_not_allowed_page();
}

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

function set_default_primarynav_usermenu()
{
    global $PAGE;
    $usermenu = $PAGE->primarynav->get('user');
    if (!empty($usermenu)) {
        $usermenu->text = get_string('login::login', 'theme_dta');
    }
}


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
                    new pix_icon($content['icon'], '')
                );
                $PAGE->primarynav->add_node($node, $content['before_key']);
            }
        }
    }
}
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

function get_sections_details()
{
    return [
        'home' => [
            'label' => get_string('navbar::teacheracademy', 'theme_dta'),
            'icon' => 'i/home',
            'node_key' => 'home',
            'link' => get_config('theme_dta', 'navbar_teacheracademy_url'),
            'before_key' => "siteadminnode",
        ],
        'themes' => [
            'label' => get_string('navbar::themes', 'theme_dta'),
            'icon' => 't/tags',
            'node_key' => 'themes',
            'link' => get_config('theme_dta', 'navbar_themes_url'),
            'before_key' => "siteadminnode",
        ],
        'experiences' => [
            'label' => get_string('navbar::experiences', 'theme_dta'),
            'icon' => 'i/courseevent',
            'node_key' => 'experiences',
            'link' => get_config('theme_dta', 'navbar_experiences_url'),
            'before_key' => "siteadminnode",
        ],
        'cases' => [
            'label' => get_string('navbar::ourcases', 'theme_dta'),
            'icon' => 'i/open',
            'node_key' => 'ourcases',
            'link' => get_config('theme_dta', 'navbar_ourcases_url'),
            'before_key' => "siteadminnode",
        ],
        'resourcerepository' => [
            'label' => get_string('navbar::resourcerepository', 'theme_dta'),
            'icon' => 'i/repository',
            'node_key' => 'resourcerepository',
            'link' => get_config('theme_dta', 'navbar_resourcerepository_url'),
            'before_key' => "siteadminnode",
        ],
    ];
}

function redirect_login_is_not_loggedin()
{
    global $PAGE;

    if ($PAGE->pagetype === 'login-index' || $PAGE->pagetype === 'login-logout' || defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
        return;
    }

    if (!isloggedin() || isguestuser()) {
        $loginurl = new moodle_url('/login/index.php');
        redirect($loginurl);
    }
}

function redirect_is_not_allowed_page()
{
    global $PAGE;

    $homeUrl = get_config('theme_dta', "navbar_teacheracademy_url");

    if (empty($homeUrl)) {
        return;
    }
    $redirecturl = new moodle_url($homeUrl);

    if (!isloggedin() || isguestuser() || defined('AJAX_SCRIPT') && AJAX_SCRIPT || $PAGE->url->compare($redirecturl, URL_MATCH_BASE)) {
        return;
    }

    $no_redirect_page_types = [
        'admin-',
        'local-dta',
        'user-profile'
    ];

    foreach ($no_redirect_page_types as $type) {
        if (strpos($PAGE->pagetype, $type) === 0) {
            return;
        }
    }
    redirect($redirecturl);
}
