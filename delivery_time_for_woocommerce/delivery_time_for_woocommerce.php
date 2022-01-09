<?php
/* 
Plugin Name: Delivery Time for WooCommerce. 
Version: 1.0
Author: Mehul dave
*/
define( 'DIR_PATH', __FILE__ );

add_action(
    'plugins_loaded', 
    array( DeliveryTimeForWoocommerce::get_instance(), 'plugin_setup' )
);

class DeliveryTimeForWoocommerce
{
    protected static $instance = NULL;
    public $plugin_url = '';
    public $plugin_path = '';

    public function __construct() {
        $this->setup_activation();
    }

    //Setting Plugi default values 
    public function setup_activation() {
        register_activation_hook( DIR_PATH, array( 'DeliveryTimeForWoocommerce', 'activate' ) );
    }

    //Activate callback
    public static function activate() {
        $default_options = array('days' => '7', 'display_on' => '1', 'color' => '#000000');
        add_option('delivery_time_options', $default_options);
    }


    public function plugin_setup()
    {
        $this->plugin_url    = plugins_url( '/', __FILE__ );
        $this->plugin_path   = plugin_dir_path( __FILE__ );
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action('woocommerce_product_options_general_product_data', array( $this, 'woocommerce_product_custom_fields'));
        add_action('woocommerce_process_product_meta', array( $this,'woocommerce_product_custom_fields_save'));
        add_action( 'woocommerce_single_product_summary', array( $this, 'product_detail'), 5 );
        add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'action_woocommerce_after_shop_loop_item_title'), 10, 0 ); 
    }

    //Make intence of class
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    //Add sub-menu inside woocommerce menu
    public function menu()
    {
        add_submenu_page( 'woocommerce', 'Delivery Time for WooCommerce', 'Delivery Time for WooCommerce', 'manage_options', 'delivery-time-settings-page', function(){ 
                include_once( 'settings_page.php' );
            }
        );
    }
    //Show metadata fields 
    public function woocommerce_product_custom_fields() {
        global $product_object; 
        print_r(get_the_ID());
        $feature_product=get_post_meta(get_the_ID(), 'days', true );
        $feature_product=get_post_meta(get_the_ID(), 'display_on', true );
        $color=get_post_meta(get_the_ID(), 'color', true );
        ?>
        <div class="options_group show_if_external" style="">
            <p class="form-field _product_url_field ">
                <label>Delivery time</label>
                <input type="text" class="short" name="days" id="days" placeholder="Days" value="<?php echo $feature_product ?>">
            </p>
            <p class="form-field _product_url_field ">
                <label>Delivery on</label>
                <select name="display_on">
                    <option value="1" <?php echo $feature_product == 1 ? 'selected' : ''; ?>>Single product page</option>
                    <option value="2" <?php echo $feature_product == 2 ? 'selected' : ''; ?>>Product archive page</option>
                </select>
            </p>
            <p class="form-field _button_text_field ">
                <label>Color</label>
                <input type="color" name="color" id="color" value="<?php echo $color ?>">
            </p>    
        </div>
        <?php
    }
    // Save Product metadata 
    public function woocommerce_product_custom_fields_save( $product ) {
        if ( isset($_POST['days']) ) {
            update_post_meta( $product, 'days', $_POST['days'] );
        }
        if ( isset($_POST['display_on']) ) {
            update_post_meta( $product, 'display_on', $_POST['display_on'] );
        }
        if ( isset($_POST['color']) ) {
            update_post_meta( $product, 'color', $_POST['color'] );
        }
    }
    // Display data on Product page 
    public function product_detail() {
        global $product;
        $getSettings = get_option( 'delivery_time_options' );
        $productdays = get_post_meta( $product->id, 'days', true );
        $productdisplay_on = get_post_meta( $product->id, 'display_on', true );
        $productcolor = get_post_meta( $product->id, 'color', true );
        if((!$getSettings['days'] == 0 || !$getSettings['days'] == '') && $productdisplay_on == 1){
            $days = $productdays == 0 || '' ? $getSettings['days'] : $productdays; 
            $color = $productcolor == '#000000' || '' ? $getSettings['color'] : $productcolor; 
            ?>
            <p style="color:<?php echo $color; ?>">
                <b>Delivvery time : </b> <?php echo $days; ?> Days
            </p>
            <?php
        }
    }

    // Display data on archive page 
    public function action_woocommerce_after_shop_loop_item_title(  ) { 
        global $product;
        $getSettings = get_option( 'delivery_time_options' );
        $productdays = get_post_meta( $product->id, 'days', true );
        $productdisplay_on = get_post_meta( $product->id, 'display_on', true );
        $productcolor = get_post_meta( $product->id, 'color', true );
        if((!$getSettings['days'] == 0 || !$getSettings['days'] == '') && $productdisplay_on == 2){
            $days = $productdays == 0 || '' ? $getSettings['days'] : $productdays; 
            $color = $productcolor == '#000000' || '' ? $getSettings['color'] : $productcolor; 
            ?>
            <p style="color:<?php echo $color; ?>">
                <b>Delivvery time : </b> <?php echo $days; ?> Days
            </p>
            <?php
        }
    }
}