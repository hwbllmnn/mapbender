<?php

/**
 * TODO: License
 */

namespace Mapbender\CoreBundle\Component;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The base bundle class for all Mapbender3 bundles.
 *
 * Mapbender3 bundles are special in a way as they expose lists of their
 * elements, layers and templates for the central Mapbender3 service, which
 * aggregates these for use in the manager backend.
 *
 * @author Christian Wygoda
 */
class MapbenderBundle extends Bundle {
    /**
     * Return list of element classes provided by this bundle.
     * Each entry in the array is a fully qualified class name.
     *
     * @return array Array of element class names
     */
    public function getElements() {
        return array();
    }

    /**
     * Return list of layer classes provided by this bundle.
     * Each entry in the array is a fully qualified class name.
     *
     * @return array Array of layer class names
     */
    public function getLayers() {
        return array();
    }

    /**
     * Return list of template classes provided by this bundle.
     * Each entry in the array is a fully qualified class name.
     *
     * @return array() Array of template class names
     */
    public function getTemplates() {
        return array();
    }

    /**
     * Return list of administration controllers to include in the manager
     * backend.
     * The list should be an array of arrays, each having giving the integer
     * weight, the name and the route.
     *
     * @return array Array of admin controllers
     */
    public function getAdminControllers()
    {
        return array(
            //array(
            //    'weight' => 5,
            //    'name' => 'Users'
            //    'route' => 'mapbender_manager_user_index',
            //    'controllers' => array(
            //        'mapbender_manager_user',
            //        'mapbender_manager_group'
            //    )
            //)
        );
    }
}

