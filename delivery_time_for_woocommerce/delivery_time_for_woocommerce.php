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
        $default_options = array('days' => '7', 'display_on' => '1', 'color' => '#000000','description' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).");
        add_option('delivery_time_options', $default_options);
    }


    public function plugin_setup()
    {
        $this->plugin_url    = plugins_url( '/', __FILE__ );
        $this->plugin_path   = plugin_dir_path( __FILE__ );
        
        add_action( 'woocommerce_settings_tabs', array( $this, 'wc_settings_tabs_delivery_time_settings_tab') );
        add_action( 'woocommerce_settings_delivery_time_settings', array( $this,'delivery_time_settings_tab_content') );
        add_filter( 'woocommerce_product_data_panels', array( $this, 'woo_new_product_tab') );
        add_filter('woocommerce_product_data_tabs', array($this, 'delivery_time_tab'));
        add_action('woocommerce_product_data_panels', array($this, 'delivery_time_filed'));
        add_action('woocommerce_process_product_meta', array( $this,'woocommerce_product_custom_fields_save'));
        add_action( 'woocommerce_single_product_summary', array( $this, 'product_detail'), 5 );
        add_action( 'woocommerce_after_shop_loop_item', array( $this, 'action_woocommerce_after_shop_loop_item_title'), 10, 0 ); 
        add_action('wp_footer', array($this, 'add_script_footer')); 
    }

    //Make intence of class
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    //Add sub-menu inside woocommerce menu
    public function wc_settings_tabs_delivery_time_settings_tab()
    {
        $current_tab = ( isset($_GET['tab']) && $_GET['tab'] === 'delivery_time_settings' ) ? 'nav-tab-active' : '';
        echo '<a href="'.admin_url().'admin.php?page=wc-settings&tab=delivery_time_settings" class="nav-tab '.$current_tab.'">'.__( "Delivery Time", "woocommerce" ).'</a>';
    }
    
    public function delivery_time_settings_tab_content() {
        include('settings_page.php');
    }
    public function delivery_time_tab($tabs) {
    	$tabs['delivery_time'] = [
    		'label' => __('Delivery Time', 'txtdomain'),
    		'target' => 'delivery_time_filed',
    		'class' => ['hide_if_external'],
    		'priority' => 99
    	];
    	return $tabs;
    }
    // Adds the new tab
    public function delivery_time_filed( $tabs ) {
    	global $product_object; 
        $feature_product=get_post_meta(get_the_ID(), 'days', true );
        $display_on=get_post_meta(get_the_ID(), 'display_on', true );
        $color=get_post_meta(get_the_ID(), 'color', true );
        $description=get_post_meta(get_the_ID(), 'description', true );
        ?>
        <div id="delivery_time_filed" class="panel woocommerce_options_panel hidden">
            <div class="options_group" style="">
                <p class="form-field _product_url_field ">
                    <label>Delivery time</label>
                    <input type="text" class="short" name="days" id="days" placeholder="Days" value="<?php echo $feature_product ?>">
                </p>
                <p class="form-field _product_url_field ">
                    <label>Delivery description</label>
                    <textarea name="delivery_description" rows="5" cols="100"><?php echo $description; ?></textarea>
                </p>
                <p class="form-field _product_url_field ">
                    <label>Delivery on</label>
                    <select name="display_on">
                        <option value="1" <?php echo $display_on == 1 ? 'selected' : ''; ?>>Single product page</option>
                        <option value="2" <?php echo $display_on == 2 ? 'selected' : ''; ?>>Product archive page</option>
                    </select>
                </p>
                <p class="form-field _button_text_field ">
                    <label>Color</label>
                    <input type="color" name="color" id="color" value="<?php echo $color ?>">
                </p>    
            </div>
        </div>
        <?php
    }
    
    //Show metadata fields 
    function woocommerce_product_custom_fields() {
        global $product_object; 
        $feature_product=get_post_meta(get_the_ID(), 'days', true );
        $display_on=get_post_meta(get_the_ID(), 'display_on', true );
        $color=get_post_meta(get_the_ID(), 'color', true );
        $description=get_post_meta(get_the_ID(), 'description', true );
        ?>
        <div class="options_group" style="">
            <p class="form-field _product_url_field ">
                <label>Delivery time</label>
                <input type="text" class="short" name="days" id="days" placeholder="Days" value="<?php echo $feature_product ?>">
            </p>
            <p class="form-field _product_url_field ">
                <label>Delivery description</label>
                <textarea name="delivery_description" rows="5" cols="100"><?php echo $description; ?></textarea>
            </p>
            <p class="form-field _product_url_field ">
                <label>Delivery on</label>
                <select name="display_on">
                    <option value="1" <?php echo $display_on == 1 ? 'selected' : ''; ?>>Single product page</option>
                    <option value="2" <?php echo $display_on == 2 ? 'selected' : ''; ?>>Product archive page</option>
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
        if ( isset($_POST['delivery_description']) ) {
            update_post_meta( $product, 'description', $_POST['delivery_description'] );
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
        $productdescription = get_post_meta( $product->id, 'description', true );
        
        $days = $productdays == 0 || '' ? $getSettings['days'] : $productdays; 
        $color = $productcolor == '' ? $getSettings['color'] : $productcolor;
        $description = $productdescription == '' || null ?  : $getSettings['description'];
        
        if($days != '-1'){
            ?>
            <p style="color:<?php echo $color; ?>; cursor: <?php echo $description == '' || null ? 'text' : 'pointer' ?>"   
            <?php echo $description == '' || null ? '' : 'onclick="toggle_discription()"' ?>>
                <b>Delivery time : </b> <?php echo $days; ?> Days
            </p>
            <p id="product_description" style="color:<?php echo $color; ?>;display: none;background: #f2f2f2;padding: 10px;border-radius: 5px;"><b>Delivery description :</b> <?php echo $productdescription ? $productdescription : $getSettings['description']; ?></p>
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
        $productdescription = get_post_meta( $product->id, 'description', true );
        
        $days = $productdays == 0 || '' ? $getSettings['days'] : $productdays; 
        $color = $productcolor == '' ? $getSettings['color'] : $productcolor;
        $description = $productdescription == '' || null ?  : $getSettings['description'];
        
        if($days != '-1'){
            ?>
            <p style="color:<?php echo $color; ?>; cursor: <?php echo $description == '' || null ? 'text' : 'pointer' ?>"   
            <?php echo $description == '' || null ? '' : 'onclick="loop_toggle_discription('.$product->id.')"' ?>>
                <b>Delivery time : </b> <?php echo $days; ?> Days
            </p>
            <p id="product_description_<?php echo $product->id?>" style="color:<?php echo $color; ?>;display: none;background: #f2f2f2;padding: 10px;border-radius: 5px;"><b>Delivery description :</b> <?php echo $productdescription ? $productdescription : $getSettings['description']; ?></p>
            <?php
        }
    }
    public function add_script_footer(){ ?>
        <script>
            
            function loop_toggle_discription(id) {
              var x = document.getElementById("product_description_"+id);
              if (x.style.display === "none") {
                x.style.display = "block";
              } else {
                x.style.display = "none";
              }
            }
            function toggle_discription() {
              var x = document.getElementById("product_description");
              if (x.style.display === "none") {
                x.style.display = "block";
              } else {
                x.style.display = "none";
              }
            }
        </script>
    <?php }
}
