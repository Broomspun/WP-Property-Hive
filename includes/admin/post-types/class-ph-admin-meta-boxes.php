<?php
/**
 * PropertyHive Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types)
 *
 * @author 		BIOSTA::
 * @category 	Admin
 * @package 	PropertyHive/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PH_Admin_Meta_Boxes
 */
class PH_Admin_Meta_Boxes {

	private static $meta_box_errors = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Property Meta Boxes
		add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Address::save', 10, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Owner::save', 12, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Record_Details::save', 13, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Coordinates::save', 70, 2 );
        
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Department::save', 15, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Residential_Details::save', 20, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Residential_Lettings_details::save', 25, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Residential_Sales_details::save', 30, 2 );
        
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Marketing::save', 35, 2 );
        
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Rooms::save', 40, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Features::save', 45, 2 );
        
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Photos::save', 50, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Floorplans::save', 55, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Brochures::save', 65, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Epcs::save', 70, 2 );
        add_action( 'propertyhive_process_property_meta', 'PH_Meta_Box_Property_Virtual_Tours::save', 75, 2 );
        
        // Save Contact Meta Boxes
        add_action( 'propertyhive_process_contact_meta', 'PH_Meta_Box_Contact_Correspondence_Address::save', 10, 2 );
        add_action( 'propertyhive_process_contact_meta', 'PH_Meta_Box_Contact_Contact_Details::save', 15, 2 );
        
        // Save Enquiry Meta Boxes
        add_action( 'propertyhive_process_enquiry_meta', 'PH_Meta_Box_Enquiry_Record_Details::save', 10, 2 );
        add_action( 'propertyhive_process_enquiry_meta', 'PH_Meta_Box_Enquiry_Details::save', 15, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option
	 */
	public function save_errors() {
		update_option( 'propertyhivemeta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'propertyhive_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="propertyhive_errors" class="error fade">';
			foreach ( $errors as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';

			// Clear
			delete_option( 'propertyhive_meta_box_errors' );
		}
	}

	/**
	 * Add PH Meta boxes
	 */
	public function add_meta_boxes() {
	    
        global $tabs, $post;
        
		// PROPERTY
		$tabs = array();
        
        add_meta_box( 'propertyhive-property-address', __( 'Property Address', 'propertyhive' ), 'PH_Meta_Box_Property_Address::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-owner', __( 'Property Owner / Landlord', 'propertyhive' ), 'PH_Meta_Box_Property_Owner::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-record-details', __( 'Record Details', 'propertyhive' ), 'PH_Meta_Box_Property_Record_Details::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-coordinates', __( 'Property Location', 'propertyhive' ), 'PH_Meta_Box_Property_Coordinates::output', 'property', 'normal', 'high' );
        $tabs[] = array(
            'name' => __( 'Summary', 'propertyhive' ),
            'id' => 'tab_address', 
            'metabox_ids' => array('propertyhive-property-address', 'propertyhive-property-owner', 'propertyhive-property-record-details', 'propertyhive-property-coordinates'),
            'post_type' => 'property'
        );
        
        add_meta_box( 'propertyhive-property-department', __( 'Property Department', 'propertyhive' ), 'PH_Meta_Box_Property_Department::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-residential-sales-details', __( 'Residential Sales Details', 'propertyhive' ), 'PH_Meta_Box_Property_Residential_Sales_Details::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-residential-lettings-details', __( 'Residential Lettings Details', 'propertyhive' ), 'PH_Meta_Box_Property_Residential_Lettings_Details::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-residential-details', __( 'Residential Details', 'propertyhive' ), 'PH_Meta_Box_Property_Residential_Details::output', 'property', 'normal', 'high' );
        $tabs[] = array(
            'name' => __( 'Details', 'propertyhive' ),
            'id' => 'tab_details', 
            'metabox_ids' => array('propertyhive-property-department', 'propertyhive-property-residential-sales-details', 'propertyhive-property-residential-lettings-details', 'propertyhive-property-residential-details'),
            'post_type' => 'property'
        );
        
        add_meta_box( 'propertyhive-property-marketing', __( 'Property Marketing', 'propertyhive' ), 'PH_Meta_Box_Property_Marketing::output', 'property', 'normal', 'high' );
        $tabs[] = array(
            'name' => __( 'Marketing', 'propertyhive' ),
            'id' => 'tab_marketing', 
            'metabox_ids' => array('propertyhive-property-marketing'),
            'post_type' => 'property'
        );
        
        add_meta_box( 'propertyhive-property-features', __( 'Property Features', 'propertyhive' ), 'PH_Meta_Box_Property_Features::output', 'property', 'normal' );
        add_meta_box( 'postexcerpt', __( 'Property Summary Description', 'propertyhive' ), 'PH_Meta_Box_Property_Summary_Description::output', 'property', 'normal' );
		add_meta_box( 'propertyhive-property-rooms', __( 'Property Rooms', 'propertyhive' ), 'PH_Meta_Box_Property_Rooms::output', 'property', 'normal' );
        $tabs[] = array(
            'name' => __( 'Descriptions', 'propertyhive' ),
            'id' => 'tab_descriptions', 
            'metabox_ids' => array('propertyhive-property-features', 'postexcerpt', 'propertyhive-property-rooms'),
            'post_type' => 'property'
        );
        
        add_meta_box( 'propertyhive-property-photos', __( 'Property Photos', 'propertyhive' ), 'PH_Meta_Box_Property_Photos::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-floorplans', __( 'Property Floorplans', 'propertyhive' ), 'PH_Meta_Box_Property_Floorplans::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-brochures', __( 'Property Brochures', 'propertyhive' ), 'PH_Meta_Box_Property_Brochures::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-epcs', __( 'Property EPCs', 'propertyhive' ), 'PH_Meta_Box_Property_Epcs::output', 'property', 'normal', 'high' );
        add_meta_box( 'propertyhive-property-virtual-tours', __( 'Property Virtual Tours', 'propertyhive' ), 'PH_Meta_Box_Property_Virtual_Tours::output', 'property', 'normal', 'high' );
        $tabs[] = array(
            'name' => __( 'Media', 'propertyhive' ),
            'id' => 'tab_media', 
            'metabox_ids' => array('propertyhive-property-photos', 'propertyhive-property-floorplans', 'propertyhive-property-brochures', 'propertyhive-property-epcs', 'propertyhive-property-virtual-tours'),
            'post_type' => 'property'
        );
        
        //add_meta_box( 'propertyhive-property-notes', __( 'Property Notes', 'propertyhive' ), 'PH_Meta_Box_Property_Notes::output', 'property', 'side' );
        
        // CONTACT
        add_meta_box( 'propertyhive-contact-correspondence-address', __( 'Correspondence Address', 'propertyhive' ), 'PH_Meta_Box_Contact_Correspondence_Address::output', 'contact', 'normal', 'high' );
        add_meta_box( 'propertyhive-contact-contact-details', __( 'Contact Details', 'propertyhive' ), 'PH_Meta_Box_Contact_Contact_Details::output', 'contact', 'normal', 'high' );
        $tabs[] = array(
            'name' => __( 'Contact Details', 'propertyhive' ),
            'id' => 'tab_contact_details', 
            'metabox_ids' => array('propertyhive-contact-correspondence-address', 'propertyhive-contact-contact-details'),
            'post_type' => 'contact'
        );
        
        if (!is_null($post) && get_post_status($post->ID) != 'auto-draft')
        {
            add_meta_box( 'propertyhive-contact-relationships', __( 'Relationships', 'propertyhive' ), 'PH_Meta_Box_Contact_Relationships::output', 'contact', 'normal', 'high' );
            $tabs[] = array(
                'name' => __( 'Relationships', 'propertyhive' ),
                'id' => 'tab_contact_relationships', 
                'metabox_ids' => array('propertyhive-contact-relationships'),
                'post_type' => 'contact'
            );
        }

        // ENQUIRY
        add_meta_box( 'propertyhive-enquiry-record-details', __( 'Record Details', 'propertyhive' ), 'PH_Meta_Box_Enquiry_Record_details::output', 'enquiry', 'normal', 'high' );
        add_meta_box( 'propertyhive-enquiry-details', __( 'Enquiry Details', 'propertyhive' ), 'PH_Meta_Box_Enquiry_details::output', 'enquiry', 'normal', 'high' );
        $tabs[] = array(
            'name' => __( 'Details', 'propertyhive' ),
            'id' => 'tab_enquiry_details', 
            'metabox_ids' => array('propertyhive-enquiry-record-details', 'propertyhive-enquiry-details'),
            'post_type' => 'enquiry'
        );
        
        // TO DO: move this so it works when in one column
        add_action( 'edit_form_after_title', array( $this, 'draw_meta_box_tabs' ), 31, 1);
    }
    
    /**
     * Draw meta box tabs
     */
    public function draw_meta_box_tabs() {
        
        global $post, $tabs;
        
        if (!empty($tabs))
        {
            $meta_boxes_under_tabs = array();
            
            $i = 0;
            echo '<div id="propertyhive_metabox_tabs" style="margin-top:15px">';
            foreach ($tabs as $tab)
            {
                if (isset($tab['post_type']) && $post->post_type == $tab['post_type'])
                {
                    echo '<a href="#' . implode("|#", $tab['metabox_ids']) . '" id="' . $tab['id'] . '" class="button' . ( ($i == 0) ? ' button-primary' : '') . '">' . $tab['name'] . '</a> ';
                    
                    $meta_boxes_under_tabs[] = $tab['metabox_ids'];
                    
                    ++$i;
                }
            }
            echo '</div><br>';
            
            if (!empty($meta_boxes_under_tabs))
            {
                echo '
                <script>
                    var meta_boxes_under_tabs = ' . json_encode($meta_boxes_under_tabs) . ';
                    
                    jQuery(document).ready(function()
                    {
                        // Hide all on page load
                        hide_meta_box_tabs();
                        
                        // Show first meta box
                        //jQuery(\'#\' + meta_boxes_under_tabs[0][0] + \'\').show();
                        for (var i in meta_boxes_under_tabs[0])
                        {
                            jQuery(\'#\' + meta_boxes_under_tabs[0][i] + \'\').show();
                        }
                        //jQuery(\'#propertyhive_metabox_tabs a:first-child\').trigger(\'click\');
                        
                        // Set first button as primary
                        jQuery(\'#propertyhive_metabox_tabs a:first-child\').addClass(\'button-primary\');
                        
                        // Hide meta boxes and show correct one when tab clicked
                        jQuery(\'#propertyhive_metabox_tabs a\').click(function()
                        {
                            hide_meta_box_tabs();
                            
                            var this_href = jQuery(this).attr(\'href\').split(\'|\');
                            
                            for (var i in this_href)
                            {
                                jQuery(this_href[i]).show();
                            }
                            
                            jQuery(this).addClass(\'button-primary\');
                            
                            ' . ( ( $post->post_type == 'property' ) ? 'if (jQuery(this).attr(\'id\') == \'tab_details\') { showHideDepartmentMetaBox(); }' : '' ) . '
                            
                            return false;
                        });
                    });
                    
                    function hide_meta_box_tabs()
                    {
                        for (var i in meta_boxes_under_tabs)
                        {
                            for (var j in meta_boxes_under_tabs[i])
                            {
                                jQuery(\'#\' + meta_boxes_under_tabs[i][j] + \'\').hide();
                            }
                        }
                        
                        jQuery(\'#propertyhive_metabox_tabs a\').removeClass(\'button-primary\');
                    }
                </script>
                ';
            }
        }
        
    }

	/**
	 * Remove bloat
	 */
	public function remove_meta_boxes() {
        //remove_meta_box( 'submitdiv', 'property', 'side' );
		remove_meta_box( 'postexcerpt', 'property', 'normal' );
		remove_meta_box( 'pageparentdiv', 'property', 'side' );
		remove_meta_box( 'commentstatusdiv', 'property', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'property', 'side' );
        remove_meta_box( 'commentsdiv', 'product', 'normal' );
	}

	/**
	 * Rename core meta boxes
	 */
	public function rename_meta_boxes() {
		global $post;

		// Comments/Reviews
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) ) {
			//remove_meta_box( 'commentsdiv', 'product', 'normal' );

			//add_meta_box( 'commentsdiv', __( 'Reviews', 'propertyhive' ), 'post_comment_meta_box', 'product', 'normal' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		
		// Check the nonce
		if ( empty( $_POST['propertyhive_meta_nonce'] ) || ! wp_verify_nonce( $_POST['propertyhive_meta_nonce'], 'propertyhive_save_data' ) ) {
			return;
		} 

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check the post type
		if ( ! in_array( $post->post_type, array( 'property', 'contact' ) ) ) {
			return;
		}

		do_action( 'propertyhive_process_' . $post->post_type . '_meta', $post_id, $post );
	}

}

new PH_Admin_Meta_Boxes();
