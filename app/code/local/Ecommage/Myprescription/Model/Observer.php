<?php
class Ecommage_Myprescription_Model_Observer
{
    /*
     * Function connect to SFTP server and dowload new file
     *
     * return array $xml
     * */
    const NO_PAY = 'myprescription/general/no_pay';
    const PAY = 'myprescription/general/pay';
    const PRI = 'myprescription/general/private';

    public $totalCount = 0;
    public $newCount = 0;
    public $modifyCount = 0;
    public $deleteCount = 0;
    public function logMessage($message){
        $fh = fopen(Mage::getBaseDir('media') . DS . 'import'. DS . 'xmloutput', "a");
        fwrite($fh, "\r\n\r\n");
        fwrite($fh, $message . "\r\n");
        fclose($fh);
    }

    public function getImageImport(){
        // download files
        $this->logMessage("Starting Image download" . date("Y-m-d H:i:s"));
        $remoteDir = '/images/weekly/full';
        $localDir = 'media/import/images';
        return "images-monthly-20151012.zip";
        // return $this->SFTPGetFile($remoteDir, $localDir);
    }

    public function getDataImport(){
        $this->logMessage("Starting " . date("Y-m-d H:i:s"));

        $remoteDir = '/xml/weekly/custom';
        $localDir = 'media/import';
        return simplexml_load_file('media/import/feed-monthly-230315-SajidKhan-1456-879.xml');
        // return simplexml_load_file('media/import/'.$this->SFTPGetFile($remoteDir, $localDir));
    }

