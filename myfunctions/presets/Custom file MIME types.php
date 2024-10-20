// Custom file MIME types
function my_custom_mime_types( $mimes ) {
// New allowed mime types.
$mimes['avif'] = 'image/avif+xml';
$mimes['svg'] = 'image/svg+xml';
$mimes['svgz'] = 'image/svg+xml';
// $mimes['doc'] = 'application/msword';
// Optional. Remove a mime type.
unset( $mimes['exe'] );
return $mimes;
}
add_filter( 'upload_mimes', 'my_custom_mime_types' );