jQuery( document ).ready(
	function ($) {
		$( '#generate' ).on(
			'click',
			function (e) {
				e.preventDefault()
				const password = makeid()
				$( '#password' ).val( password )
			}
		)
	}
)

function makeid () {
	let text       = ''
	const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'

	for (let i = 0; i < 10; i++) {
		text += possible.charAt( Math.floor( Math.random() * possible.length ) )
	}

	return text
}