    public function SFTPGetFile($remoteDir, $localDir){
        $host = 'download.cddata.co.uk';
        $port = 22;
        $username = 'Averroes';
        $password = 'iXzDTWwm';
        // $remoteDir = '/xml/weekly/custom';
        // $localDir = 'media/import';
        // return "images-monthly-20151012.zip";
        try {
            if (!function_exists("ssh2_connect")){
                die('Function ssh2_connect not found, you cannot use ssh2 here');
            }
            if (!$connection = ssh2_connect($host, $port)){
                die('Unable to connect');
            }
            if (!ssh2_auth_password($connection, $username, $password)){
                die('Unable to authenticate.');
            }

            if (!$stream = ssh2_sftp($connection)){
                die('Unable to create a stream.');
            }

            if (!$dir = opendir("ssh2.sftp://$stream{$remoteDir}")){
                // echo '<pre>' . print_r("ssh2.sftp://$stream{$remoteDir}", true) . '</pre>';
                die('Could not open the directory. Have you connected manually to store the key?');
            }
            // get all file
            $files = array();
            while (false !== ($file = readdir($dir)))
            {
                if ($file == "." || $file == "..")
                    continue;
                $files[] = $file;
            }

            // get the last file.
            $file_name = $files[11];
            // $check = 0;
            // $date_check = '';
            // foreach ($files as $name) {
            //     $fileRead1 = "ssh2.sftp://{$stream}/{$remoteDir}/{$name}";
            //     if($check == 0){
            //         $date_check = strtotime(date("m/d/Y", filemtime($fileRead1)));
            //         $file_name = $name;
            //         var_dump(date("m/d/Y", filemtime($fileRead1)));
            //     }else{
            //         if ($date_check < strtotime(date("m/d/Y", filemtime($fileRead1)))) {
            //             $date_check = strtotime(date("m/d/Y", filemtime($fileRead1)));
            //             $file_name = $name;
            //         }
            //     }
            //     $check++;
            // }
            // Read and Download file to local
            $fileRead = "ssh2.sftp://{$stream}/{$remoteDir}/{$file_name}";

            if (!$remote = @fopen($fileRead, 'r'))
            {
                echo "Unable to open remote file: $file_name\n";
            }

            if (!$local = @fopen($localDir . '/' . $file_name, 'w'))
            {
                echo "Unable to create local file: $file_name\n";
            }

            $read = 0;
            $filesize = filesize($fileRead);

            while ($read < $filesize && ($buffer = fread($remote, $filesize - $read)))
            {
                $read += strlen($buffer);
                if (fwrite($local, $buffer) === FALSE)
                {
                    echo "Unable to write to local file: $file_name\n";
                    break;
                }
            }
            fclose($local);
            fclose($remote);
            return $file_name;
            

        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }


    /*
    * $sku
    *
    * Delete product
    * */
    public function deleteProduct($sku)
    {
        
        try{
            $this->deleteCount++;
            $this->totalCount++;
            Mage::register('isSecureArea', true);
            $product = Mage::getModel('catalog/product')->load(Mage::getModel('catalog/product')->getResource()->getIdBySku($sku));
            $product->delete();
            Mage::unregister('isSecureArea'); /* un set secure admin area*/
        }catch (Exception $e){
            $this->logMessage("Deleting product");
            $this->logMessage($e->getMessage());
            var_dump($e->getMessage());
            Mage::log($e->getMessage());
        }
    }

    /*
    * $product, $array
    *
    * Modifile product
    * */
    public function modifyProduct($product,$array)
    {
        
        try{
            $_product = Mage::getModel('catalog/product')->load(Mage::getModel('catalog/product')->getResource()->getIdBySku((string)$product->Pip_code));
            if($_product->getId()) {
                $this->modifyCount++;
                $this->totalCount++;
                if ((string)$product->Adjustment_type == 'a') {
                    $this->_emptyGroupPrices($_product);
                    $_product->setPrice((string)$product->Retail_price);
                }
                if ((string)$product->Adjustment_type == 'r') {
                    $this->_emptyGroupPrices($_product);
                    $_product->setPrice((string)$product->Retail_price);
                }
                if ((string)$product->Adjustment_type == 'c') {
                    $this->_emptyGroupPrices($_product);
                    $_product = $this->setDataProduct($product, $array, $_product);
                }
                if ((string)$product->Adjustment_type == 'i') {
                    $this->_emptyGroupPrices($_product);
                    $_product->setPrice((string)$product->Retail_price);
                }
                try {
                    $_product->getResource()->save($_product);
                    $_product->save($_product);
                    // $_product->save();
                } catch (Exception $e) {
                    $this->logMessage("Modifying product");
                    $this->logMessage($e->getMessage());
                    var_dump($e->getMessage());
                }
            }else{
                $this->saveNewProduct($product, $array);
            }
        }catch (Exception $e){
            $this->logMessage("Modifying product");
            $this->logMessage($e->getMessage());
            var_dump($e->getMessage());
            Mage::log($e->getMessage());
        }
    }

    /*
    * $productdata, $array, $_product
    *
    * New product
    * */
    public function setDataProduct($product, $array,$_product, $new = false)
    {
        try {
            // get value for file multi select Other_Code
            $array['other_code_description'] = array();

            foreach ($product->Other_Codes->Other_Code as $otherCode) {
                array_push($array['other_code_description'], (int)$this->setOtherCodeDescriptionValue($otherCode->Code_Description));
            }

            // get value for file multi select Monograph
            $array['monograph_description'] = array();
            foreach ($product->Monographs->Monograph as $monograph) {
                array_push($array['monograph_description'], (int)$this->setMonographDescriptionValue($monograph->Monograph_Description));
            }

            $_product
                ->setName((string)$product->Till_roll_extended) //product name
                ->setManufacturer(28) //manufacturer id
                ->setSpecialFromDate((string)$product->Promotion_Start)//special price from (MM-DD-YYYY)
                ->setSpecialToDate((string)$product->Promotion_End)//special price to (MM-DD-YYYY)
                ->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
                ->setTaxClassId($this->setDefaultTaxId((string)$product->Vat_code))// Tax Class $product->Vat_code
                ->setShortDescription('Comming soon...')
                ->setStockData(array(
                        'use_config_manage_stock' => 0, //'Use config settings' checkbox
                        'manage_stock'=>1, //manage stock
                        'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
                        'max_sale_qty'=>2, //Maximum Qty Allowed in Shopping Cart
                        'is_in_stock' => 1, //Stock Availability
                        'qty' => intval($product->Quantity)?:0,
                    )
                )
                ->setCategoryIds(array($this->setCatagoryId((string)$product->Main_Product_Classification))); //assign product to categories
            if ($product->Retail_price){
                // can be null
                $_product->setPrice((string)$product->Retail_price);//price in form 11.22
            }
            $_product->setdata('description', $product->Description);
            // Set data for textfileds New attribute
            $_product
                ->setCompanyCode((string)$array['company_code'])
                ->setCompanyName((string)$array['Company_Name'])
                ->setBrandCode((string)$array['Brand_Code'])
                ->setBrandName((string)$array['Brand_Name'])
                ->setSubbrandName((string)$array['Subbrand'])
                ->setGroupCode((string)$array['Group_Code'])
                ->setParentGroupCode((string)$array['Parent_Code'])
                ->setPipCode((string)$product->Pip_code)
                ->setSizePrescriptionProduct((string)$product->Size)
                ->setFormulationIngredient((string)$product->Formulations->Formulation->Formulation_Ingredient)
                ->setFormulationStrength((string)$product->Formulations->Formulation->Formulation_Strength)
                ->setProductDimensionsWidth($product->Product_Dimensions->Width)
                ->setProductDimensionsWeight('1')//tim
                ->setProductDimensionsHeight($product->Product_Dimensions->Height)
                ->setProductDimensionsDepth($product->Product_Dimensions->Depth)
                ->setOuterDimensionsWidth($product->Outer_Dimensions->Width)
                ->setOuterDimensionsWeight($product->Outer_Dimensions->Weight)
                ->setOuterDimensionsHeight($product->Outer_Dimensions->Height)
                ->setOuterDimensionsDepth($product->Outer_Dimensions->Depth)
                ->setShipperDimensionsWidth($product->Shipper_Dimensions->Width)
                ->setShipperDimensionsWeight($product->Shipper_Dimensions->Weight)
                ->setShipperDimensionsHeight($product->Shipper_Dimensions->Height)
                ->setShipperDimensionsDepth($product->Shipper_Dimensions->Depth);

            // Set data for select option and multiselect fields New attribute

            $_product->setData('drug_codes_legal', (string)$product->Drug_Codes->Legal);
            // Checking Prescription product
            if(substr((string)$product->Drug_Codes->Legal,0,3) == 'POM'){
                $_product->setIsPrescriptionProduct(1);
                $_product->setProductDimensionsWeight('0');
            }
            // Set group price
            if (isset($product->Trade_price)) {
                // check for a product ID
                // if ($_product->getId() > 0){
                    $productID = $_product->getId();
                    $transaction = Mage::getSingleton('core/resource')->getConnection('core_write');
                    try {
                        $transaction->beginTransaction();

                        $transaction->query('DELETE FROM catalog_product_entity_group_price WHERE entity_id = "'.$productID . '"');

                        $transaction->commit();
                    } catch (Exception $e) {
                        $this->logMessage("FAILED TO REMOVE GROUP PRICE: " . $e->getMessage());
                        $transaction->rollBack(); // if anything goes wrong, this will undo all changes you made to your database
                    }
                    // $sql = Mage::getSingleton('core/resource')->getConnection('core_write');
                    // $sql->query('delete from `catalog_product_entity_group_price` where `entity_id` = '.$_product->getId());
                // }
                $group_prices = array();
                $new_price = array(array ('website_id'=>0,
                    'cust_group'=>2,
                    'price'=>$product->Trade_price));
                $group_prices = array_merge($group_prices, $new_price);
                $_product->setData('group_price', $group_prices);
            }
            $_product->setData('options_container', '1');
            $_product->setData('drug_codes_tariff', $this->setDrugCodesTariffValue((string)$product->Drug_Codes->Tariffs->Tariff));
            $_product->setData('other_code_description', $array['other_code_description']);
            $_product->setData('monograph_description', $array['monograph_description']);

            // $this->logMessage(print_r($product->Other_Codes));
            // die();
            foreach($product->Other_Codes->Other_Code as $oc => $data){
                $description = $data->Code_Description->__toString();
                $value = $data->Code_Value->__toString();
                if ($description == "EAN"){
                    // we have an image reference.
                    if($this->saveImageToProduct($value, $_product)){
//                        $this->logMessage("Added image");
                    } else {
                        $this->logMessage("Can't find image file for " . $value);
                    }
                }
            }
            return $_product;

        } catch (Exception $e) {
            Mage::log($e->getMessage());
            var_dump($e->getMessage());
        }
    }

    public function saveImageToProduct($image, $_product){
        $imageFilename      = Mage::getBaseDir('media') . DS . 'import'. DS . 'images' . DS . 'unzipped' . DS . 'A_' . $image . '.jpg';
        // $imageFilename = "/var/www/iwantmymeds/iwantmymeds/media/import/images/unzipped/A_" . $image . ".jpg";
//        $this->logMessage('hi' . $imageFilename);
        // configure image to be 3 main image types as we only get the one image from FTP.
        if (file_exists($imageFilename)){
//            $this->logMessage('file found');
            $mediaAttribute = array (
                'thumbnail',
                'small_image',
                'image'
            );

            $_product->addImageToMediaGallery( $imageFilename , $mediaAttribute, false, false );
            return true;
        } else {
//            $this->logMessage('File lost');
            return false;
        }
    }

    /*
    * $product, $array
    *
    * New product
    * */
    public function saveNewProduct($product, $array)
    {
//        $this->logMessage("New");
        $_product = Mage::getModel('catalog/product')->load(Mage::getModel('catalog/product')->getResource()->getIdBySku((string)$product->Pip_code));
        if($_product->getId()) {
            $this->modifyProduct($product,$array);
        } else {
            try {
                $this->newCount++;
                $this->totalCount++;
                $attributeSetName = "Prescription Product"; // get attribute set by name, not ID.
                $entityTypeId = Mage::getModel('eav/entity')
                    ->setType('catalog_product')
                    ->getTypeId();
                $attributeSetId     = Mage::getModel('eav/entity_attribute_set')
                        ->getCollection()
                        ->setEntityTypeFilter($entityTypeId)
                        ->addFieldToFilter('attribute_set_name', $attributeSetName)
                        ->getFirstItem()
                        ->getAttributeSetId();
                // create the product
                $_product = Mage::getModel('catalog/product');
                $_product->setWeight(1.0000)
                    ->setStatus(1)//product status (1 - enabled, 2 - disabled)
                    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)//catalog and search visibility
                    ->setWebsiteIds(array(1))//website ID the product is assigned to, as an array
                    ->setStoreId(1)
                    ->setAttributeSetId($attributeSetId)//ID of a attribute set named 'Prescription Product'
                    ->setTypeId('simple')//product type
                    ->setCreatedAt(strtotime('now'))//product creation time
                    ->setSku((string)$product->Pip_code); //SKU
                    // $this->logMessage(print_r('hi', true));
                $_product = $this->setDataProduct($product, $array,$_product, true);
                // $this->logMessage(print_r('ho', true));
                try {
                    $_product->getResource()->save($_product);
                    $_product->save($_product);
                    // $_product->save();
                    // $this->logMessage(print_r('away', true));
                } catch (Exception $e) {
//                    $this->logMessage("Saving New product 1");
                    $this->logMessage($e->getMessage());
                    var_dump($e->getMessage());
                }
            } catch (Exception $e) {
//                $this->logMessage("Saving New product 2");
                $this->logMessage($e->getMessage());
                Mage::log($e->getMessage());
                var_dump($e->getMessage());
            }
        }
    }


