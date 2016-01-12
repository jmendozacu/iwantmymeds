<?php
class Ecommage_Myprescription_Model_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities() {
        return array(
            Ecommage_Myprescription_Model_Myprescription::ENTITY => array(
                'entity_model' => 'ecommage_myprescription/register',
                'table' => 'ecommage_myprescription/register',/* Maps to the config.xml > global > models > tc_skeleton_resource > entities > skeleton */
                'attributes' => array(
                    'name' => array(
                        'type' => 'text',
                        'label' => 'Name',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 10,
                        'position' => 10,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'phone_number' => array(
                        'type' => 'int',
                        'label' => 'Phone Number',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 11,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'mobile' => array(
                        'type' => 'int',
                        'label' => 'Mobile',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 12,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'email' => array(
                        'type' => 'varchar',
                        'label' => 'Email',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 13,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'dob' => array(
                        'type' => 'varchar',
                        'label' => 'Date Of Birth',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 13,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'nhs_number' => array(
                        'type' => 'varchar',
                        'label' => 'NHS number',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 14,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'doctor_name' => array(
                        'type' => 'text',
                        'label' => 'Doctor’s name and address',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 14,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'doctor_phone' => array(
                        'type' => 'int',
                        'label' => 'Doctor’s phone number',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 15,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'detail_medical' => array(
                        'type' => 'text',
                        'label' => 'Details of the medicines',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'position' => 16,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'status' => array(
                        'type' => 'int',
                        'label' => 'Status',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 10,
                        'position' => 17,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                )
            )
        );
    }
}