<?php

class Ecommage_Prescriptioncheckout_Block_Adminhtml_Prescription_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('prescriptionTablerateGrid');
        $this->_exportPageSize = 10000;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('prescriptioncheckout/prescription_collection');


        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('prescriptioncheckout');

        $this->addColumn('prescription_id', array(
            'header' => $helper->__('ID'),
            'index'  => 'prescription_id'
        ));

        $this->addColumn('title', array(
            'header' => $helper->__('Title'),
            'index'  => 'title'
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('prescriptioncheckout')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('prescriptioncheckout')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}