    /*
    * Param $array
    *
    * Set default value for attribute product with company , Brand and Group
    *
    * return TRUE
    * */
    public function saveProduct($product, $array)
    {
        
        try {
            $this->setAdjustmentTypeValue($product,$array);
        } catch (Exception $e) {
//            $this->logMessage("Saving Product");
            $this->logMessage($e->getMessage());
            Mage::log($e->getMessage());
            var_dump($e->getMessage());
        }
    }

    /*
     * param $Vat_code
     *
     * return Tax id
     *
     * */
    public function setDefaultTaxId($vat_code)
    {
        $name = 'tax_class_id';
        $options = $this->loadDataAttributeByCode($name);
        switch ($vat_code) {
            case '.':
                $lable = 'No VAT (Generic, Virtual Generic and Virtual Special products)';
                break;
            case 'E':
                $lable = 'Exempt';
                break;
            case 'H':
                $lable = 'High (Currently VAT rate N/A)';
                break;
            case 'L':
                $lable = 'Low (5%)';
                break;
            case 'S':
                $lable = 'Standard (20%)';
                break;
            case 'Z':
                $lable = 'Zero';
                break;
        }
        return $this->loadIdOptionAttributeLable($lable,$options);
    }

    /*
     * param $Main_Product_Classification
     *
     * return catagory id
     *
     * */
    public function setCatagoryId($main_product_classification)
    {
        switch ($main_product_classification) {
            case 'A':
                $name = 'Appliances';
                break;
            case 'B':
                $name = 'Babycare';
                break;
            case 'C':
                $name = 'Cosmetics';
                break;
            case 'D':
                $name = 'Dressings';
                break;
            case 'E':
                $name = 'Ethical';
                break;
            case 'F':
                $name = 'Food & Drink';
                break;
            case 'G':
                $name = 'Garden Products';
                break;
            case 'H':
                $name = 'Household';
                break;
            case 'J':
                $name = 'Virtual Generics';
                break;
            case 'K':
                $name = 'Virtual Specials';
                break;
            case 'N':
                $name = 'Nutritional Supplements';
                break;
            case 'O':
                $name = 'OTC';
                break;
            case 'P':
                $name = 'Photographic';
                break;
            case 'S':
                $name = 'Sundries';
                break;
            case 'T':
                $name = 'Toiletries';
                break;
            case 'V':
                $name = 'Veterinary Products';
                break;
            case 'X':
                $name = 'Generics';
                break;
        }
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');
        foreach ($categories as $category)
        {
            if ($category->getIsActive()) {
                if (strtolower($category->getName()) == strtolower($name)) {
                    return intval($category->getId());
                }
            }
        }
    }

