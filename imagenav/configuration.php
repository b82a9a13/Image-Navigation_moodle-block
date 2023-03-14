<?php
require_once(__DIR__ . '/../../config.php');

use block_imagenav\lib;

require_login();
$context = context_system::instance();
require_capability('block/imagenav:admin', $context);

$PAGE->set_url(new moodle_url('/blocks/imagenav/configuration.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('configuration', 'block_imagenav'));
$PAGE->set_heading(get_string('configuration', 'block_imagenav'));

$lib = new lib();

echo $OUTPUT->header();
echo('<link rel="stylesheet" href="./classes/css/configuration.css">');

$template = (object)[
    'config_img_nav' => get_string('config_img_nav', 'block_imagenav'),
    'admin_nav' => get_string('admin_nav', 'block_imagenav'),
    'coach_nav' => get_string('coach_nav', 'block_imagenav'),
    'learner_nav' => get_string('learner_nav', 'block_imagenav')
];
echo $OUTPUT->render_from_template('block_imagenav/nav-btns', $template);

$types = ['admin', 'coach', 'learner'];
foreach($types as $type){
    $template = (object)[
        'role' => $type,
        'roleText' => get_string($type, 'block_imagenav'),
        'navigation' => get_string('navigation', 'block_imagenav'),
        'submit' => get_string('submit', 'block_imagenav'),
        'preview' => get_string('preview', 'block_imagenav'),
        'add_new_img' => get_string('add_new_img', 'block_imagenav'),
        'img_pix_width' => get_string('img_pix_width', 'block_imagenav'),
        'img_pix_height' => get_string('img_pix_height', 'block_imagenav'),
        'keep_aspect_ratio' => get_string('keep_aspect_ratio', 'block_imagenav'),
        'input_a_url' => get_string('input_a_url', 'block_imagenav')
    ];
    $adminConfig = $lib->get_nav_config($type);
    if(!empty($adminConfig)){
        $template->width = $adminConfig->width;
        $template->height = $adminConfig->height;
        if($adminConfig->aspectratio == 1){
            $template->aspectratio = 'checked';
        }
    } else {
        $template->width = 100;
        $template->height = 100;
    }
    $adminImage = $lib->get_nav_images($type);
    if(!empty($adminImage)){
        $template->total_images = count($adminImage);
        $template->array = array_values($adminImage);
    } else {
        $template->total_images = 1;
        $template->array = array_values(array([1,'','','']));
    }
    echo $OUTPUT->render_from_template('block_imagenav/nav-div', $template);
}

echo('<script src="./classes/js/configuration.js"></script>');
echo $OUTPUT->footer();