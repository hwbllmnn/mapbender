<?php

/**
 * TODO: License
 */

namespace Mapbender\CoreBundle;

use Mapbender\CoreBundle\Component\Application;
use Mapbender\CoreBundle\Component\ApplicationYAMLMapper;
use Mapbender\CoreBundle\Entity\Application as Entity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Mapbender - The central Mapbender3 service. Provides metadata about
 * available elements, layers and templates.
 *
 * @author Christian Wygoda
 */
class Mapbender {
    private $container;
    private $elements = array();
    private $layers = array();
    private $templates = array();
    private $adminControllers = array();

    /**
     * Mapbender constructor.
     *
     * Iterate over all bundles and if is an MapbenderBundle, get list
     * of elements, layers and templates.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $bundles = $container->get('kernel')->getBundles();
        foreach($bundles as $bundle) {
            if(is_subclass_of($bundle,
                'Mapbender\CoreBundle\Component\MapbenderBundle')) {

                $this->elements = array_merge($this->elements,
                    $bundle->getElements());
                $this->layer =  array_merge($this->layers,
                    $bundle->getLayers());
                $this->templates = array_merge($this->templates,
                    $bundle->getTemplates());

                $this->adminControllers += $bundle->getAdminControllers();
            }
        }
    }

    /**
     * Get list of all declared element classes.
     *
     * Element classes need to be declared in each bundle's main class getElement
     * method.
     *
     * @return array
     */
    public function getElements() {
        return $this->elements;
    }

    /**
     * Get list of all declared layer classes.
     *
     * Layer classes need to be declared in each bundle's main class getLayers
     * method.
     *
     * @return array
     */
    public function getLayers() {
        return $this->layers;
    }

    /**
     * Get list of all declared template classes.
     *
     * Template classes need to be declared in each bundle's main class
     * getTemplates method.
     *
     * @return array
     */
    public function getTemplates() {
        return $this->templates;
    }

    /**
     * Get list of all declared admin controllers.
     *
     * @return array
     */
    public function getAdminControllers()
    {
        return $this->adminControllers;
    }

    /**
     * Get the application for the given slug.
     *
     * Returns either application if it exists, null otherwise. If two
     * applications with the same slug exist, the database one will
     * override the YAML one.
     *
     * @return Application
     */
    public function getApplication($slug, $urls) {
        $entity = $this->getApplicationEntity($slug);
        if(!$entity) {
            return null;
        }

        return new Application($this->container, $entity, $urls);
    }

    /**
     * Get application entities
     *
     * @return array
     */
    public function getApplicationEntities() {
        $entities = array();

        $yamlMapper = new ApplicationYAMLMapper($this->container);
        $yamlEntities = $yamlMapper->getApplications();
        foreach($yamlEntities as $entity) {
            $entities[$entity->getSlug()] = $entity;
        }

        $dbEntities = $this->container->get('doctrine')
            ->getRepository('MapbenderCoreBundle:Application')
            ->findAll();
        foreach($dbEntities as $entity) {
            $entity->setSource(Entity::SOURCE_DB);
            $entities[$entity->getSlug()] = $entity;
        }

        return $entities;
    }

    /**
     * Get application entity for given slug
     *
     * @return Entity
     */
    public function getApplicationEntity($slug) {
        $entity = $this->container->get('doctrine')
            ->getRepository('MapbenderCoreBundle:Application')
            ->findOneBySlug($slug);
        if($entity) {
            $entity->setSource(Entity::SOURCE_DB);
            return $entity;
        }

        $yamlMapper = new ApplicationYAMLMapper($this->container);
        return $yamlMapper->getApplication($slug);
    }
}

