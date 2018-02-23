<?php
namespace Kigo\Themes\instaparent;

abstract class WidgetBase extends \WP_Widget {

    /**
     * 
     * @param array $params array to configure the widget array( $base_id, $visible_name, $description, $text_domain )
     */
    function __construct($params) {
        parent::__construct(
                $params['base_id'], // Base ID
                __($params['visible_name'], $params['textdomain']), // Name
                array('description' => __($params['description'], $params['textdomain']),) // Args
        );
    }

    /**
     *
     * @param array $instance widget fields array
     */
    function form($ins) {
        $reflect = new \ReflectionClass($this);
        if (file_exists(rtrim($reflect->getFileName(), '.php') . '-form.php'))
            include rtrim($reflect->getFileName(), '.php') . '-form.php';
    }

    /**
     *
     * @param array $args widgets inherited vars
     * @param array  $instance widget fields array
     */
    function widget($args, $ins) {
        extract($args);

        echo $before_widget;

        $reflect = new \ReflectionClass($this);
        if (file_exists(rtrim($reflect->getFileName(), '.php') . '-template.php'))
            include rtrim($reflect->getFileName(), '.php') . '-template.php';
        echo $after_widget;

    }

    /**
     *
     * @param type $old_instance old widget fields array
     * @param type $instance new widget fields array
     */
    function update($ins, $old_ins) {
        return $ins;
    }

}
