<?php

add_action('wp_enqueue_scripts', 'ea_dentistas_frontend_scripts');

function ea_dentistas_frontend_scripts()
{

    $min = (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '10.0.0.3'))) ? '' : '.min';
    $state = isset($_POST['state']) ? $_POST['state'] : null;
    $listagem = ea_dentistas_get_listagem_wp();

    if (empty($min)) :
        wp_enqueue_script('ea-dentistas-livereload', 'http://localhost:35729/livereload.js?snipver=1', array(), null, true);
    endif;

    wp_register_script('list-js', EA_DENTISTAS_URL . 'assets/js/list' . $min . '.js', array('jquery'), '1.0.0', true);

    wp_register_script('ea-dentistas-script', EA_DENTISTAS_URL . 'assets/js/ea-dentistas' . $min . '.js', array('jquery', 'list-js'), '1.0.2', true);

    wp_enqueue_script('ea-dentistas-script');

    wp_localize_script('ea-dentistas-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => EA_DENTISTAS_URL,
        'listagem' => $listagem,
        'lat' => isset($_POST['lat']) ? $_POST['lat'] : null,
        'lng' => isset($_POST['lng']) ? $_POST['lng'] : null,
        'estado' => isset($_POST['estado']) ? $_POST['estado'] : null,
        'cidade' => isset($_POST['cidade']) ? $_POST['cidade'] : null

    ));
    wp_enqueue_style('ea-dentistas-style', EA_DENTISTAS_URL . 'assets/css/ea-dentistas.css', array(), false, 'all');

    $gmaps_key = ea_dentistas_get_option('gmaps_key');

    if ($gmaps_key) {
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $gmaps_key . '&libraries=places&callback=initGoogleApi&', array('ea-dentistas-script'), null,  array(
            'in_footer' => true,
            'strategy' => 'defer'
        ));
    }
}

add_action('admin_enqueue_scripts', 'ea_dentistas_admin_scripts');

function ea_dentistas_admin_scripts() {
    if (!is_user_logged_in())
        return;

    $min = (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1', '10.0.0.3'))) ? '' : '.min';

    wp_register_script('ea-dentistas-admin-script', EA_DENTISTAS_URL . 'assets/js/ea-dentistas-admin' . $min . '.js', array('jquery'), '1.0.4', true);

    wp_enqueue_script('ea-dentistas-admin-script');

    wp_localize_script('ea-dentistas-admin-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}