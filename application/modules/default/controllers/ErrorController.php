<?php

class Default_ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        //echo "<pre>Error";print_r($errors); exit;


        if (!$errors) {
            //this is for error that coming from accesscontrol.
            $this->view->message = '<div class="page-not-authorized"><div class="div-authorized"></div><p class="sry-text">You are not authorized to access this page.</p></div>';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = '<div class="page-not-404"><div class="div404"></div><p class="page-text">Page not found</p><p class="sry-text">Sorry, but the page you are looking for has not been found. Try checking URL for errors, then hit the refresh button on your browser</p></div>';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = '<div class="page-not-404"><div class="div503"></div><p class="page-text">Application error</p><p class="sry-text">Sorry, but the page you are looking for has not been found. Try checking URL for errors, then hit the refresh button on your browser</p></div>';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters:', $priority, var_export($errors->request->getParams(),true));
        }
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        
        return $log;
    }


}

