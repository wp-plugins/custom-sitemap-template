<?php
global $wpdb;
$cst_version = "1.0";
$cst_installed_ver = get_option( "cst_version" );
if ( $cst_installed_ver != $cst_version ) {
update_option( "cst_version", $cst_version );
}

add_action( 'plugins_loaded', array( 'CustomSitemapTemplate', 'get_instance' ) );

add_action( 'admin_menu', 'sitemap_template_plugin_menu' );

function sitemap_template_plugin_menu(){
add_submenu_page( 'options-general.php', 'Sitemap Page', 'Sitemap Page', 'manage_options', 'sitemap_settings', 'sitemap_settings_callback'); 
add_action('admin_init','register_sitemap_settings');
}

function register_sitemap_settings(){
register_setting('sitemap_settings-group','sitemap_post_list');
register_setting('sitemap_settings-group','sitemap_cat_list');
register_setting('sitemap_settings-group','sitemap_hide_post');
register_setting('sitemap_settings-group','sitemap_hide_cat');
register_setting('sitemap_settings-group','cst_settings_arr');
}

function sitemap_settings_callback(){
	settings_fields('sitemap_settings-group');
	/* delete_option('sitemap_post_list');
	add_option('sitemap_post_list',
	array(
	"folder_name_1" => array("9th of May 2009","Party Pictures from New York"),
	)
	);*/
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}?>
	<div class="wrap">
	<h2>Sitemap Settings</h2>
<?php if(isset($_POST['set_sitemap'])){ ?>
	<div><style>#option-saved-box{display: inline-block;}</style>
	<script>
	jQuery(document).ready(function($){
	   	setTimeout(function(){
	  		$("#option-saved-box").fadeOut("slow", function () {
	  			$("#option-saved-box").remove();
	      		});
	 
		}, 3000);
	});
	</script>
	<p id="option-saved-box"><strong><?php _e( 'Options saved', 'customtheme' ); ?></strong></p>
	</div>
<?php /* START 16-06-15 */
/*$checkpost = $_POST['checkpost'];
$checkcat = $_POST['checkcat'];
$hidepost = $_POST['hidepost'];
$hidecat = $_POST['hidecat'];*/

$checkpost = $_POST['checkpost'];
$checkcat = $_POST['checkcat'];
$hidepost = sanitize_text_field( stripslashes( $_POST['hidepost']) );
$hidecat = sanitize_text_field( stripslashes( $_POST['hidecat'] ) );

$hidepost = preg_replace(
  array(
    '/[^\d,]/',    // Matches anything that's not a comma or number.
    '/(?<=,),+/',  // Matches consecutive commas.
    '/^,+/',       // Matches leading commas.
    '/,+$/'        // Matches trailing commas.
  ),
  '',              // Remove all matched substrings.
  $hidepost
);

$hidecat = preg_replace(
  array(
    '/[^\d,]/',    // Matches anything that's not a comma or number.
    '/(?<=,),+/',  // Matches consecutive commas.
    '/^,+/',       // Matches leading commas.
    '/,+$/'        // Matches trailing commas.
  ),
  '',              // Remove all matched substrings.
  $hidecat
);

$data = array(	
		'sitemap_post_list' => $checkpost,
		'sitemap_cat_list' => $checkcat,
		'sitemap_hide_post' => $hidepost,
		'sitemap_hide_cat' => $hidecat,
  	);

update_option('cst_settings_arr', serialize($data));

update_option('sitemap_post_list', $checkpost);
update_option('sitemap_cat_list', $checkcat);
update_option('sitemap_hide_post', $hidepost);
update_option('sitemap_hide_cat', $hidecat);
/* END 16-05-15 */

}?>

	<h3>Include Post Types</h3>
	<p>Please select which post type would you want to display on sitemap.</p>

<?php  $args = array(
   /*'public'   => false,
   '_builtin' => true*/
);

$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'

$post_types = get_post_types( $args, $output, $operator ); ?>

<form action="" method="post" onsubmit="return numericValidation();">
<?php foreach ( $post_types as $post_type ) : ?>
<p>
<?php $lists = get_option('sitemap_post_list', true);
if (in_array($post_type, $lists)) { ?>
<input value="<?php echo $post_type;?>" type="checkbox" name="checkpost[]" checked>
<?php } else { ?>
<input value="<?php echo $post_type;?>" type="checkbox" name="checkpost[]">
<?php }
echo $post_type;?>
</p>
<?php endforeach; ?>
<h3>Exclude Post/Page Id</h3>
<p>Please enter post/page id which you want to hide on sitemap. You can enter multiple post ids using comma seperated.</p>
<div id="hide_post_wrapper">Enter Post/Page Ids: 
<input type="text" id="hidepost" name="hidepost" value="<?php echo get_option('sitemap_hide_post', true);?>">
<span class="cst-error" id="cst-error1">Error. Please enter number & comma (,) only.</span>
</div>


<h3>Include Taxonomies</h3>
<p>Please select which taxonomy would you want to display on sitemap.</p>
<?php $taxonomies = get_taxonomies(); ?>
<p>
<?php foreach ( $taxonomies as $taxonomy ) : 
$catlist = get_option('sitemap_cat_list', true);
if (in_array($taxonomy, $catlist)) { ?>
<input value="<?php echo $taxonomy;?>" type="checkbox" name="checkcat[]" checked>
<?php } else { ?>
<input value="<?php echo $taxonomy;?>" type="checkbox" name="checkcat[]">
<?php } 
echo $taxonomy;?>
</p>
<?php endforeach; ?>
<h3>Exclude Taxonomy Id</h3>
<p>Please enter taxonomy id which you want to hide on sitemap. You can enter multiple taxonomy ids using comma seperated.</p>
<div id="hide_cat_wrapper">Enter Post Ids: 
<input type="text" id="hidecat" name="hidecat" value="<?php echo get_option('sitemap_hide_cat', true);?>">
<span class="cst-error" id="cst-error2">Error. Please enter number & comma (,) only.</span><br>
<input type="submit" name="set_sitemap" value="Submit">
</div>
</form>
    <script type="text/javascript">
        function numericValidation() {
            //var numbers = /^[0-9]+$/; //only for numbers

            var numbers = /^([0-9 ,]+)?$/;

            //[0-9]+ matches 1 or more digits [,-] matches a , or a -

            //(...)? is an optional match

            //^ anchors the start and $ anchors the end of the string

            var txt = document.getElementById('hidepost');
            var txtcat = document.getElementById('hidecat');

            if (!(txt.value.match(numbers))) {
                //alert('Your input is valid');

		document.getElementById("cst-error1").style.display = "inline-block";
                return false;
            }
            else {
                //alert('Please input numeric characters only');
		
		if (!(txtcat.value.match(numbers))) {
		        //alert('Your input is valid');

			/*jQuery(document).ready(function($){
	   		setTimeout(function(){
	  			$("#option-saved-box").fadeOut("slow", function () {
	  				$("#option-saved-box").remove();
	      			});
	 
			}, 3000);
	   		});*/

			document.getElementById("cst-error2").style.display = "inline-block";
                	return false;
                }
                else { 
			//document.getElementById("option-saved-box").style.display = "inline-block";
	              	return true;
            }
            
        }
}
    </script>
</div>
<?php } ?>
