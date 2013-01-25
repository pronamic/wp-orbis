<?php

function orbis_flot( $id, $data, $options ) {
	printf(
		'<script type="text/javascript">jQuery.plot("#%s", %s, %s);</script>',
		$id,
		json_encode( $data ),
		json_encode( $options )
	);
}