    /*
     * Function fix bugs Error with Group price
     * Can not modifie again product
     * */
    private function _emptyGroupPrices($product) {
        /* @var $sql Mage_Core_Model_Resource */
        $sql = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql->query('delete from `catalog_product_entity_group_price` where `entity_id` = '.$product->getId());
    }

    /*
     * Loading all value and id attribute by attribute code
     * Return Array 
     * */
    public function loadDataAttributeByCode($name){
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($name)->getFirstItem();
        $attributeId = $attributeInfo->getAttributeId();
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $options = $attribute ->getSource()->getAllOptions(false);
        return $options;
    }

    /*
     * Loading id option off attribute flow by value
     * Return id 
     * */
    public function loadIdOptionAttributeLable($lable,$options){
        foreach ($options as $option) {
            if (strtolower($option['label']) == strtolower($lable)) {
                return intval($option['value']);
            }
        }
    }

    /*
     * Cron job import database flow by new file on server download
     *
     * */

    public function cronJobImportImages(){
//        connect to image FTP
        $this->logMessage("Starting Image Import");
        $images = $this->getImageImport();
//        $this->logMessage($images);
        $zip = new ZipArchive;
        // $this->logMessage(Mage::getBaseDir('media') . DS . 'import'. DS . 'images' . DS . $images);
        $result = $zip->open(Mage::getBaseDir('media') . DS . 'import'. DS . 'images' . DS . $images);
        if ($result === true){
            $this->logMessage("Unzipped");
            $zip->extractTo(Mage::getBaseDir('media') . DS . 'import'. DS . 'images' . DS . "unzipped");
            $zip->close();
            $this->logMessage("Done unzipping");
            // now, lets loop through the files

        } else {
            Mage::log("Unzip failed. Error code: " . $result);
        }
//        $this->logMessage(print_r($result, true));
    }

