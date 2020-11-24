jQuery( document ).ready(
	function ($) {
		$( '.cm_file_picker > input[type="button"]' ).click(
			function (e) {
				e.preventDefault()
				const file_picker       = $( this ).closest( '.cm_file_picker' )
				const file_picker_field = $( file_picker ).children( 'input[type="text"]' )

				//Extend the wp.media object
				const custom_uploader = wp.media.frames.file_frame = wp.media(
					{
						multiple: false
					}
				)

				//When a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on(
					'select',
					function () {
						const attachment = custom_uploader.state().get( 'selection' ).first().toJSON()
						console.log( attachment )
						$( file_picker_field ).val( attachment.url )
					}
				)

				//Open the uploader dialog
				custom_uploader.open()

			}
		)
	}
)
