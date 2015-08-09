<?php
/**
 * Property Residential Details
 *
 * @author 		PropertyHive
 * @category 	Admin
 * @package 	PropertyHive/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PH_Meta_Box_Property_Address
 */
class PH_Meta_Box_Property_Residential_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
        
        echo '<div class="propertyhive_meta_box">';
        
        echo '<div class="options_group">';
        
        propertyhive_wp_text_input( array( 
            'id' => '_bedrooms', 
            'label' => __( 'Bedrooms', 'propertyhive' ), 
            'desc_tip' => false,
            'type' => 'number'
        ) );
        
        propertyhive_wp_text_input( array( 
            'id' => '_bathrooms', 
            'label' => __( 'Bathrooms', 'propertyhive' ), 
            'desc_tip' => false,
            'type' => 'number'
        ) );
        
        propertyhive_wp_text_input( array( 
            'id' => '_reception_rooms', 
            'label' => __( 'Reception Rooms', 'propertyhive' ), 
            'desc_tip' => false,
            'type' => 'number'
        ) );
        
        // Property Type
        $options = array( '' => '' );
        $args = array(
            'hide_empty' => false,
            'parent' => 0
        );
        $terms = get_terms( 'property_type', $args );
        
        $selected_value = '';
        if ( !empty( $terms ) && !is_wp_error( $terms ) )
        {
            foreach ($terms as $term)
            {
                $options[$term->term_id] = $term->name;
                
                $args = array(
                    'hide_empty' => false,
                    'parent' => $term->term_id
                );
                $subterms = get_terms( 'property_type', $args );
                
                if ( !empty( $subterms ) && !is_wp_error( $subterms ) )
                {
                    foreach ($subterms as $term)
                    {
                        $options[$term->term_id] = '- ' . $term->name;
                    }
                }
            }

            $term_list = wp_get_post_terms($post->ID, 'property_type', array("fields" => "ids"));
            
            if ( !is_wp_error($term_list) && is_array($term_list) && !empty($term_list) )
            {
                $selected_value = $term_list[0];
            }
        }
        
        $args = array( 
            'id' => 'property_type_id', 
            'label' => __( 'Property Type', 'propertyhive' ), 
            'desc_tip' => false,
            'options' => $options
        );
        if ($selected_value != '')
        {
            $args['value'] = $selected_value;
        }
        propertyhive_wp_select( $args );
        
        // Parking
        $options = array( '' => '' );
        $args = array(
            'hide_empty' => false,
            'parent' => 0
        );
        $terms = get_terms( 'parking', $args );
        
        $selected_value = '';
        if ( !empty( $terms ) && !is_wp_error( $terms ) )
        {
            foreach ($terms as $term)
            {
                $options[$term->term_id] = $term->name;
            }

            $term_list = wp_get_post_terms($post->ID, 'parking', array("fields" => "ids"));
            
            if ( !is_wp_error($term_list) && is_array($term_list) && !empty($term_list) )
            {
                $selected_value = $term_list[0];
            }
        }
        
        $args = array( 
            'id' => 'parking_id', 
            'label' => __( 'Parking', 'propertyhive' ), 
            'desc_tip' => false,
            'options' => $options
        );
        if ($selected_value != '')
        {
            $args['value'] = $selected_value;
        }
        propertyhive_wp_select( $args );
?>
        <p class="form-field"><label for="outside_space_ids"><?php _e( 'Outside Space', 'propertyhive' ); ?></label>
        <select id="outside_space_ids" name="outside_space_ids[]" multiple="multiple" data-placeholder="<?php _e( 'Select outside space', 'propertyhive' ); ?>" class="multiselect attribute_values">
            <?php
                $options = array( '' => '' );
                $args = array(
                    'hide_empty' => false,
                    'parent' => 0
                );
                $terms = get_terms( 'outside_space', $args );
                
                $selected_values = array();
                $term_list = wp_get_post_terms($post->ID, 'outside_space', array("fields" => "ids"));
                if ( !is_wp_error($term_list) && is_array($term_list) && !empty($term_list) )
                {
                    foreach ( $term_list as $term_id )
                    {
                        $selected_values[] = $term_id;
                    }
                }
                
                if ( !empty( $terms ) && !is_wp_error( $terms ) )
                {
                    foreach ( $terms as $term )
                    {
                        echo '<option value="' . esc_attr( $term->term_id ) . '"';
                        if ( in_array( $term->term_id, $selected_values ) )
                        {
                            echo ' selected';
                        }
                        echo '>' . esc_html( $term->name ) . '</option>';
                    }
                }
            ?>
        </select>
<?php
    
        do_action('propertyhive_property_residential_details_fields');
	   
        echo '</div>';
        
        echo '</div>';
           
    }

    /**
     * Save meta box data
     */
    public static function save( $post_id, $post ) {
        global $wpdb;
        
        update_post_meta( $post_id, '_bedrooms', $_POST['_bedrooms'] );
        update_post_meta( $post_id, '_bathrooms', $_POST['_bathrooms'] );
        update_post_meta( $post_id, '_reception_rooms', $_POST['_reception_rooms'] );
        
        if ( !empty($_POST['property_type_id']) )
        {
            wp_set_post_terms( $post_id, $_POST['property_type_id'], 'property_type' );
        }
        else
        {
            // Setting to blank
            wp_delete_object_term_relationships( $post_id, 'property_type' );
        }

        if ( !empty($_POST['parking_id']) )
        {
            wp_set_post_terms( $post_id, $_POST['parking_id'], 'parking' );
        }
        else
        {
            // Setting to blank
            wp_delete_object_term_relationships( $post_id, 'parking' );
        }
        
        $outside_spaces = array();
        if ( isset( $_POST['outside_space_ids'] ) && !empty( $_POST['outside_space_ids'] ) )
        {
            foreach ( $_POST['outside_space_ids'] as $outside_space_id )
            {
                $outside_spaces[] = $outside_space_id;
            }
        }
        if ( !empty($outside_spaces) )
        {
            wp_set_post_terms( $post_id, $outside_spaces, 'outside_space' );
        }
        else
        {
            wp_delete_object_term_relationships( $post_id, 'outside_space' );
        }
    }

}
