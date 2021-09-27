<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class p3dlitePrinters_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {


		parent::__construct( [
			'singular' => __( 'Printer', '3dprint-lite' ), //singular name of the listed records
			'plural'   => __( 'Printers', '3dprint-lite' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}

	public static function apply_filter($sql) {
		global $wpdb;

		$where_str = ' where 1=1 ';
		if (isset($_REQUEST['printer_text']) && strlen($_REQUEST['printer_text'])>0) {

			$printer_text = esc_sql(trim($_REQUEST['printer_text']));
			$where_str .= " and ( name like '%$printer_text%' OR description like '%$printer_text%' ) ";

		}


		$sql.=$where_str;

		return $sql;
	}

	/**
	 * Retrieve p3dprinters data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_p3dprinters( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

        	$sql = "select * from ".$wpdb->prefix."p3dlite_printers ";

		$sql = self::apply_filter($sql);


		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
		else {
			$sql .= ' ORDER BY id desc ';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );


		return $result;
	}


	/**
	 * Delete a printer record.
	 *
	 * @param int $id printer ID
	 */
	public static function delete_printer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}p3dlite_printers",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;
		$where_str='';

        	$sql = "select count(*) from ".$wpdb->prefix."p3dlite_printers ";
		$sql = self::apply_filter($sql);


		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no printer data is available */
	public function no_items() {
		_e( 'No printers avaliable.', '3dprint-lite' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {


		switch ( $column_name ) {
			case 'status':

				if ((int)$item[ $column_name ] == 1) return '<span class="p3d-lite-active">'.__('Active', '3dprint-lite').'</span>';
				if ((int)$item[ $column_name ] == 0) return '<span class="p3d-lite-inactive">'.__('Inactive', '3dprint-lite').'</span>';
			break;
			default:
#				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
				return $item[ $column_name ];
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-action[]" value="%s" />', $item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_printer' );
		$edit_nonce = wp_create_nonce( 'sp_edit_printer' );
		$clone_nonce = wp_create_nonce( 'sp_clone_printer' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'edit' => sprintf( '<a href="?page=%s&action=%s&printer=%s&_wpnonce=%s">'.__("Edit", '3dprint-lite').'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $edit_nonce ),
			'clone' => sprintf( '<a href="?page=%s&action=%s&printer=%s&_wpnonce=%s">'.__("Clone", '3dprint-lite').'</a>', esc_attr( $_REQUEST['page'] ), 'clone', absint( $item['id'] ), $clone_nonce ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&printer=%s&_wpnonce=%s">'.__("Delete", '3dprint-lite').'</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'id'    => __( 'ID', '3dprint-lite' ),
			'name'    => __( 'Name', '3dprint-lite' ),
			'group_name'    => __( 'Group', '3dprint-lite' ),
			'status'    => __( 'Status', '3dprint-lite' )
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array( 'id', false ),
			'name' => array( 'name', false ),
			'group_name' => array( 'group_name', false ),
			'status' => array( 'status', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-activate' => 'Activate',
			'bulk-deactivate' => 'Deactivate',
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$settings=p3dlite_get_option( 'p3dlite_settings' );
//		$this->_column_headers = $this->get_column_info();
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);



		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $settings['items_per_page'];
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$items = self::get_p3dprinters( $per_page, $current_page );

		$this->items = $items;
	}

	public function process_bulk_action() {
		global $wpdb;

		if ( 'edit' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_edit_printer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				wp_redirect( admin_url( 'admin.php?page=p3dlite_printers&action=edit&printer_id='.(int)$_GET['printer'] ) );
				exit;
			}

		}

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_printer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_printer( absint( $_GET['printer'] ) );

				wp_redirect( admin_url( 'admin.php?page=p3dlite_printers' ) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )	
		  || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {

			$delete_ids = esc_sql( $_POST['bulk-action'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_printer( $id );

			}
			wp_redirect( admin_url( 'admin.php?page=p3dlite_printers' ) );
			exit;
		}
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-activate' ) 
		  || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-activate' ) ) {
			$activate_ids = esc_sql( $_POST['bulk-action'] );
			foreach ($activate_ids as $id) {
				$wpdb->update($wpdb->prefix.'p3dlite_printers', array('status'=>1), array('id'=>$id));
			}
			wp_redirect( admin_url( 'admin.php?page=p3dlite_printers' ) );
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-deactivate' ) 
		  || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-deactivate' ) ) {
			$deactivate_ids = esc_sql( $_POST['bulk-action'] );

			foreach ($deactivate_ids as $id) {
				$wpdb->update($wpdb->prefix.'p3dlite_printers', array('status'=>0), array('id'=>$id));

			}
			wp_redirect( admin_url( 'admin.php?page=p3dlite_printers' ) );
		}
	}

}

class p3dliteP_Plugin {

	// class instance
	static $instance;

	// printer WP_List_Table object
	public $p3dprinters_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );

		$this->screen_option();
	}


	public static function set_screen( $status, $option, $value ) {

		return $value;
	}

	public function plugin_menu() {


	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {

		?>
		<div class="wrap">
			<h2><?php _e('Printers', '3dprint-lite');?> </h2>

			<div id="poststuff p3d-lite-poststuff">
				<button class="button-secondary" type="button" onclick="location.href='<?php echo admin_url( 'admin.php?page=p3dlite_printers&action=add' );?>'"><b><?php _e('Add Printer', '3dprint-lite');?></b></button>
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable p3d-lite-table">
							<form name="printer_form" method="post">
								<?php _e('Search in name or description', '3dprint-lite');?>: <input name="printer_text" value="<?php if(isset($_REQUEST['printer_text'])) echo $_REQUEST['printer_text'];?>">&nbsp;
								<input type="submit" value="<?php _e('Search', '3dprint-lite');?>">
								<?php
								$this->p3dprinters_obj->prepare_items();
								$this->p3dprinters_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
				<button class="button-secondary" type="button" onclick="location.href='<?php echo admin_url( 'admin.php?page=p3dlite_printers&action=add' );?>'"><b><?php _e('Add Printer', '3dprint-lite');?></b></button>
			</div>
		</div>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'p3dPrinters',
			'default' => 10,
			'option'  => 'p3dprinters_per_page'
		];

		add_screen_option( $option, $args );

		$this->p3dprinters_obj = new p3dlitePrinters_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
?>