<?php
/**
 * Meta Box For App Pages Icons
*/
class AdForestAPI_appPages_Rational_Meta_Box {
	private $screens = array( 'page', );
	private $fields = array(
		array(
			'id' => 'app-page-menu-icon',
			'label' => 'Upload icon here size 24X24 ',
			'type' => 'media',
		),
	);

	/**
	 * Class construct method. Adds actions to their respective WordPress hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Hooks into WordPress' add_meta_boxes function.
	 * Goes through screens (post types) and adds the meta box.
	 */
	public function add_meta_boxes() {
		foreach ( $this->screens as $screen ) {
			add_meta_box(
				'app-page-icons',
				__( 'App Page Icons', 'adforest-rest-api' ),
				array( $this, 'adforestAPI_page_icon_add_meta_box_callback' ),
				$screen,
				'advanced',
				'high'
			);
		}
	}

	/**
	 * Generates the HTML for the meta box
	 * 
	 * @param object $post WordPress post object
	 */
	public function adforestAPI_page_icon_add_meta_box_callback( $post ) {
		wp_nonce_field( 'app_page_icons_data', 'app_page_icons_nonce' );
		echo __( 'Upload icon for the app pages in menu sidebar. If no icon is uploaded then defaul icon will be shown from App Api Options.', 'adforest-rest-api' );
		$this->generate_fields( $post );
	}

	/**
	 * Hooks into WordPress' admin_footer function.
	 * Adds scripts for media uploader.
	 */
	public function admin_footer() {
		?><script>
			// https://codestag.com/how-to-use-wordpress-3-5-media-uploader-in-theme-options/
			jQuery(document).ready(function($){
				if ( typeof wp.media !== 'undefined' ) {
					var _custom_media = true,
					_orig_send_attachment = wp.media.editor.send.attachment;
					$('.rational-metabox-media').click(function(e) {
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(this);
						var id = button.attr('id').replace('_button', '');
						_custom_media = true;
							wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								$("#"+id).val(attachment.url);
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							};
						}
						wp.media.editor.open(button);
						return false;
					});
					$('.add_media').on('click', function(){
						_custom_media = false;
					});
				}
			});
		</script><?php
	}

	/**
	 * Generates the field's HTML for the meta box.
	 */
	public function generate_fields( $post ) {
		$output = '';
		foreach ( $this->fields as $field ) {
			$label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
			$db_value = get_post_meta( $post->ID, 'app_page_icons_' . $field['id'], true );
			switch ( $field['type'] ) {
				case 'media':
					$input = sprintf(
						'<input class="regular-text" id="%s" name="%s" type="text" value="%s"> <input class="button rational-metabox-media" id="%s_button" name="%s_button" type="button" value="Upload" />', $field['id'], $field['id'], $db_value, $field['id'], $field['id'] );
					break;
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">', $field['type'] !== 'color' ? 'class="regular-text"' : '', $field['id'], $field['id'], $field['type'], $db_value );
			}
			$output .= $this->row_format( $label, $input );
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}

	/**
	 * Generates the HTML for table rows.
	 */
	public function row_format( $label, $input ) {
		return sprintf(
			'<tr><th scope="row">%s</th><td>%s</td></tr>',
			$label,
			$input
		);
	}
	/**
	 * Hooks into WordPress' save_post function
	 */
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['app_page_icons_nonce'] ) )
			return $post_id;

		$nonce = $_POST['app_page_icons_nonce'];
		if ( !wp_verify_nonce( $nonce, 'app_page_icons_data' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		foreach ( $this->fields as $field ) {
			if ( isset( $_POST[ $field['id'] ] ) ) {
				switch ( $field['type'] ) {
					case 'email':
						$_POST[ $field['id'] ] = sanitize_email( $_POST[ $field['id'] ] );
						break;
					case 'text':
						$_POST[ $field['id'] ] = sanitize_text_field( $_POST[ $field['id'] ] );
						break;
				}
				update_post_meta( $post_id, 'app_page_icons_' . $field['id'], $_POST[ $field['id'] ] );
			} else if ( $field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, 'app_page_icons_' . $field['id'], '0' );
			}
		}
	}
}
new AdForestAPI_appPages_Rational_Meta_Box;