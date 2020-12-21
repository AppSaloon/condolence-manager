jQuery( document ).ready(
	function ($) {
		$( '.jquery-datepicker' ).datepicker(
			{
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				yearRange: '-130:+10'
			}
		)
	}
)
