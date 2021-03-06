<?php

namespace Mapbender\CoreBundle\Element;

use Mapbender\CoreBundle\Component\Element;
//use Symfony\Component\DependencyInjection\ContainerInterface;

class Ruler extends Element {
    static public function getClassTitle() {
        return 'Line/Area ruler';
    }

    static public function getClassDescription() {
        return "Please give me a description";
    }

    static public function getClassTags() {
        return array();
    }

    public function getAssets() {
        return array(
            'js' => array('@MapbenderCoreBundle/Resources/public/mapbender.element.ruler.js'),
            //TODO: Split up
            'css' => array('@MapbenderCoreBundle/Resources/public/mapbender.elements.css'));
    }

    public static function getDefaultConfiguration() {
        return array(
            'type' => 'line');
    }

    public function getWidgetName() {
        return 'mapbender.mbRuler';
    }

    public function render() {
        return $this->container->get('templating')
            ->render('MapbenderCoreBundle:Element:measure_dialog.html.twig',
                array(
                    'id' => $this->getId(),
                    'configuration' => $this->getConfiguration()));
    }
}

