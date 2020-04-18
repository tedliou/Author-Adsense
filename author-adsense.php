<?php
/**
 * Plugin Name: Author Adsense
 * Plugin URI: https://tedliou.com
 * Description: Each author can earn his own advertising revenue.
 * Author: Ted Liou
 * Author URI: https://tedliou.com
 * Version: 1.0.1
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
 
defined( 'ABSPATH' ) or die();

class AuthorAdsense {
	private $saved_options;

	// Start
	public function __construct() {
		add_shortcode( 'author-adsense', array( $this, 'author_adsense_shortcode' ) );
		
		if ( is_admin() ){
			add_action( 'show_user_profile', array( $this, 'author_adsens_usermeta_form' ) );
			add_action( 'edit_user_profile', array( $this, 'author_adsens_usermeta_form' ) );
			add_action( 'personal_options_update', array( $this, 'author_adsens_usermeta_form_update' ) );
			add_action( 'edit_user_profile_update', array( $this, 'author_adsens_usermeta_form_update' ) );
			add_action( 'admin_menu', array( $this, 'author_adsense_add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'author_adsense_page_init' ) );
		}
	}
	
	// Account Options UI
	public function author_adsens_usermeta_form( $user )
	{
		$user_options = get_user_meta($user->ID, 'author_adsense_user_options')[0];
?>
		<h3><?= __('Author Adsense', 'author-adsense'); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="publisher_id"><?= __('Publisher ID', 'author-adsense'); ?></label>
				</th>
				<td>
					<input type="text"
						   class="regular-text"
						   id="publisher_id"
						   name="author_adsense_user_options[publisher_id]"
						   value="<?= isset( $user_options['publisher_id'] ) ? esc_attr( $user_options['publisher_id'] ) : ''; ?>"
						   placeholder="pub-1234567890123456">
					<p class="description">
						<?= __('Please enter your publisher id.', 'author-adsense'); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="author_adsense_slot_id"><?= __('Slot ID', 'author-adsense'); ?></label>
				</th>
				<td>
					<input type="text"
						   class="regular-text"
						   id="slot_id"
						   name="author_adsense_user_options[slot_id]"
						   value="<?= isset( $user_options['slot_id'] ) ? esc_attr( $user_options['slot_id'] ) : ''; ?>"
						   placeholder="1234567890">
					<p class="description">
						<?= __('Please enter your slot id.', 'author-adsense'); ?>
					</p>
				</td>
			</tr>
		</table>
<?php
	}

	// Account Options Update
	public function author_adsens_usermeta_form_update( $user_id )
	{
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		
		update_user_meta( $user_id, 'author_adsense_user_options', $this->author_adsense_sanitize($_POST['author_adsense_user_options']));
	  
		return;
	}

	// WordPress Options UI
	public function author_adsense_create_admin_page() {
		$this->saved_options = get_option( 'author_adsense_options' ); ?>

		<div class="wrap">
			<h2>Author Adsense</h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'author_adsense_option_group' );
					do_settings_sections( 'author-adsense-admin' );
					submit_button();
				?>
			</form>
		</div>
		
	<?php }
	
	// Add WordPress Menu
	public function author_adsense_add_plugin_page() {
		add_options_page(
			'Author Adsense', // page_title
			'Author Adsense', // menu_title
			'manage_options', // capability
			'author-adsense', // menu_slug
			array( $this, 'author_adsense_create_admin_page' ) // function
		);
	}
	
	// WordPress Options Init
	public function author_adsense_page_init() {
		register_setting(
			'author_adsense_option_group', // option_group
			'author_adsense_options', // option_name
			array( $this, 'author_adsense_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'author_adsense_setting_section', // id
			__('Default Adsense Publisher Data', 'author-adsense'), // title
			array( $this, 'author_adsense_section_info' ), // callback
			'author-adsense-admin' // page
		);

		add_settings_field(
			'publisher_id', // id
			__('Publisher ID', 'author-adsense'), // title
			array( $this, 'publisher_id_callback' ), // callback
			'author-adsense-admin', // page
			'author_adsense_setting_section' // section
		);

		add_settings_field(
			'slot_id', // id
			__('Slot ID', 'author-adsense'), // title
			array( $this, 'slot_id_callback' ), // callback
			'author-adsense-admin', // page
			'author_adsense_setting_section' // section
		);
	}

	// Format Data
	public function author_adsense_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['publisher_id']) ) {
			$sanitary_values['publisher_id'] = sanitize_text_field( $input['publisher_id'] );
		}
		if ( isset( $input['slot_id'] )) {
			$sanitary_values['slot_id'] = sanitize_text_field( $input['slot_id'] );
		}

		return $sanitary_values;
	}

	public function author_adsense_section_info() {
		// Section content
	}

	public function publisher_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="author_adsense_options[publisher_id]" id="publisher_id" value="%s">',
			isset( $this->saved_options['publisher_id'] ) ? esc_attr( $this->saved_options['publisher_id']) : ''
		);
	}

	public function slot_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="author_adsense_options[slot_id]" id="slot_id" value="%s">',
			isset( $this->saved_options['slot_id'] ) ? esc_attr( $this->saved_options['slot_id']) : ''
		);
	}
	
	public function author_adsense_shortcode() {
		$show_pubID = '';
		$show_slotID = '';
		
		$default_options = get_option( 'author_adsense_options' );
		$default_publisher_id = 
			isset( $default_options['publisher_id'] ) ? 
				!empty( $default_options['publisher_id'] ) ? $default_options['publisher_id'] : false
			: false;
		$default_slot_id = 
			isset( $default_options['slot_id'] ) ? 
				!empty( $default_options['slot_id'] ) ? $default_options['slot_id'] : false
			: false;
		
		if ( is_single() ){
			$user_options = get_user_meta(get_the_author_meta('ID'), 'author_adsense_user_options')[0];
			$user_pubID = 
				isset( $user_options['publisher_id'] ) ? 
					!empty( $user_options['publisher_id'] ) ? $user_options['publisher_id'] : false
				: false;
			$user_slotID = 
				isset( $user_options['slot_id'] ) ? 
					!empty( $user_options['slot_id'] ) ? $user_options['slot_id'] : false
				: false;
			
			if( $user_pubID && $user_slotID ) {
				$show_pubID = $user_pubID;
				$show_slotID = $user_slotID;
			} else {
				$show_pubID = $default_options['publisher_id'];
				$show_slotID = $default_options['slot_id'];
			}
			
		}else{
			$show_pubID = $default_options['publisher_id'];
			$show_slotID = $default_options['slot_id'];
		}
		
		if ( !empty($show_pubID) && !empty($show_slotID) ) {
			printf(
				'<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<ins class="adsbygoogle"
					 style="display:block"
					 data-ad-client="ca-%s"
					 data-ad-slot="%s"
					 data-ad-format="auto"
					 data-full-width-responsive="true"></ins>
				<script>
					 (adsbygoogle = window.adsbygoogle || []).push({});
				</script>',
				$show_pubID, $show_slotID
			);
		}
	}
}

new AuthorAdsense();
