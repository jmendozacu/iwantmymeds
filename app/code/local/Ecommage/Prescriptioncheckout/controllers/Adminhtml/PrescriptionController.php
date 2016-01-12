<?php
class Ecommage_Prescriptioncheckout_Adminhtml_PrescriptionController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Prescription'))->_title($this->__('Management Prescription'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('prescriptioncheckout/adminhtml_prescription'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('prescriptioncheckout/adminhtml_prescription_grid')->toHtml()
        );
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Prescription'))
            ->_title($this->__('Manage Prescription'));

        $model = Mage::getModel('prescriptioncheckout/prescription');
        $Prescription_Id = $this->getRequest()->getParam('id');
        if ($Prescription_Id) {
            $model->load($Prescription_Id);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('prescriptioncheckout')->__('item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            $this->_title($model->getTitle());
        } else {
            $this->_title(Mage::helper('prescriptioncheckout')->__('New Item'));
        }


        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('prescription_item', $model);

        // 5. render layout
        $this->renderLayout();
    }

    public function saveAction()
    {
        $redirectPath   = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {

            $model = Mage::getModel('prescriptioncheckout/prescription');

            // if news item exists, try to load it
            $prescriptionId = $this->getRequest()->getParam('prescription_id');
            if ($prescriptionId) {
                $model->load($prescriptionId);
            }
            $model->addData($data);

            try {
                $hasError = false;
                $model->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('prescriptioncheckout')->__('The prescription item has been saved.')
                );

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $redirectPath   = '*/*/edit';
                    $redirectParams = array('id' => $model->getId());
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('prescriptioncheckout')->__('An error occurred while saving the prescription item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }



    public function deleteAction()
    {
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {

                $model = Mage::getModel('prescriptioncheckout/prescription');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('prescriptioncheckout')->__('Unable to find a prescription item.'));
                }
                $model->delete();

                $this->_getSession()->addSuccess(
                    Mage::helper('prescriptioncheckout')->__('The prescription item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('prescriptioncheckout')->__('An error occurred while deleting the prescription item.')
                );
            }
        }

        $this->_redirect('*/*/');
    }
}