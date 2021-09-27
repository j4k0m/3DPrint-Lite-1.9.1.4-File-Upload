# 3DPrint-Lite-1.9.1.4-File-Upload
Analysis of WordPress 3D Print Lite 1.9.1.4 - arbitrary file upload vulnerability.

## The Vulnerability:
This vulnerability allow an unauthenticated attacker to upload attribute file to the target host, the files will be uploaded in `/wp-content/uploads/p3d` directory, this is due to the application that does not perform any verification process of the file extension when uploading it other than escaping functions, as we can see in [p3dlite_handle_upload](https://github.com/RyouYoo/3DPrint-Lite-1.9.1.4-File-Upload/blob/2fc9594edd7ffc6d8bbbf02d795a9444331e58dc/3dprint-lite/includes/3dprint-lite-functions.php#L1066) function.
The function can be accessed by `p33dlite_handle_upload` ajax action ```add_action( 'wp_ajax_p3dlite_handle_upload', 'p3dlite_handle_upload' );``` in https://github.com/RyouYoo/3DPrint-Lite-1.9.1.4-File-Upload/blob/2fc9594edd7ffc6d8bbbf02d795a9444331e58dc/3dprint-lite/3dprint-lite.php#L25.

The application using `fopen()` function to create and write into files, `fopen()` function without any additional checks can be used to upload files in another directory that we shouldn't be able to upload to, but in this case, the developer did add two functions to prevent this behavior:
```php
function p3dlite_basename($file) {
	$array=explode('/',$file);
	$base=array_pop($array);
	return $base;
} 

function p3dlite_extension($file) {
	$array=explode('.',$file);
	$ext=array_pop($array);
	return $ext;
} 
```
These functions make sure to take only the base file name and pass it to the function `fopen()`, so trying to upload a file with malicious name like: "../shell.php" won't work.
