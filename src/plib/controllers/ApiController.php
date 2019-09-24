<?php

use PleskExt\Siwecos\Helper;

class ApiController extends pm_Controller_Action
{
    protected $_accessLevel = 'admin';

    public function pingAction(): void
    {
        $this->_helper->json(Helper::getTime());
    }
}
