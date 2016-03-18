<html>
<body>
<FORM><INPUT TYPE="BUTTON" VALUE="Go Back" 
ONCLICK="history.go(-1)"></FORM>
<?php

// Config
require_once('config.php');

// Install 
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Application Classes
require_once(DIR_SYSTEM . 'library/customer.php');
require_once(DIR_SYSTEM . 'library/affiliate.php');
require_once(DIR_SYSTEM . 'library/currency.php');
require_once(DIR_SYSTEM . 'library/tax.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/length.php');
require_once(DIR_SYSTEM . 'library/cart.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);   

// Database 
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = 'seoimages'");

foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$data[$result['key']] = $result['value'];
			} else {
				$data[$result['key']] = unserialize($result['value']);
			}
		}
		
if (isset($data)) {$parameters = $data['seoimageparameters'];}
	else {$parameters['keywords'] = '%p';}


$query = $db->query("SELECT language_id FROM " . DB_PREFIX . "setting s
		inner join " . DB_PREFIX . "language l on s.value = l.code
		where s.group = 'config' and s.key = 'config_language'

	");

foreach ($query->rows as $language) {
		$language_id = $language['language_id'];
	}


//Product images
$query = $db->query("select pd.name as name, p.image as image, p.product_id as product_id, p.model as model, p.sku as sku, p.upc as upc, m.name as brand, cd.name as cname 
	from " . DB_PREFIX . "product_description pd 
	inner join " . DB_PREFIX . "product p on p.product_id = pd.product_id
	inner join " . DB_PREFIX . "product_to_category pc on pd.product_id = pc.product_id
	inner join " . DB_PREFIX . "category_description cd on cd.category_id = pc.category_id and cd.language_id = pd.language_id
	left join " . DB_PREFIX . "manufacturer m on m.manufacturer_id = p.manufacturer_id
	where pd.language_id = $language_id
	");

foreach ($query->rows as $product) {
	
	$bef = array("%c", "%p", "%m", "%s", "%u", "%b");
	$aft = array($product['cname'], $product['name'], $product['model'], $product['sku'], $product['upc'], $product['brand']);
	
	$newimagename = str_replace($bef, $aft,  $parameters['keywords']);
	
	$replace = '/'.generateSlug($newimagename).'-'.rand().'.';
	$old_image = $product['image'];
	$new_image = preg_replace('/\/([\w\s\t\a\e\f\v\W\D\s\S]((?!\/).)*?)\./', $replace, $product['image']);
	if (file_exists(DIR_IMAGE.$old_image))
		{
		echo 'Renaming for <b>'.$product['name'].' </b> -------- Image: '. $new_image .'<br> ';
		rename(DIR_IMAGE.$old_image, DIR_IMAGE.$new_image);
		$old_image = mysql_real_escape_string($old_image);
		
		$query = $db->query("update " . DB_PREFIX . "product set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "product_image set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "banner_image set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "category set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "language set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "manufacturer set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "option_value set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "voucher_theme set image = '".$new_image."' where image ='".$old_image."'");
		}
	}

//Additional product images
$query = $db->query("select pd.name as name, pi.image as image, pi.product_id as product_id, p.model as model, p.sku as sku, p.upc as upc, m.name as brand, cd.name as cname  
	from " . DB_PREFIX . "product_description pd 
	inner join " . DB_PREFIX . "product_image pi on pi.product_id = pd.product_id
	inner join " . DB_PREFIX . "product p on p.product_id = pd.product_id
	inner join " . DB_PREFIX . "product_to_category pc on pd.product_id = pc.product_id
	inner join " . DB_PREFIX . "category_description cd on cd.category_id = pc.category_id and cd.language_id = pd.language_id
	left join " . DB_PREFIX . "manufacturer m on m.manufacturer_id = p.manufacturer_id
	where pd.language_id = $language_id
	");

foreach ($query->rows as $product) {
	
	$bef = array("%c", "%p", "%m", "%s", "%u", "%b");
	$aft = array($product['cname'], $product['name'], $product['model'], $product['sku'], $product['upc'], $product['brand']);
	
	$newimagename = str_replace($bef, $aft,  $parameters['keywords']);
	
	$replace = '/'.generateSlug($newimagename).'-'.rand().'.';
	
	$old_image = $product['image'];
	$new_image = preg_replace('/\/([\w\s\t\a\e\f\v\W\D\s\S]((?!\/).)*?)\./', $replace, $product['image']);
	if (file_exists(DIR_IMAGE.$old_image))
		{
		echo 'Renaming for <b>'.$product['name'].' </b> -------- Image: '. $new_image .'<br> ';
		rename(DIR_IMAGE.$old_image, DIR_IMAGE.$new_image);
		$old_image = mysql_real_escape_string($old_image);
		
		$query = $db->query("update " . DB_PREFIX . "product set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "product_image set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "banner_image set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "category set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "language set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "manufacturer set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "option_value set image = '".$new_image."' where image ='".$old_image."'");
		$query = $db->query("update " . DB_PREFIX . "voucher_theme set image = '".$new_image."' where image ='".$old_image."'");
		}
	}

function generateSlug($phrase) {
	
	$cyr = array(
        "й"=>"i","ц"=>"c","у"=>"u","к"=>"k","е"=>"e","н"=>"n",
        "г"=>"g","ш"=>"sh","щ"=>"sh","з"=>"z","х"=>"x","ъ"=>"\'",
        "ф"=>"f","ы"=>"i","в"=>"v","а"=>"a","п"=>"p","р"=>"r",
        "о"=>"o","л"=>"l","д"=>"d","ж"=>"zh","э"=>"ie","ё"=>"e",
        "я"=>"ya","ч"=>"ch","с"=>"c","м"=>"m","и"=>"i","т"=>"t",
        "ь"=>"\'","б"=>"b","ю"=>"yu",
        "Й"=>"I","Ц"=>"C","У"=>"U","К"=>"K","Е"=>"E","Н"=>"N",
        "Г"=>"G","Ш"=>"SH","Щ"=>"SH","З"=>"Z","Х"=>"X","Ъ"=>"\'",
        "Ф"=>"F","Ы"=>"I","В"=>"V","А"=>"A","П"=>"P","Р"=>"R",
        "О"=>"O","Л"=>"L","Д"=>"D","Ж"=>"ZH","Э"=>"IE","Ё"=>"E",
        "Я"=>"YA","Ч"=>"CH","С"=>"C","М"=>"M","И"=>"I","Т"=>"T",
        "Ь"=>"\'","Б"=>"B","Ю"=>"YU"
    ); 
	
	$gr = array(
		"Β" => "V", "Γ" => "Y", "Δ" => "Th", "Ε" => "E", "Ζ" => "Z", "Η" => "E",
		"Θ" => "Th", "Ι" => "i", "Κ" => "K", "Λ" => "L", "Μ" => "M", "Ν" => "N",
		"Ξ" => "X", "Ο" => "O", "Π" => "P", "Ρ" => "R", "Σ" => "S", "Τ" => "T",
		"Υ" => "E", "Φ" => "F", "Χ" => "Ch", "Ψ" => "Ps", "Ω" => "O", "α" => "a",
		"β" => "v", "γ" => "y", "δ" => "th", "ε" => "e", "ζ" => "z", "η" => "e",
		"θ" => "th", "ι" => "i", "κ" => "k", "λ" => "l", "μ" => "m", "ν" => "n",
		"ξ" => "x", "ο" => "o", "π" => "p", "ρ" => "r", "σ" => "s", "τ" => "t",
		"υ" => "e", "φ" => "f", "χ" => "ch", "ψ" => "ps", "ω" => "o", "ς" => "s",
		"ς" => "s", "ς" => "s", "ς" => "s", "έ" => "e", "ί" => "i", "ά" => "a",
		"ή" => "e", "ώ" => "o", "ό" => "o"
	);
	
	$arabic = array(
		"ا"=>"a", "أ"=>"a", "آ"=>"a", "إ"=>"e", "ب"=>"b", "ت"=>"t", "ث"=>"th", "ج"=>"j",
		"ح"=>"h", "خ"=>"kh", "د"=>"d", "ذ"=>"d", "ر"=>"r", "ز"=>"z", "س"=>"s", "ش"=>"sh",
		"ص"=>"s", "ض"=>"d", "ط"=>"t", "ظ"=>"z", "ع"=>"'e", "غ"=>"gh", "ف"=>"f", "ق"=>"q",
		"ك"=>"k", "ل"=>"l", "م"=>"m", "ن"=>"n", "ه"=>"h", "و"=>"w", "ي"=>"y", "ى"=>"a",
		"ئ"=>"'e", "ء"=>"'",   
		"ؤ"=>"'e", "لا"=>"la", "ة"=>"h", "؟"=>"?", "!"=>"!", 
		"ـ"=>"", 
		"،"=>",", 
		"َ‎"=>"a", "ُ"=>"u", "ِ‎"=>"e", "ٌ"=>"un", "ً"=>"an", "ٍ"=>"en", "ّ"=>""
	);
	
	$persian = array(
		"ا"=>"a", "أ"=>"a", "آ"=>"a", "إ"=>"e", "ب"=>"b", "ت"=>"t", "ث"=>"th",
		"ج"=>"j", "ح"=>"h", "خ"=>"kh", "د"=>"d", "ذ"=>"d", "ر"=>"r", "ز"=>"z",
		"س"=>"s", "ش"=>"sh", "ص"=>"s", "ض"=>"d", "ط"=>"t", "ظ"=>"z", "ع"=>"'e",
		"غ"=>"gh", "ف"=>"f", "ق"=>"q", "ك"=>"k", "ل"=>"l", "م"=>"m", "ن"=>"n",
		"ه"=>"h", "و"=>"w", "ي"=>"y", "ى"=>"a", "ئ"=>"'e", "ء"=>"'", 
		"ؤ"=>"'e", "لا"=>"la", "ک"=>"ke", "پ"=>"pe", "چ"=>"che", "ژ"=>"je", "گ"=>"gu",
		"ی"=>"a", "ٔ"=>"", "ة"=>"h", "؟"=>"?", "!"=>"!", 
		"ـ"=>"", 
		"،"=>",", 
		"َ‎"=>"a", "ُ"=>"u", "ِ‎"=>"e", "ٌ"=>"un", "ً"=>"an", "ٍ"=>"en", "ّ"=>""
	);
	
	$normalize = array(
		'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
		'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
		'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
		'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
		'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
		'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
		'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 'Ğ'=>'G', 'Ş'=>'S', 'Ü'=>'U',
		'ü'=>'u', 'Ẑ'=>'Z', 'ẑ'=>'z', 'Ǹ'=>'N', 'ǹ'=>'n', 'Ò'=>'O', 'ò'=>'o', 'Ù'=>'U', 'ù'=>'u', 'Ẁ'=>'W',
		'ẁ'=>'w', 'Ỳ'=>'Y', 'ỳ'=>'y'		
	);
	
	$result = html_entity_decode($phrase); 
	
	$result = strtr($result, $cyr);
	$result = strtr($result, $gr);
	$result = strtr($result, $arabic);
	$result = strtr($result, $persian);
	$result = strtr($result, $normalize);	
	$result = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $result);
	
	$result = strtolower($result);
	$result = str_replace('&', '-and-', $result);
	$result = str_replace('^', '', $result);
    $result = preg_replace("/[^a-z0-9-]/", "-", $result);
	$result = preg_replace('{(-)\1+}','$1', $result); 
	$result = trim(substr($result, 0, 800));
	$result = trim($result,'-'); //Thanks to LeXXoS
    
    return $result;
	}
	
?>

</body>
</html>