    public function cronJobImportProduct()
    {
        $this->logMessage("Starting");
        $this->cronJobImportImages();
        $xml = $this->getDataImport();
        foreach ($xml->Company as $company) {

            $array = array();
            $array['company_code'] = (string)$company->Code;
            $array['Company_Name'] = (string)$company->Company_Name;
            foreach ($company->Brand as $brands) {
                $array['Brand_Code'] = (string)$brands->Brand_Code;
                $array['Brand_Name'] = (string)$brands->Brand_Name;

                // Checking with have been tags subbrand
                if (isset($brands->Groups->Subbrand)) {
                    $array['Subbrand'] = (string)$brands->Groups->Subbrand->Subbrand_Name;
                    foreach ($brands->Groups->Subbrand->Group as $groups) {
                        $array['Group_Code'] = (string)$groups->Group_Code;
                        $array['Group_Name'] = (string)$groups->Group_Name;
                        if (isset($groups->Product)) {
                            foreach ($groups->Product as $products) {
                                $this->saveProduct($products, $array);
                            }
                        }
                        foreach ($groups->Group as $chilgroup) {
                            $array['Parent_Code'] = (string)$chilgroup->Parent_Code;
                            foreach ($chilgroup->Product as $product) {
                                $this->saveProduct($product, $array);
                            }
                        }
                    }
                } else {
                    foreach ($brands->Groups->Group as $groups) {
                        $array['Group_Code'] = (string)$groups->Group_Code;
                        $array['Group_Name'] = (string)$groups->Group_Name;
                        if (isset($groups->Product)) {
                            foreach ($groups->Product as $products) {
                                $this->saveProduct($products, $array);
                            }
                        }
                        foreach ($groups->Group as $chilgroup) {
                            $array['Parent_Code'] = (string)$chilgroup->Parent_Code;
                            foreach ($chilgroup->Product as $product) {
                                $this->saveProduct($product, $array);
                            }
                        }
                    }
                }
            }
        }
        $this->logMessage("Done");
        $this->logMessage("Total: " . $this->totalCount);
        $this->logMessage("New: " . $this->newCount);
        $this->logMessage("Modified: " . $this->modifyCount);
        $this->logMessage("Deleted: " . $this->deleteCount);
    }

