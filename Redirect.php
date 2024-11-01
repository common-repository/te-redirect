<?php
/*
Plugin Name: TE Redirect
Description: 301 redirect plugin
Version: 0.1.5
Author: TrubinE
Author URI: http://onwp.ru
Text Domain: te-redirect
License: GPL
*/

/*  Copyright 2018  TrubinE  (email: onlajn@bk.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if (!class_exists('TeRedirect')) {
    class TeRedirect
    {
        public $pluginInfo, $pluginSettings, $pluginEvent;

        public function __construct()
        {
            include_once 'include/TeRedirectSettings.php';
            include_once 'include/TeRedirectEvent.php';
            $this->pluginSettings = new TeRedirectSettings();
            $this->pluginEvent = new TeRedirectEvent();
            $this->pluginInfo = [
                'Version' => '0.1.3',
            ];
        }

        public function start()
        {
            add_action('init', [$this->pluginEvent, 'redirect'], 1);
            add_action('admin_menu', [$this->pluginSettings, 'add_menu']);
            add_action('admin_init', [$this->pluginSettings, 'register_settings']);
            add_action('admin_init', [$this->pluginSettings, 'save_settings']);
            add_action('admin_enqueue_scripts', [$this, '_vendor']);
        }

        public function _vendor()
        {
            wp_enqueue_style(
                'te-redirect-css',
                plugins_url('css/style.css', __FILE__),
                [],
                $this->pluginInfo['Version'],
                false
            );

            wp_enqueue_script(
                'te-redirect-js',
                plugins_url('js/script.js', __FILE__),
                ['jquery'],
                $this->pluginInfo['Version'],
                true
            );

            // Localize
            $translation_array = array(
                'from' => __( 'from', 'te-redirect' ),
                'to' => __( 'to', 'te-redirect' ),
                'remove' => __( 'remove', 'te-redirect' ),
                'add_item' => __( 'Add item', 'te-redirect' ),
                'checking' => __( 'Data checking..', 'te-redirect' ),
                'error_from' => __( 'address is already in the table', 'te-redirect' ),
                'error_to' => __( 'address page is already listed as the destination page', 'te-redirect' ),
            );
            wp_localize_script( 'te-redirect-js', 'translateTeRedirect', $translation_array );
        }

    }

    (new TeRedirect())->start();
}