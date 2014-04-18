<?php

/**
 * Plugin Name: Biodget
 * Plugin URI: https://github.com/hansagastyra/biodget
 * Description: Biodget is a WordPress plugin that enable you to use a widget to show your biography and gravatar.
 * Version: 0.0.1
 * Author: Hans Agastyra
 * Author URI: http://hansagastyra.esy.es/
 * License: GPLv2
 */

function biodget_active(){
    global $wp_version;
    
    if( version_compare( $wp_version, '3.4', '<' ) ){
        wp_die( 'Sorry, this plugin requires WordPress 3.4 or higher.' );
    }
}
register_activation_hook( __FILE__, 'biodget_activation' );

function biodget_deactive(){
    //Something that executed when user deactivate the plugin
}
register_deactivation_hook( __FILE__, 'biodget_deactive');

function biodget_scripts(){
    wp_enqueue_style('biodget', plugins_url('css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'biodget_scripts');

class Biodget extends WP_Widget{
    public function __construct(){
        parent::__construct('biodget', __( 'Biodget', 'biodget' ), array(
            'description'   =>  __( 'Biodget will enable you to show your biography and gravatar.', 'biodget' )
        ) );
    }
    
    public function form( $instance ){
        $defaults = array(
            'title'     => __( 'My Biography', 'biodget' ),
            'username'  => '',
            'avatar'    => 'off'
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = $instance['title'];
        $username = $instance['username'];
        $avatar = $instance['avatar'];
        $avatar_size = $instance['avatar_size'];
        
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat"
                       id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>"
                       type="text"
                       value="<?php echo esc_attr($title); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username/Nickname:'); ?></label>
                <input class="widefat"
                       id="<?php echo $this->get_field_id('username'); ?>"
                       name="<?php echo $this->get_field_name('username'); ?>"
                       type="text"
                       value="<?php echo esc_attr($username); ?>" />
            </p>
            <p>
                <input class="widefat"
                       id="<?php echo $this->get_field_id('avatar'); ?>"
                       name="<?php echo $this->get_field_name('avatar'); ?>"
                       type="checkbox"
                       <?php checked($avatar, 'on'); ?> />
                <label for="<?php echo $this->get_field_id('avatar'); ?>"><?php _e('Show avatar'); ?></label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php _e('Avatar size:'); ?></label>
                <input class="widefat"
                       id="<?php echo $this->get_field_id('avatar_size'); ?>"
                       name="<?php echo $this->get_field_name('avatar_size'); ?>"
                       type="text"
                       value="<?php echo esc_attr($avatar_size); ?>" />
            </p>
        <?php
    }
    
    public function update( $new_instance, $old_instance ){
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['username'] = sanitize_text_field($new_instance['username']);
        $instance['avatar'] = $new_instance['avatar'];
        $instance['avatar_size'] = absint( intval( $new_instance['avatar_size'] ) );
        return $instance;
    }
    
    public function widget( $args, $instance ){
        extract($args);
        
        echo $before_widget;
        
        $title = apply_filters('widget_title', $instance['title']);
        $username = $instance['username'];
        $user = get_user_by('slug', $username);
        $avatar = $instance['avatar'];
        $avatar_size = $instance['avatar_size'];
        
        if(!empty($title)){
            echo $before_title . $title . $after_title;
        }
        ?>
            <div id="biodget-container" class="biodget-area">
                <?php if($avatar === 'on') : ?>
                    <div class="biodget-avatar">
                        <?php echo get_avatar( $user->user_email, $avatar_size); ?>
                    </div>
                <?php endif; ?>
                <div class="biodget-bio">
                    <p>
                        <?php the_author_meta('description', $user->ID); ?>
                    </p>
                </div>
            </div>
        <?php
        
        echo $after_widget; 
    }
}

function biodget_register(){
    register_widget('Biodget');
}
add_action( 'widgets_init', 'biodget_register' );

