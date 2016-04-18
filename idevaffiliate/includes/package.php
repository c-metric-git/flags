<?PHP

// Edition 1: Standard
// Edition 2: Gold
// Edition 3: Platinum
// Edition 4: Black

// Editing these variables will not do anything without a valid
// matching key. For instance, switching to the "Black Edition"
// will not actually give you the Black Edition if you don't
// already have a valid Black Edition key.

$edition = 1;

if ($edition == 1) {
$prid = 19;
$write_edition = "Standard Edition";
$short_edition = "iDevAffiliateStandard"; // For installation page.
} elseif ($edition == 2) {
$prid = 13;
$write_edition = "Gold Edition";
$short_edition = "iDevAffiliateGold"; // For installation page.
} elseif ($edition == 3) {
$prid = 14;
$write_edition = "Platinum Edition";
$short_edition = "iDevAffiliatePlatinum"; // For installation page.
} elseif ($edition == 4) {
$prid = 20;
$write_edition = "Black Edition";
$short_edition = "iDevAffiliateBlack"; // For installation page.
}

?>