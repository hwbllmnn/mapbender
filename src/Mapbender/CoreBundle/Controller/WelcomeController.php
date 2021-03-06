<?php

/**
 * TODO: License
 */

namespace Mapbender\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Welcome controller.
 *
 * This controller can be used to display an list of available applications.
 * It has been seperated in it's own class so it can easily be added or
 * removed from the routing.
 *
 * @author Christian Wygoda
 */
class WelcomeController extends Controller {
    /**
     * List applications.
     *
     * Lists all applications (TODO: available to the current user)
     *
     * @Route("/")
     * @Template()
     */
    public function listAction() {
        //TODO: Effecient way to get only applications available to current
        //      user/token
        $applications = $this->get('mapbender')->getApplicationEntities();

        return array('applications' => $applications);
    }
}

