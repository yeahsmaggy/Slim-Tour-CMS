<?php

header("HTTP/1.0 404 Not Found");
$header = '<meta name="DC.Subject" content="' .  SITENAME . ' | 404 Error - Page Not Found';
$description = '';
$page_title = SITENAME . ' | 404 Error - Page Not Found';
$page_javascript = '$.backstretch([""]);';

require('includes/header.php');
?>
<div class="row">
<div class="col-md-8">
<div class="boxinner whitebg">
I'm sorry but this page doesn't exist.<br>
Why not try the <a href="/">home page</a>.
</div>
</div>
<div class="col-md-4">
<div class="boxinner whitebg">
</div>
</div>
</div>
<?php require('includes/footer.php');?>