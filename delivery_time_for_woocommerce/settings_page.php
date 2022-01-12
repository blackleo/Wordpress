<?php
	$getData = get_option( 'delivery_time_options' );
	
	if ($_POST['submit']) {
		$days = $_POST['delivery_time'];
		$display_on = $_POST['display_on']; 
		$color = $_POST['color'];
		$delivery_description = $_POST['delivery_description'];
		$updateDeliverySettings = array('days' => $days, 'display_on' => $display_on, 'color' => $color, 'description' => $delivery_description);
		update_option('delivery_time_options', $updateDeliverySettings);
		header('Location: '.$_SERVER['REQUEST_URI']);
 	}
?>
<style>
    .woocommerce-save-button{
        display:none!important;
    }
</style>
<div class="wpbody-content">
	<h1>Delivery Time for WooCommerce</h1>
	<form action="" method="POST">
		<table class="form-table">
			<tr>
				<th scope="row"><label>Delivery time</label></th>
				<td><input type="text" name="delivery_time" placeholder="day" value="<?php echo $getData['days']; ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label>Delivery description</label></th>
				<td><textarea name="delivery_description" rows="5" cols="100"><?php echo $getData['description']; ?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label>Delivery on</label></th>
				<td>
					<select name="display_on">
						<option value="1" <?php echo $getData['display_on'] == 1 ? 'selected' : ''; ?>>Single product page</option>
						<option value="2" <?php echo $getData['display_on'] == 2 ? 'selected' : ''; ?>>Product archive page</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Color</label></th>
				<td><input type="color" name="color" value="<?php echo $getData['color']; ?>"></td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
	</form>
</div>