    /*
       * Param $Tariff this is string
       *
       * Return value drug_codes_legal on back-end
       *
       * */
    public function setDrugCodesTariffValue($tariff)
    {
        $name = 'drug_codes_tariff';
        $options = $this->loadDataAttributeByCode($name);

        if($tariff == 'SL' ){
            $lable = 'Selected List (Drugs and other substances not to be prescribed under the NHS Pharmaceutical Services (Part XVIIIA of the England and Wales Drug Tariff)';

        }elseif($tariff == 'BS' ){
            $lable = 'Borderline Substances, ACBS Approved (Part XV of the England and Wales Drug Tariff)';

        }elseif($tariff == 'DT' ){
            $lable = 'Drug Tariff Item (England and Wales) - Generics & Virtual Specials';

        }elseif($tariff == 'DT A' ){
            $lable = 'Drug Tariff Item & Category (England and Wales) - Virtual Generics only';

        }elseif($tariff == 'DT C' ){
            $lable = 'Drug Tariff Item & Category (England and Wales) - Virtual Generics only';

        }elseif($tariff == 'DT M' ){
            $lable = 'Drug Tariff Item & Category (England and Wales) - Virtual Generics only';

        }elseif($tariff == 'N DT' ){
            $lable = 'Non Drug Tariff Item - Virtual Generics only';
        }
        return $this->loadIdOptionAttributeLable($lable,$options);
    }

    /*
      * Param $other_code this is string
      *
      * Return value drug_codes_legal on back-end
      *
      * */

