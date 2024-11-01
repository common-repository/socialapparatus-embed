<?php
/**
 * Plugin Name: SocialApparatus Embed Plugin
 * Plugin URI: http://socia.us
 * Description: Allows the embedding of a SocialApparatus social network into your Wordpress Site.
 * Version: 1.0
 * Author: Shane Barron | SocialApparatus
 * Author URI: http://sbarron.com
 * License: GPL2
 */
/*  Copyright 2014  Shane Barron  (email : clifton@sbarron.com)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

add_action("admin_menu", "socia_admin_actions");
add_shortcode("socia_embed", "socia_embed_shortcode");

function socia_admin_actions() {
    add_options_page('SocialApparatus Embed Code', 'SocialApparatus', 'manage_options', __FILE__, 'socia_admin_page');
}

function socia_admin_page() {
    if (isset($_POST['socia_site_id'])) {
        update_option('socia_site_id', $_POST['socia_site_id']);
        unset($_POST['socia_site_id']);
    }

    $socia_site_id = get_option('socia_site_id');
    ?>

    <div class="wrap">
        <h2>SocialApparatus Configuration</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="blogname">Site Code</label></th>
                <td>
                    <form action="" method="post">
                        <input name="socia_site_id" type="text" id="blogname" class="regular-text" value="<?php echo $socia_site_id; ?>" />
                        <input type="submit" value="Save">
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

function socia_embed_shortcode($atts) {
    extract(shortcode_atts(array(
        'margin' => '10px',
        'float' => 'none',
        'width' => '100%',
        'height' => '380px'
                    ), $atts, 'socia_embed'));
    $socia_site_id = get_option('socia_site_id');
    $return = "<script id='socia_embed' data-id='$socia_site_id' data-margin='$margin' data-float='$float' data-width='$width' data-height='$height' src='//socia.us/source.js'></script>";
    return $return;
}

class Socia_Embed_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
                'socia_embed_widget', // Base ID
                __('SocialApparatus Embedded Social Network', 'text_domain'), // Name
                array('description' => __('Embeds a Social Apparatus Social Network', 'text_domain'),) // Args
        );
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $height = apply_filters('widget_height', $instance['height']);
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        $socia_site_id = get_option('socia_site_id');
        if (!$height) {
            $height = '380px';
        }
        echo "<script id='socia_embed' data-id='$socia_site_id' data-height='$height' data-width='100%' data-margin='0px' src='//socia.us/source.js'></script>";
        echo $args['after_widget'];
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('My Community', 'text_domain');
        }
        if (isset($instance['height'])) {
            $height = $instance['height'];
        } else {
            $height = 300;
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height: in pixels'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($height); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['height'] = (!empty($new_instance['height'])) ? strip_tags($new_instance['height']) : '';
        return $instance;
    }

}

add_action('widgets_init', function() {
    register_widget('Socia_Embed_Widget');
});
