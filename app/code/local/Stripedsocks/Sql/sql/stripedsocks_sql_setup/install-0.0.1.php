 <?php
 $this->startSetup();
 $this->addAttribute('catalog_category', 'category_header', array(
     'group'                 => 'General Information',
     'type'                  => 'varchar', // can be int, varchar, decimal, text, datetime
     'backend'               => '', // If you're making an image attribute you'll need to add : catalog/category_attribute_backend_image
     'frontend_input'        => '',
     'frontend'              => '',
     'label'                 => 'Category Header',
     'input'                 => 'text', //text, textarea, select, file, image, multiselect
     'class'                 => '',
     'source'                => '', // Only needed if using select or multiselect
     'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // Scope can be SCOPE_STORE, SCOPE_GLOBAL or SCOPE_WEBSITE
     'visible'               => true,
     'frontend_class'        => '',
     'required'              => false, // or true
     'user_defined'          => true, // or false
     'default'               => '',
     'position'              => 10, // Number depends on where you want it to display in the grid.
 ));
  $this->addAttribute('catalog_category', 'description_bottom', array(
     'group'                 => 'General Information',
     'type'                  => 'text', // can be int, varchar, decimal, text, datetime
     'backend'               => '', // If you're making an image attribute you'll need to add : catalog/category_attribute_backend_image
     'frontend_input'        => '',
     'frontend'              => '',
     'label'                 => 'Description Bottom',
     'input'                 => 'textarea', //text, textarea, select, file, image, multiselect
     'class'                 => '',
     'source'                => 'eav/entity_attribute_source_boolean', // Only needed if using select or multiselect
     'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // Scope can be SCOPE_STORE, SCOPE_GLOBAL or SCOPE_WEBSITE
     'visible'               => true,
     'frontend_class'        => '',
     'required'              => false, // or true
     'user_defined'          => true, // or false
     'default'               => '',
     'position'              => 11, // Number depends on where you want it to display in the grid.
 ));
  $this->addAttribute('catalog_category', 'is_occasion', array(
     'group'                 => 'General Information',
     'type'                  => 'int', // can be int, varchar, decimal, text, datetime
     'backend'               => '', // If you're making an image attribute you'll need to add : catalog/category_attribute_backend_image
     'frontend_input'        => '',
     'frontend'              => '',
     'label'                 => 'Is Occasion',
     'input'                 => 'select', //text, textarea, select, file, image, multiselect
     'class'                 => '',
     'source'                => 'eav/entity_attribute_source_boolean', // Only needed if using select or multiselect
     'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // Scope can be SCOPE_STORE, SCOPE_GLOBAL or SCOPE_WEBSITE
     'visible'               => true,
     'frontend_class'        => '',
     'required'              => false, // or true
     'user_defined'          => true, // or false
     'default'               => '',
     'position'              => 11, // Number depends on where you want it to display in the grid.
	 'option'        => array(
                            'value' => array(
                                '0' => 'No',
                                '1' => 'Yes',
                            )
                        ),
 ));
   $this->addAttribute('catalog_category', 'is_manufacturer', array(
     'group'                 => 'General Information',
     'type'                  => 'int', // can be int, varchar, decimal, text, datetime
     'backend'               => '', // If you're making an image attribute you'll need to add : catalog/category_attribute_backend_image
     'frontend_input'        => '',
     'frontend'              => '',
     'label'                 => 'Is Manufacturer',
     'input'                 => 'select', //text, textarea, select, file, image, multiselect
     'class'                 => '',
     'source'                => 'eav/entity_attribute_source_boolean', // Only needed if using select or multiselect
     'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // Scope can be SCOPE_STORE, SCOPE_GLOBAL or SCOPE_WEBSITE
     'visible'               => true,
     'frontend_class'        => '',
     'required'              => false, // or true
     'user_defined'          => true, // or false
     'default'               => '',
     'position'              => 11, // Number depends on where you want it to display in the grid.
	 'option'        => array(
                            'value' => array(
                                '0' => 'No',
                                '1' => 'Yes',
                            )
                        ),
 ));
 $this->addAttribute('catalog_category', 'google_category', array(
     'group'                 => 'General Information',
     'type'                  => 'varchar', // can be int, varchar, decimal, text, datetime
     'backend'               => '', // If you're making an image attribute you'll need to add : catalog/category_attribute_backend_image
     'frontend_input'        => '',
     'frontend'              => '',
     'label'                 => 'Google Category',
     'input'                 => 'text', //text, textarea, select, file, image, multiselect
     'class'                 => '',
     'source'                => '', // Only needed if using select or multiselect
     'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // Scope can be SCOPE_STORE, SCOPE_GLOBAL or SCOPE_WEBSITE
     'visible'               => true,
     'frontend_class'        => '',
     'required'              => false, // or true
     'user_defined'          => true, // or false
     'default'               => '',
     'position'              => 11, // Number depends on where you want it to display in the grid.	
 ));
  $this->addAttribute('catalog_category', 'short_name', array(
     'group'                 => 'General Information',
     'type'                  => 'varchar', // can be int, varchar, decimal, text, datetime
     'backend'               => '', // If you're making an image attribute you'll need to add : catalog/category_attribute_backend_image
     'frontend_input'        => '',
     'frontend'              => '',
     'label'                 => 'Short Name For Menu',
     'input'                 => 'text', //text, textarea, select, file, image, multiselect
     'class'                 => '',
     'source'                => '', // Only needed if using select or multiselect
     'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL, // Scope can be SCOPE_STORE, SCOPE_GLOBAL or SCOPE_WEBSITE
     'visible'               => true,
     'frontend_class'        => '',
     'required'              => false, // or true
     'user_defined'          => true, // or false
     'default'               => '',
     'position'              => 11, // Number depends on where you want it to display in the grid.
 ));             
 $this->endSetup();