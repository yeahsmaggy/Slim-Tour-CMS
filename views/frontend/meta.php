<meta name="Description" content="<?php  (isset($description)) ? print $description : print 'your-description-text'; ?>">
<meta name="Keywords" content="your site keywords">
<?php $author = 'author'; (isset($page_title)) ? print $header : ''; ?>


<!-- DC -->
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />

<meta name="DC.Title" content="<?php  (isset($page_title)) ? print $page_title : print SITENAME . '|  your site description'; ?>">

<meta name="DC.Creator" content="<?php  (isset($author)) ? print $author :  print ''; ?>">

<meta name="DC.Description" content="<?php (isset($description)) ? print $description : print 'your-description-text'; ?>">

<meta name="DC.Subject" content="Mountain Bike and Bicycle tours" />

<meta name="DC.Type" content="Website" />

<meta name="DC.Format" content="Website" />

<meta name="DC.Language" content="en-GB" />

<meta name="DC.Rights" content="All Rights Reserved">



<!-- open graph -->
<meta property="og:title" content="<?php  (isset($page_title)) ? print $page_title : print SITENAME . '|  your site description'; ?>"/>

<meta property="og:image" content="<?php  (isset($page_image)) ? print 'http://' . $_SERVER["SERVER_NAME"] . $page_image : print 'http://' . $_SERVER["SERVER_NAME"] . ''; ?>"/>

<meta property="og:type" content="website">

<meta property="og:url" content="http://<?php print $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] ?>"/>

<meta property="og:site_name" content="www.georidersmtb.com">

<meta property="og:description" content="<?php (isset($description)) ? print  $description : print 'your-description-text'; ?>">


