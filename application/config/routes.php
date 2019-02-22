<?php

return [
	'^$' => 'main/index',
	'^index$' => 'main/index',
	
	'^signin$' => 'user/signin',
	'^logout$' => 'user/logout',
	'^create$' => 'user/create',
	'^user_page$' => 'user/userPage',
	'^photo$' => 'user/photo',
	'^restore$' => 'user/restore',
	'^passrestore(.*)$' => 'user/passrestore',
	'^verify(.*)$' => 'user/verify',

	'^gallery$' => 'gallery/index',
	'^add_like$' => 'gallery/likes',
	'^delete_like$' => 'gallery/unlikes',
	'^save_photo$' => 'gallery/saveImage',
	'^delete_photo$' => 'gallery/deleteImage',
	'^new_comment$' => 'gallery/saveComment',
	'^delete_comment$' => 'gallery/deleteComment'
];