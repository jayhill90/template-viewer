<?php
/**
 * Plugin Name:     Template Viewer
 * Plugin URI:      https://wpdev.life
 * Description:     lets authors easily see which template a page is using and also see only pages using a particular template.
 * Author:          Jay Hill
 * Author URI:      https://wpdev.life
 * Text Domain:     template-viewer
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package Template_Viewer
 */

namespace WPEngine\TemplateViewer;

define(__NAMESPACE__ . '\PATH', plugin_dir_path(__FILE__));

spl_autoload_register(
    function ( $class ) {
        $base_dir = __DIR__ . '/inc/';
        $len = strlen(__NAMESPACE__);
        if (strncmp(__NAMESPACE__, $class, $len) !== 0 ) {
            return;
        }
        // Remove the namespace prefix.
        // Replace namespace separators with directory separators in the class name.
        // Replace underscores with dashes in the class name.
        // Append with .php extension.
        $class_file_name = str_replace([ '\\', '_' ], [ '/', '-' ], strtolower(substr($class, $len + 1))) . '.php';
        // Add `class-` to file name so we meet WPCS standards.
        $class_file_name = preg_replace('/([\w-]+)\.php/', 'class-$1.php', $class_file_name);
        $file = $base_dir . $class_file_name;
        // If the file exists, require it.
        if (file_exists($file) ) {
            include $file;
        }
    }
);

function activated() 
{
    //add filters and things on activation
}

register_activation_hook(__FILE__, __NAMESPACE__ . '\activated');


function deactivated() 
{
    // remove filters and things on deactivation
}

register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivated');

add_action(
    'plugins_loaded',
    function () {
        TemplateViewer::init();
    }
)

?>