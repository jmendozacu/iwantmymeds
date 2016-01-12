<?php
class Ecommage_Myprescription_Adminhtml_RegisterrepeatController extends Mage_Adminhtml_Controller_Action{
    public function indexAction()
    {
        $this->_title($this->__('Register'))->_title($this->__('Management Register'));
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('myprescription/adminhtml_register'));
        $this->renderLayout();
    }

//    public function gridAction()
//    {
//        $this->loadLayout();
//        $this->getResponse()->setBody(
//            $this->getLayout()->createBlock('myprescription/adminhtml_register_grid')->toHtml()
//        );
//    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Register'))
            ->_title($this->__('Manage Register'));

        $model = Mage::getModel('ecommage_myprescription/myprescription');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('myprescription')->__('item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            $this->_title($model->getTitle());
        } else {
            $this->_title(Mage::helper('myprescription')->__('New Item'));
        }


        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('register_item', $model);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('myprescription/adminhtml_register_edit'))
            ->_addLeft($this->getLayout()->createBlock('myprescription/adminhtml_register_edit_tabs'));
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

            $model = Mage::getModel('ecommage_myprescription/myprescription');

            // if news item exists, try to load it
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->addData($data);

            try {
                $hasError = false;
                $model->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('myprescription')->__('The register item has been saved.')
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
                    Mage::helper('myprescription')->__('An error occurred while saving the register item.')
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

                $model = Mage::getModel('ecommage_myprescription/myprescription');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('myprescription')->__('Unable to find a register item.'));
                }
                $model->delete();

                $this->_getSession()->addSuccess(
                    Mage::helper('myprescription')->__('The register item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('myprescription')->__('An error occurred while deleting the Register item.')
                );
            }
        }

        $this->_redirect('*/*/');
    }
}