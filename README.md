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
https://github.com/RyouYoo/3DPrint-Lite-1.9.1.4-File-Upload/blob/2fc9594edd7ffc6d8bbbf02d795a9444331e58dc/3dprint-lite/includes/3dprint-lite-functions.php#L967

These functions make sure to take only the base file name and pass it to the function `fopen()`, so trying to upload a file with malicious name like: "../shell.php" won't work, trying to upload a double extension file also won't work in which case test.php.jpg will be uploaded with the name test.php_.jpg.
```
☁  3DPrint-Lite-1.9.1.4-File-Upload [main] ⚡  ./exploit.sh http://jakom.com
{"jsonrpc":"2.0","filename":"1632779012_test.php_.jpg"}%
☁  3DPrint-Lite-1.9.1.4-File-Upload [main] ⚡
```
The attacker will not be able to access the uploaded files that match the FilesMatch regex in the `.htaccess` file in `/wp-content/uploads/p3d/`.
```
AddType application/octet-stream obj
AddType application/octet-stream stl
<ifmodule mod_deflate.c>
        AddOutputFilterByType DEFLATE application/octet-stream
</ifmodule>
<FilesMatch "\.(php([0-9]|s)?|s?p?html|cgi|py|pl|exe)$">
        Order Deny,Allow
        Deny from all
</FilesMatch>
<ifmodule mod_expires.c>
        ExpiresActive on
        ExpiresDefault "access plus 365 days"
</ifmodule>
<ifmodule mod_headers.c>
        Header set Cache-Control "max-age=31536050"
</ifmodule>
```
![image](https://user-images.githubusercontent.com/48088579/134990636-44d5e746-debc-4ed5-9b7f-a5d6deebab01.png)

So getting Remote Code Execution in the target host is not part of this vulnerability.
