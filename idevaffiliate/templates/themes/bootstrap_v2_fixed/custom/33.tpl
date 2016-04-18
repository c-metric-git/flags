{*
-------------------------------------------------------
	iDevAffiliate HTML Front-End Template
-------------------------------------------------------
	Template   : Bootstrap 2 - Fixed Width Responsive
-------------------------------------------------------
	Copyright  : iDevDirect.com LLC
	Website    : www.idevdirect.com
-------------------------------------------------------

Custom Page Instructions
------------------------

You can get to this page in the affiliate control panel here: /account.php?action=33

You really don't want to "hard code" any text into this page because using language packs will become useless.
In your admin center, create a "Custom Language Token" and use that token instead.

There is already a sample token provided for this page.

	{$custom_page_header}

You can see it being used below.
Change, remove or create new tokens as you wish.
------------------------
*}

<legend>{$custom_page_header}</legend>
{* The following are available tokens on all your template pages.  For more information, see the Custom Control Panel Pages area of the admin center. *}
<p>{$affiliate_id}</p>
<p>{$affiliate_username}</p>
<p>{$affiliate_firstname}</p>
<p>{$affiliate_lastname}</p>
<p>{$affiliate_email}</p>
<p>{$site_homepage}</p>
<p>{$site_affhome}</p>
<p>{$site_textlink}</p>