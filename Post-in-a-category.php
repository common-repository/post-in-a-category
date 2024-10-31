<?php
/*
Plugin Name: Post in a Category  
Plugin URI: http://wordpress.org/plugins/post-in-a-category/
Description: This is a plugin to display post in a particular category in a page.
Author: Sudina Prashob
Version: 1.5
Author URI:
*/
// create custom plugin settings menu
if(isset($_POST['image']))
{
    $image=$_POST['image'];
    $title=$_POST['title'];
    $content=$_POST['content'];
	$category=$_POST['category'];
    update_option('image',$image);
    update_option('title',$title);
    update_option('content',$content);
	update_option('category',$category);
}
add_action('admin_menu', 'baw_create_menu');
function baw_create_menu() {

	//create new top-level menu
	add_menu_page('Post in a category Plugin Settings', 'Post Category Settings', 'administrator', __FILE__, 'baw_settings_page',plugins_url('/img/icon1.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'baw-settings-group', 'new_option_name' );
}

function baw_settings_page() {
?>
<div class="wrap">
<h2>Post in a category</h2>

<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <?php settings_fields( 'baw-settings-group' ); ?>
    <?php do_settings_sections( 'baw-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Display Image</th>
        <td>
            <input type="radio" name="image" value="show" <?php echo (get_option('image')=="show") ?  "checked" : "" ;  ?>/>Show 
            <input type="radio" name="image" value="hide"<?php echo (get_option('image')=="hide") ?  "checked" : "" ;  ?>/>Hide </td>
        </tr>
        <tr valign="top">
        <th scope="row">Display Title</th>
        <td>
            <input type="radio" name="title" value="show" <?php echo (get_option('title')=="show") ?  "checked" : "" ;  ?>/>Show 
            <input type="radio" name="title" value="hide"<?php echo (get_option('title')=="hide") ?  "checked" : "" ;  ?>/>Hide </td>
        </tr>
        <tr valign="top">
        <th scope="row">Display Content</th>
        <td>
            <input type="radio" name="content" value="show" <?php echo (get_option('content')=="show") ?  "checked" : "" ;  ?>/>Show 
            <input type="radio" name="content" value="hide"<?php echo (get_option('content')=="hide") ?  "checked" : "" ;  ?>/>Hide </td>
        </tr>
		<tr>
		<th scope="row">Select Category</th>
		<td>
		<select name="category"><?php $categories = get_categories( $args ); 
		foreach($categories as $cat)
		{
		?>
		<option value="<?php echo $cat->name; ?>"<?php if ($cat->name == get_option('category')) echo 'selected="selected"'; ?>><?php echo $cat->name; ?></option>
		<?php
		}?>
		</select>
		</td></tr>
    </table>
    
    <input type="submit" name="save" value="Save Settings"/>

</form>
</div>

<?php }


function pic_Displaypostcategory($atts) {
     extract(shortcode_atts(array(
       // 'category' => 1,
        'num' => 2,
        'classname' => 3,
   ), $atts));

		$cat_name=get_option('category');
                $cat_id=get_cat_ID( $cat_name ); 
                $args = array(
    'numberposts' => $num,
    'offset' => 0,
    'category' => $cat_id,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'draft, publish, future, pending, private',
    'suppress_filters' => true );

    $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
        echo '<div class="'.$classname.'">';
	foreach( $recent_posts as $recent )
            {
            if($recent["post_title"]!=="" && get_option('title')=="show" )
            {
                    echo '<h5><a href="' . get_permalink($recent["ID"]) . '" title="Look '.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </h5> ';
            }
            if ( has_post_thumbnail($recent["ID"]) && get_option('image')=="show" ) {
                    $src = wp_get_attachment_image_src(get_post_thumbnail_id($recent["ID"]), array(300, 300), false, '');
                    echo '<a href="' . get_permalink($recent["ID"]) . '"><img src="' . $src[0]. '" /></a><br/>';
            }
            if($recent["post_content"]!=="" && get_option('content')=="show" )
            {
      ?> 
                <h6><?php echo substr($recent["post_content"], 0 , 150); ?>
                <span class="link"><?php echo '<a href="' . get_permalink($recent["ID"]) . '" title="Reaed More '.$recent["post_title"].'" >' .   'Read More'.'</a> '; ?></span>
                </h6>
    <?php   }
            }
  echo '</div>';   
}
add_shortcode("displaypost", 'pic_Displaypostcategory');


