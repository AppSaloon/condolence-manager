<?php
namespace cm\includes\koffie_tafel;

class Menu_Page_View
{
    private $controller;

    public function __construct() {
        $this->controller = new Koffie_Tafel_Controller();
    }

	public function cm_sub_menu_callback()
	{

		$post_ids = $this->controller->all_koffie_posts();

		?>
        <form method="post">
		<select name="koffie_tafel_id" id="">
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
            <input type="submit" value="Download CSV">
            <input type="hidden" name="CSV_koffie_tafel" value="csv">
        </form>

        <form method="post">
            <select name="koffie_tafel_id" id="">
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
            <input type="submit" value="Check list">
            <input type="hidden" name="koffie_tafel" value="list">
        </form>

        <?php
        $check = (isset($_REQUEST['koffie_tafel']) && $_REQUEST['koffie_tafel'] == 'list'
                  && isset($_REQUEST['koffie_tafel_id'])) ? true : false;
        if( $check ){
            $id = $_REQUEST['koffie_tafel_id'];
            $result = $this->controller->all_participants_by_id($id);
            $result = $this->controller->result_to_array_objects($result);
            if ( $result ){
                ?> <table> <?php
	            foreach ( $result as $participant ){
	                echo "<tr><td>$participant->name</td><td>$participant->surname</td><td>$participant->telefon</td><td>$participant->email</td></tr>";

                }
                    ?></table><?php
            }
        }
	}
}