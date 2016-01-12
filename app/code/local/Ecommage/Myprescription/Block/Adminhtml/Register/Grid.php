<?php
class Ecommage_Myprescription_Block_Adminhtml_Register_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('registerGrid');
        $this->setDefaultDir('DESC');
        $this->_exportPageSize = 10000;
    }

    /**
     * prepare collection for block to display
     *
     * @return Ecommage_Myprescription_Block_Adminhtml_Register_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('ecommage_myprescription/myprescription')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Ecommage_Myprescription_Block_Adminhtml_Register_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => 'Id',
            'index'  => 'id',
            'align'     =>'left',
        ));
        $this->addColumn('name_and_address', array(
            'header' => 'Name and Address',
            'index'  => 'name_and_address',
            'align'     =>'left',
        ));

        $this->addColumn('email', array(
            'header' => 'Email',
            'index'  => 'email',
            'align'     =>'left',
        ));
        $this->addColumn('status', array(
            'header' => 'Status',
            'index'  => 'status',
            'align'     =>'left',
            'renderer'  => 'Ecommage_Myprescription_Block_Adminhtml_Register_Renderer_Status',
        ));
        $this->addColumn('action',
            array(
                'header'    => 'Action',
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('myprescription')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }


    /**
     * goto action edit when lick row on grid
     *
     * @return Ecommage_Myprescription_Adminhtml_RegisterrepeatController
     * Call to Function editAction
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