    public function setOtherCodeDescriptionValue($other_code)
    {
        $name = 'other_code_description';
        $options = $this->loadDataAttributeByCode($name);

        if($other_code == 'EAN'){
            $lable = 'Current EAN Code';
        }elseif($other_code == 'OLD EAN'){
            $lable = 'Old EAN Code';
        }elseif($other_code == 'Outer EAN'){
            $lable = 'Current Outer EAN Code';
        }elseif($other_code == 'OLD outer EAN'){
            $lable = 'Old Outer EAN Code';
        }elseif($other_code == 'shipper EAN'){
            $lable = 'Current Shipper EAN Code';
        }elseif($other_code == 'OLD shipper EAN'){
            $lable = 'Old Shipper EAN Code';
        }elseif($other_code == 'AMPP'){
            $lable = 'Current AMPP Code';
        }elseif($other_code == 'OLD AMPP'){
            $lable = 'Old AMPP Code';
        }elseif($other_code == 'AMPP Manf'){
            $lable = 'AMPP Manufacturer Code';
        }elseif($other_code == 'VMPP'){
            $lable = 'Current VMPP Code';
        }elseif($other_code == 'OLD VMPP'){
            $lable = 'Old VMPP Code';
        }elseif($other_code == 'Image'){
            $lable = 'Picture Library EAN Referencing Code';
        }elseif($other_code == 'EAN'){
            $lable = 'Picture Library EAN Referencing Code';
        }
        return $this->loadIdOptionAttributeLable($lable,$options);
    }


    /*
     * Param $monograph_description this is string
     *
     * Return value drug_codes_legal on back-end
     *
     * */
    public function setMonographDescriptionValue($monograph_description)
    {
        $name = 'monograph_description';
        $options = $this->loadDataAttributeByCode($name);
        return $this->loadIdOptionAttributeLable($monograph_description,$options);
    }

    /*
     * Param $Adjustment_type this is string
     *
     * Return Action for product
     *
     * */
    public function setAdjustmentTypeValue($product,$array)
    {
        if((string)$product->Adjustment_type == 'd') {
            $this->deleteProduct((string)$product->Pip_code);
        }
        else if((string)$product->Adjustment_type == 'n') {
            $this->saveNewProduct($product,$array);
        }
        else{
            $this->modifyProduct($product,$array);
        }
    }
    /*
     * Function observer for action remove item on cart
     * */
    public function removeQuoteItem(Varien_Event_Observer $observer){
        $sessionfree = Mage::getSingleton('core/session', array('name' => 'frontend'));
        $items = $observer->getQuoteItem()->getProduct()->getData();
        $productId = $items['entity_id'];
        $models = Mage::getModel('catalog/product');
        $product = $models->load($productId);
        $productPrescription = $product->getIsPrescriptionProduct();
        if($productPrescription == '1'){
            $checkIsPrescription = 0;
            $cart = Mage::getModel('checkout/cart')->getQuote();
            foreach ($cart->getAllItems() as $cart_item) {
                $id = $cart_item->getProduct()->getId();
                $items = Mage::getModel('catalog/product')->load($id);
                if($items->getIsPrescriptionProduct() == '1'){
                    $checkIsPrescription++;
                    // print('$items '.$id);
                }
            }
            if($checkIsPrescription == 0){
                $sessionfree->unsetData('perscription_check');
                $sessionfree->unsPerscriptionCheck();
            }

        }
    }

    public function modifyPrice(Varien_Event_Observer $observer){

        $noPay =  Mage::getStoreConfig(self::NO_PAY);
        $pay =  Mage::getStoreConfig(self::PAY);
        $private = Mage::getStoreConfig(self::PRI);
        $payMethod =  Mage::getSingleton("core/session")->getPerscriptionCheck();
        $item = $observer->getQuoteItem();
        $price= $item->getProduct()->getData('price');
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        // check is_prescription_product
        if($item->getProduct()->getData('is_prescription_product') == '1'){
//            amend price so that VAT is taken into account
            if($payMethod === 'no_pay'){
                $exemption =  Mage::getSingleton("core/session")->getPrescriptionExemptionCheck();
                if ($exemption > 0){
                    $item->getProduct()->setIsSuperMode(true);
                    $item->setCustomPrice($noPay);
                    $item->setOriginalCustomPrice($noPay);
                }
            }
            if($payMethod === 'pay'){
                $item->getProduct()->setIsSuperMode(true);
                $item->setCustomPrice($pay);
                $item->setOriginalCustomPrice($pay);
            }if($payMethod ==='private')
            {
                $item->getProduct()->setIsSuperMode(true);
                $item->setCustomPrice($private);
                $item->setOriginalCustomPrice($private);
            }
        }
    }

