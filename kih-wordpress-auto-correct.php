<?php
/*
	Plugin Name: WordPress Auto Correct
	Plugin URI: http://www.codestuff.com/projects/kih-wordpress-auto-correct
	Description: Replace all occurrences of "wordpress" with the proper format which is WordPress with a capital "W" and captital "P".
	Version: 1.0.5
	Author: Gerry Ilagan
	Author URI: http://gerry.ws

==================================================================================================

1.0.0 - 2009-04-09 - Initial version
1.0.5 - 2009-04-26 - Took into consideration html tags

==================================================================================================
This software is provided "as is" and any express or implied warranties, including,
but not limited to, the implied warranties of merchantibility and fitness for a particular
purpose are disclaimed. In no event shall the copyright owner or contributors be liable for
any direct, indirect, incidental, special, exemplary, or consequential damages (including,
but not limited to, procurement of substitute goods or services; loss of use, data, or
profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way
out of the use of this software, even if advised of the possibility of such damage.

For full license details see license.txt
=============================================================================================== */

/**
 * Create the admin page of this plugin under the options menu of wordpress.
 */
function kih_add_wpautocorrect_adminpage() {

	add_option('kih_wpautocorrect_on', 1,
			'Turn on/off the WordPress auto correction process.');

	// Create a submenu under Options:
    add_options_page( __('WordPress Auto Correct'), __('WP Auto Correct'), 8,
    				'kih-wpautocorrect', 'kih_wpautocorrect_adminpage' );

}

/**
 * Display the options page for the plugin
 */
function kih_wpautocorrect_adminpage() {

	if (isset($_POST['kih_wpautocorrect_save'])) {
		check_admin_referer('kih-wpautocorr-opts');
		update_option( 'kih_wpautocorrect_on',
						( (intval($_POST['kih_wpautocorrect_on']) == 1) ? 1 : 0 ) );

		// display update message
		echo "<div class='updated fade'><p>";
		_e('WordPress Auto Correct options updated.');
		echo "</p></div>";
	}

	?>
	<div class="wrap">
		<h2><?php _e('WordPress Auto Correct Options'); ?></h2>

		<form method="post">
			<?php if ( function_exists('wp_nonce_field') )
					wp_nonce_field('kih-wpautocorr-opts'); ?>
			<fieldset class='options'>
				<table class="editform" cellspacing="2" cellpadding="5" width="100%">
					<tr>
						<th width="30%" valign="top" style="padding-top: 10px;">
							<label for="kih_wpautocorrect_on">Turn on/off the WordPress auto
							correction</label>
						</th>
						<td>
                    		<select name="kih_wpautocorrect_on">
                    		<option value="0"
                    		<?php echo (get_option('kih_wpautocorrect_on')==0?"selected":"" ); ?>>
                    		<?php _e('WP Auto Correct is OFF'); ?></option>
                    		<option value="1"
                    		<?php echo (get_option('kih_wpautocorrect_on')==1?"selected":""); ?>>
                    		<?php _e('WP Auto Correct is ON'); ?></option>
                    		</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<p class="submit"><input type='submit' name='kih_wpautocorrect_save'
						value='Update Options' /></p>
						<input type="hidden" name="action" value="update" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<?php
}

/**
 * The WP Auto Correct filter function
 */
function kih_wpautocorrect_filter ($str='') {

	if ( get_option('kih_wpautocorrect_on') != 1 )	return $str;

	    $img_pattern = '/([^<>]+)<[^>]+>|<[^>]+>([^<>]+)/';

	    if (preg_match_all($img_pattern,$str,$matches,PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE)) {
	    	$retval = $matches[0];
	    } else {
    		$retval = '';
	    }

        // Process all strings that can be replaced
        for ( $i = 1; $i < count($matches); $i++ ) {
            foreach ( $matches[$i] as $match ) {
                if ( $match[1] != -1 ) {
                    $converted = str_ireplace( 'wordpress', 'WordPress', $match[0] );
                    $str = substr_replace( $str, $converted, $match[1], strlen($match[0]) );
                }
            }
        }

	    return($str);
}

// Create the hooks to Wordpress
add_action('admin_menu', 'kih_add_wpautocorrect_adminpage');
add_filter('the_content', 'kih_wpautocorrect_filter', 10, 1);

?>