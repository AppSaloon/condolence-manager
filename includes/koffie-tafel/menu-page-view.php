<?php
namespace cm\includes\koffie_tafel;

class Menu_Page_View
{

	public function cm_sub_menu_callback()
	{
		$controller = new Koffie_Tafel_Controller();
		$post_ids = $controller->all_koffie_posts();

		?>
        <form method="post">
		<select name="select_koffie_tafel" id="">
			<?php
		if ($post_ids){
			foreach ( $post_ids as $ID ){
				$name = get_post_meta($ID, 'name', true);
				$familyname = get_post_meta($ID, 'familyname', true);
				$tekst = $name . " " . $familyname;
				echo "<option value=".$ID.">$tekst</option>";
			}
		}
			?>
		</select>
            <input type="submit" value="Show">
            <input type="hidden" name="load_koffie_tafel">
        </form>


		<?php
	}

}