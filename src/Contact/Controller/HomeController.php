<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contact\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HomeController extends AbstractActionController {

    public function indexAction() {
        $contact = $this->getServiceLocator()->get('Contact\Model\Contact');

        $data['rows'] = $contact->getAllRows();
        return new ViewModel($data);
    }

    public function showfriendsAction($id) {
        $id = $this->params()->fromQuery('id', 0);
        $contact = $this->getServiceLocator()->get('Contact\Model\Contact');
        $city = $this->getServiceLocator()->get('Contact\Model\City');
        $friends['user']=$contact->getRow($id);
        $friends['rows'] = $contact->showFriends($id);
        $friends['rows1'] = $contact->showFriendsOfFriends($id, $friends);
        $friends['rows2'] = $contact->setSuggestedFriends($friends['rows1']);
        $friends['cities'] = $city->setSuggestedCities($friends, $id);
        return new ViewModel($friends);
    }

}