    public function updatePrescriptionPrice(Varien_Event_Observer $observer){
        $noPay =  Mage::getStoreConfig(self::NO_PAY);
        $pay =  Mage::getStoreConfig(self::PAY);
        $private = Mage::getStoreConfig(self::PRI);
        $payMethod =  Mage::getSingleton("core/session")->getPerscriptionCheck();
//        $item = $observer->getQuoteItem();



        $product = $observer->getEvent()->getProduct();
        $price= $product->getData('price');
        // Do your queries to your custom tables here and if necessary ..


        if($product->getData('is_prescription_product') == '1'){
//            amend price so that VAT is taken into account
            if($payMethod === 'no_pay'){
                $exemption =  Mage::getSingleton("core/session")->getPrescriptionExemptionCheck();
                if ($exemption > 0){
                    $product->setFinalPrice($noPay);
                }
            }
            if($payMethod === 'pay'){
//                $item->getProduct()->setIsSuperMode(true);
//                $item->setCustomPrice($pay);
//                $item->setOriginalCustomPrice($pay);
//                $item->setFinalPrice($pay);
                $product->setFinalPrice($pay);
            }if($payMethod ==='private')
            {
//                $item->getProduct()->setIsSuperMode(true);
//                $item->setCustomPrice($private);
//                $item->setOriginalCustomPrice($private);
//                $item->setFinalPrice($private);
                $product->setFinalPrice($private);
            }
        } else {
            $product->setFinalPrice($price);
        }
    }

    public function updatePrescriptionCategoryListPrice(Varien_Event_Observer $observer){
        $products = $observer->getCollection();
        $noPay =  Mage::getStoreConfig(self::NO_PAY);
        $pay =  Mage::getStoreConfig(self::PAY);
        $private = Mage::getStoreConfig(self::PRI);
        $payMethod =  Mage::getSingleton("core/session")->getPerscriptionCheck();
        foreach( $products as $product ){
            if ($product->getData('is_prescription_product') == '1'){
                if($payMethod === 'no_pay'){
                    $exemption =  Mage::getSingleton("core/session")->getPrescriptionExemptionCheck();
                    if ($exemption > 0){
                        $product->setFinalPrice($noPay);
                        $product->setPrice($noPay);
                    }
                }
                if($payMethod === 'pay'){
                    $product->setFinalPrice($pay);
                    $product->setPrice($pay);
                }if($payMethod ==='private')
                {
                    $product->setFinalPrice($private);
                    $product->setPrice($private);
                }
            }
        }
    }

    public function saveOrderPrescriptionExemption(Varien_Event_Observer $observer){
        $exemption =  Mage::getSingleton("core/session")->getPrescriptionExemptionCheck();
        if ($exemption > 0){
            $exemptions = Mage::getResourceModel('prescriptioncheckout/prescription_collection');
            foreach($exemptions as $ex){
                if ($ex->getID() == $exemption){
                    $exemptionText = '<b>' . $ex->getTitle() . '</b>: ' . $ex->getDescriptions();
                }
            }
            $orderIds=$observer->getData('order_ids');
            foreach ($orderIds as $orderId) {
                $order = new Mage_Sales_Model_Order();
                $order->load($orderId);

                $order->setData('prescription_exemption',$exemptionText);
                $order->save();
            }
            Mage::getSingleton('core/session')->getPrescriptionExemptionCheck('');
        }
    }
}