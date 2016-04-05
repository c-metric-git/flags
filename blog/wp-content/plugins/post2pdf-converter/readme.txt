=== POST2PDF Converter ===
Contributors: redcocker
Donate link: http://www.near-mint.com/blog/donate
Tags: pdf, post, page, convert, download, tcpdf
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: 0.4.2

This plugin converts your post/page to PDF for visitors and visitors can download it easily.

== Description ==

This plugin converts your post/page to PDF for visitors and visitors can download it easily.

You can add a download link above/below every posts/pages.

Note: This plugin requires PHP 5.

= Features =

* Base on "[TCPDF](http://www.tcpdf.org/ "TCPDF")".
* Convert your posts/pages to PDF file dynamically or statically.
* Easy to add a download link into every or specified posts/pages.
* Support caching created PDFs.
* Easy to add your TrueType fonts.
* Easy to configure.

= Localization =

* 日本語(Japanese) by redcocker
* Deutsch(German) by Uli Sobers([Free Templates](http://www.free-templates-sobers.de/ "Free Templates"), [IQ137](http://www.iq137.de/ "IQ137"))

= Support plugins =

You can use POST2PDF Converter with following code syntaxhighlighter plugins and convert posts/pages with sourcecodes to PDF.

* [WP SyntaxHighlighter](http://wordpress.org/extend/plugins/wp-syntaxhighlighter/ "WP SyntaxHighlighter")
* [SyntaxHighlighter Evolved](http://wordpress.org/extend/plugins/syntaxhighlighter/ "SyntaxHighlighter Evolved")
* [WP-Syntax](http://wordpress.org/extend/plugins/wp-syntax/ "WP-Syntax")
* [CodeColorer](http://wordpress.org/extend/plugins/codecolorer/ "CodeColorer")
* [WP-CodeBox](http://wordpress.org/extend/plugins/wp-codebox/ "WP-CodeBox")
* [WP-SynHighlight](http://wordpress.org/extend/plugins/wp-synhighlight/ "WP-SynHighlight")
* [Syntax Highlighter Compress](http://wordpress.org/extend/plugins/syntax-highlighter-compress/ "Syntax Highlighter Compress")
* [Auto SyntaxHighlighter](http://wordpress.org/extend/plugins/auto-syntaxhighlighter/ "Auto SyntaxHighlighter")
* [Syntax Highlighter and Code Colorizer for WordPress](http://wordpress.org/extend/plugins/syntax-highlighter-and-code-prettifier/ "Syntax Highlighter and Code Colorizer for WordPress")
* [Syntax Highlighter MT](http://wordpress.org/extend/plugins/syntax-highlighter-mt/ "Syntax Highlighter MT")
* [SyntaxHighlighterPro](http://wordpress.org/extend/plugins/syntaxhighlighterpro/ "SyntaxHighlighterPro")
* [Easy Google Syntax Highlighter](http://wordpress.org/extend/plugins/easy-google-syntax-highlighter/ "Easy Google Syntax Highlighter")
* [WP-GeSHi-Highlight](http://wordpress.org/extend/plugins/wp-geshi-highlight/ "WP-GeSHi-Highlight")
* [WP Code Highlight](http://wordpress.org/extend/plugins/wp-code-highlight/ "WP Code Highlight") etc.

You can use POST2PDF Converter with following LaTex plugins and convert posts/pages with mathematical formulas, chemical formulae and charts to PDF.

* [WP QuickLaTeX](http://wordpress.org/extend/plugins/wp-quicklatex/ "WP QuickLaTeX")

You can use POST2PDF Converter with following translation plugins and generate translated PDF.

* [qTranslate](http://wordpress.org/extend/plugins/qtranslate/ "qTranslate")

**This list is only one example.**

== Installation ==

= Installation =

1. Upload plugin folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Go to "Settings" -> "POST2PDF Converter" to configure.

= Change the default font =

1. Go to "Settings" -> "POST2PDF Converter" to configure.
1. Enter new font name to "Font" and "Monospaced font" option in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.
* Don't apply a proportional font to "Monospaced font" option.

= Fonts for Japanese =

This plugin includes following Japanese fonts.

Font name: cid0jp, kozgopromedium, kozminproregular

This plugin can't quite convert contents which include Japanese characters to PDF file using bundled fonts.

However, you can download better Japanese fonts and use them.

If your contents are written in Japanese, please install one of these fonts. You have only to upload font files without converting and configure some setting options.

[TCPDF用日本語フォント](http://www.near-mint.com/blog/software/rcjfont-for-tcpdf "TCPDF用日本語フォント")

1. Upload all *.php, *.z, *.ctg.z files to `/wp-content/plugins/post2pdf-converter/tcpdf/fonts` directory.
1. Go to "Settings" -> "POST2PDF Converter" to configure.
1. Enter font name to "Font" and "Monospaced font" option in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.
* Don't apply a proportional font to "Monospaced font" option.

You can also use following fonts.

[日本語フリーFONT](http://www.monzen.org/Refdoc/tcpdf_freefontj/ "日本語フリーFONT")

If you want to use your own TrueType font, you can convert TrueType font to a font for TCPDF using "Font converter" in setting panel.

= Fonts for Latin, Greek and Cyrillic =

This plugin includes following Latin fonts.

Font name: courier, helvetica, times

You can also use following fonts. However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

Font name: pdfacourier, pdfahelvetica, pdfatimes

You can also use "FreeFont" or "DejaVu fonts". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= Fonts for Simplified Chinese =

This plugin includes following Simplified Chinese fonts.

Font name: cid0cs, stsongstdlight

= Fonts for Traditional Chinese =

This plugin includes following Traditional Chinese fonts.

Font name: cid0ct, msungstdlight

= Fonts for Korean =

This plugin includes following Korean fonts.

Font name: cid0kr, hysmyeongjostdmedium

= Fonts for Cyrillic =

You can use "FreeFont" or "DejaVu fonts". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= Fonts for Arabic =

This plugin includes following Arabic fonts.

Font name: aealarabiya, aefurat

You can also use "FreeFont". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= Fonts for Hebrew, Armenian, Georgian, Ethiopian and Thai =

You can use "FreeFont". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

= DejaVu fonts =

This plugin inclues "DejaVu fonts". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

Font name: dejavusans, dejavusanscondensed, dejavusansextralight, dejavusansmono, dejavuserif, dejavuserifcondensed

Note: "dejavusansmono" is a monospaced font.

"DejaVu fonts" support Latin, Greek and Cyrillic characters.

= FreeFont =

This plugin inclues "FreeFont". However, If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, you had better disable "Font subsetting" option.

Font name: freemono, freesans, freeserif

Note: "freemono" is a monospaced font.

"FreeFont" supports Latin, Greek, Cyrillic, Arabic, Hebrew, Armenian, Georgian, Ethiopian and Thai characters.

= Add new fonts =

You can add new fonts for TCPDF.

**How to convert a font**

Convert a TrueType font(*.ttf) to font files for TCPDF.

1. Read [TCPDF Fonts](http://www.tcpdf.org/fonts.php "TCPDF Fonts") or Use "Font converter" in the setting panel.

You can download [utilities](http://tcpdf.git.sourceforge.net/git/gitweb.cgi?p=tcpdf/tcpdf;a=tree;f=fonts/utils;h=bb226721af699d7ce3b5fff6f612c115a9a3a1a3;hb=30b07848b44dd53e2519617542f67e4e83f53134 "utilities") to convert a font.

**How to install a font**

1. Upload all *.php, *.z, *.ctg.z files to `/wp-content/plugins/post2pdf-converter/tcpdf/fonts` directory.

**How to use the added font**

1. Go to "Settings" -> "POST2PDF Converter" to configure.
1. Enter font name to "Font" and "Monospaced font" option in "PDF Settings" section.
1. If you allow visitors who receive a PDF to make changes to it even if they don't have the same font, disable "Font subsetting" option.

* If your font file is xxxx.z, font name is xxxx.
* Don't apply a proportional font to "Monospaced font" option.

= Safe fonts directory =

After automatic updating, your added fonts will be removed.

If you enable "Safe fonts directory" option in the setting panel, your fonts will never be removed.

Before you enable "Safe fonts directory", you must make `/wp-content/tcpdf-fonts/` directory on your server manually. And upload/move bundled fonts and your fonts to new directory and enable "Safe fonts directory".

Now you can remove original fonts directory.

* Bundled fonts: Unzip a plugin zip file and you can find orignal fonts in `/post2pdf-converter/tcpdf/fonts`.
* Original fonts directory: `/YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/fonts`
・You need not to upload all bundled fonts to new font directory. But you must upload helvetica.php, helveticab.php, helveticabi.php, helveticai.php to new font directory at least.

However, after automatic updating, fonts in the new directory will never be updated. You must maintain the new fonts directory by yourself.

= Shortcode =

You can insert the download link to your posts/pages using shortcode.

Note: Before you use shortcode, go to the setting panel and ebable "Shortcode" option.

`[pdf]Click here to get a PDF[/pdf]`

This plugin allows you to set some attributes.

`[pdf id="1643" lang="jpn" file="id" font="cid0jp" monospaced="cid0jp" fontsize="11" subsetting="1" ratio="1.35" header="1" logo="1" logo_file="my_logo.png" logo_width="45" wrap_title="1" footer="1" filters="1" shortcode="parse" ffamily="0"]Click here to get a PDF[/pdf]`

You can apply different font, image ratio, header logo on each posts/pages.

You can also make the download link for other posts/pages using "id" attribute.

**Available attributes**

* id: set post id  by numbers. e.g. 1245
* lang: Set your language by language code. see "Available languages" below. e.g. eng
* file: Set filename type to `title` or `id`.
* font: Set default font by font name. e.g. helvetica
* monospaced: Set default monospaced font by font name. e.g. courier
* fontsize: Set font size by numbers. e.g. 12
* subsetting: Set to `1` or `0` to enable/disable Font subsetting.
* ratio: Set image ratio by numbers. e.g. 1.25
* header: Set to `1` or `0` to show/hide header.
* logo: Set to `1` or `0` to show/hide logo image.
* logo_file: Set logo file name. e.g. tcpdf_logo.jpg
* logo_width: Set logo width in millimeters. e.g. 30
* title: Set to `1` or `0` to show/hide title.
* wrap_title: Set to `1` or `0`. When set to `1`, long title will be wrapped.
* filters: Set to `1` or `0`. When set to `1`, WordPress default filtes will be applied to the title/content.
* footer: Set to `1` or `0` to show/hide footer.
* shortcode: Set to `parse` or `remove` to parse/remove shortcode.
* ffamily: If a PDF file has garbled characters, set to `1`.

Note: When `id` is omitted, current post id will be set to. When other attribures are omitted, current setting value will be set to.

**Available languages**

* afr: Afrikaans
* sqi: Albanian
* ara: Arabic
* aze: Azerbaijanian
* eus: Basque
* bel: Belarusian
* bra: Portuguese(Brazil)
* cat: Catalan
* chi: Chinese(Simplified)
* zho: Chinese(Traditional)
* hrv: Croatian
* ces: Czech
* dan: Danish
* nld: Dutch
* eng: English
* est: Estonian
* far: Farsi
* fra: French
* ger: German
* gle: Irish
* glg: Galician
* kat: Georgian
* hat: Haitian Creole
* heb: Hebrew
* hun: Hungarian
* hye: Armenian
* ind: Indonesian
* ita: Italian
* jpn: Japanese
* kor: Korean
* mkd: Macedonian
* msa: Malay
* mlt: Maltese
* ron2: Moldavian
* ron3: Moldovan
* nob: Norwegian Bokmål
* pol: Polish
* por: Portuguese
* ron1: Romanian
* rus: Russian
* srp: Serbian
* slv: Slovenian
* spa: Spanish
* swa: Swahili
* swe: Swedish
* urd: Urdu
* cym: Welsh
* yid: Yiddish
* ltr: Other(Text direction: Left to Right)
* rtl: Other(Text direction: Right to Left)

== Frequently Asked Questions ==

= Q. Created PDF file has garbled characters. =

A. Go to the setting panel and enter following font names to "Font" and "Monospaced font" option.

* Font: `freesans`, Monospaced font: `freemono` (For Latin, Greek, Cyrillic, Hebrew, Armenian, Georgian, Ethiopian and Thai etc.)
* Font: `dejavusans`, Monospaced font: `dejavusansmono` (For Latin, Greek and Cyrillic etc.)
* Font: `aefurat`, Monospaced font: `aefurat` (For Arabic)
* Font: `stsongstdlight`, Monospaced font: `stsongstdlight` (For Simplified Chinese)
* Font: `msungstdlight`, Monospaced font: `msungstdlight` (For Traditional Chinese)
* Font: `hysmyeongjostdmedium`, Monospaced font: `hysmyeongjostdmedium` (For Korean)

**When this solves the problem, please give me details(your language, font, monospaced font), I may need to change the default font for your language.**

You can also add new font for your language. For details, read "Add new fonts" section in this document.

If you still can't solve the problem, Go to the setting panel and enable "Add default font to font-family" option.

For detailed information about Japanese font, read "Fonts for Japanese" section in this document.

= Q. Created PDF file name becomes garbled. =

A. Go to the setting panel and set "File name" option to "Post id" in "2. PDF Settings" section.

= Q. Created PDF contains images with wrong size. =

A. Adjust width and height attributes in `<img>` tag or width and height properties in style attribute  in `<img>` tag or other elements.

= Q. Created PDF contains no images. =

A. If large size images are placed in same row, the images may disappear in PDF.

Adjust width and height attributes in `<img>` tag or width and height properties in style attribute  in `<img>` tag or other elements.

Or place `<br />` tags after each image blocks.

= Q. I got following error when downloding PDF file. "TCPDF ERROR: Could not include font definition file: helvetica" =

When "Safe fonts directory" option is enabled, there must be some bundled fonts in new font directory. Upload helvetica.php, helveticab.php, helveticabi.php, helveticai.php to new font directory at least. You can find these fonts in `/YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/fonts` directory.

== Screenshots ==

1. This is the download link.
2. This is setting panel.

== Changelog ==

= 0.4.2 =
* TCPDF is updated to 5.9.149.
* Support qTranslate.
* Added new setting option to cache temporary data.
* Added new filter to add "aligin: right;" to some images.
* Added $get_by_http_request as a hidden setting option.
* Fix a bug: POST2PDF Converter version doesn't be shown in "6. Your System Info" setting section.

= 0.4.0.1 =
* Fix a bug: When "Cache" is enabled, the first-time downloaded PDF becomes empty sometime.

= 0.4 =
* TCPDF is updated to 5.9.147.
* Support caching created PDFs.
* Added new setting option to show/hide title in the created PDF.
* Added a filter to remove own shortcodes([pdf]xxx[/pdf]).
* Extended shortcode format.
* Added some items to "6. Your System Info" in the setting panel.
* Changed some file name.
* Fix a bug: Applied shortcode parser for WP SyntaxHighlighter in an improper way.

= 0.3.3 =
* TCPDF is updated to 5.9.145.
* Added new setting options to add custom icon to the download link.
* Support WP SyntaxHighlighter, SyntaxHighlighter Evolved, WP-Syntax, CodeColor, WP-CodeBox, WP-SynHighlight and some other code syntaxhighlighter plugins.
* Support WP QuickLaTeX.
* Changed definition of "K_PATH_IMAGES" constant.
* Fix a bug: Wrong outputing css.

= 0.3 =
* TCPDF is updated to 5.9.144.
* Can convert post/page to a PDF statically in the setting panel.
* Can convert a TrueType font to a font for TCPDF in the setting panel.
* Changed the default font for Turkish.

= 0.2.4.1 =
* Fix a bug: When Site Address is different from WordPress Address and "Access" option is set to "Deny any access with the download URL directly", Can't donwload PDF file.

= 0.2.4 =
* Added new setting option to prevent search engines from crawling.
* Fix a bug: A problem when updating DB table.

= 0.2.3.1 =
* Support relative image path.

= 0.2.3 =
* Added new setting option to excluded some posts/pages as posts/pages without a download link.
* Changed filter for `<blockquote>` tag.

= 0.2.2 =
* TCPDF is updated to 5.9.143.
* Changed the default file name in some languages.
* Fix a bug: Getting the error, "TCPDF ERROR: Some data has already been output, can't send PDF file"

= 0.2.1 =
* Modified filters to format sourcecode.
* Fix a bug: A problem in PHP safe mode. Thanks Michael Starke.

= 0.2 =
* TCPDF is updated to 5.9.142.
* Support shortcode to insert the download link.
* Add new filter to format sourcecode.
* Added new setting option to show/hide header.
* Added new setting option to show/hide footer.
* Added new setting option to wrap the long title.
* Added new setting option to show/hide signature.
* Added new setting option to apply WordPress filters.
* Added some filters for content.
* Changed the default font for some languages.
* Fix a bug: The mix two character encoding in tcpdf.php.

= 0.1.6 =
* Added new setting option to change image size.
* Added "Before and After the post/page content block" option to "Position".
* Cahnged separating character for Keywords.
* Fix a bug: Subject is not plain text but HTML with tags.

= 0.1.5 =
* Added new setting option to change the header logo.
* Added German translation. Thanks Uli Sobers.
* Changed the default font size.
* Changed the default font for Polosh.
* Better regular expression for "Add default font to font-family" option.

= 0.1.3 =
* Added new setting option to define PDF file name.
* Added new setting option to set default monospaced font.
* Added new setting option to change fonts directory location.
* Updated post2pdf_conv_add_style() to load CSS on only posts or pages.
* Fix a bug: In some languages, created PDF has garbled characters.
* Fix a bug: When content contains font properties(font-family), created PDF has garbled characters.
* Fix a bug: When only log-in users are allowed to access, download link appears for guest users.
* Fix a bug: When a post has long title, the title on header sticks out from the right edge.
* Fix a bug: PDF file name length possibly exceed system limit.

= 0.1 =
* This is the initial release.

== Upgrade Notice ==

= 0.4.2 =
This version has new features, change and bug fix.

= 0.4.0.1 =
This version has a bug fix.

= 0.4 =
This version has new features, changes and bug fix.

= 0.3.3 =
This version has new features, change and bug fix.

= 0.3 =
This version has a new feature.

= 0.2.4.1 =
This version has a bug fix.

= 0.2.4 =
This version has a new feature and bug fix.

= 0.2.3.1 =
This version has a change.

= 0.2.3 =
This version has a new feature and change.

= 0.2.2 =
This version has a change and bug fix.

= 0.2.1 =
This version has a change and bug fix.

= 0.2 =
This version has new features, change and bug fix.

= 0.1.6 =
This version has a new feature, change and bug fix.

= 0.1.5 =
This version has new features and changes.

= 0.1.3 =
This version has new features, changes and bug fixes.

= 0.1 =
This is the initial release.
