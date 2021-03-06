<?php

namespace Mapbender\WmcBundle\Element;

use Mapbender\CoreBundle\Component\Element;
use Mapbender\WmcBundle\Entity\Wmc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison;

class WmcStorage extends Element {
    static public function getClassTitle() {
        return "WMC Storage";
    }

    static public function getClassDescription() {
        return "Stores and loads WMC documents. Can provide a dialog for "
            . "selecting and saving.";
    }

    static public function getClassTags() {
        return array('WMC');
    }

    public function getAssets() {
        return array(
            'js' => array('mapbender.element.wmcstorage.js'),
            'css' => array());
    }

    public static function getDefaultConfiguration() {
        return array(
            'target' => null);
    }

    public function getWidgetName() {
        return 'mapbender.mbWmcStorage';
    }

    public function httpAction($action) {
        $response = new Response();
        $request = $this->get('request');
        $em = $this->get('doctrine')->getEntityManager();
        $repository = $this->get('doctrine')->getRepository('MapbenderWmcBundle:Wmc');

        switch($action) {
        case 'downloadDirect':
            $response->setContent(file_get_contents($request->files->get('wmcContent')));
            $response->headers->set('Content-Type', 'application/xml');
            return $response;
            break;
        case 'download':
            $response->setContent($request->get('wmcContent'));
            $response->headers->set('Content-Type', 'application/xml');
            $response->headers->set('Content-Disposition', 'attachment; filename=wmc.xml');
            return $response;
            break;

        case 'save':
            //TODO: owner shall be a reference to a UserInterface
            $owner = $this->get('security.context')->getToken()->getUser()->getUsername();
            $title = $request->get('title');
            $public = strtolower($request->get('public'));
            $public = $public === 'true' ? true : false;
            if(!$title) {
                throw new \Exception('You did not send a title for the WMC document.');
            }
            $crs = $request->get('crs');

            $wmcDocument = file_get_contents('php://input');

            $wmc = $repository->findOneBy(array(
                'title' => $title,
                'owner' => $owner
            ));

            $status = array(
                'code' => 'ok'
            );

            if($wmc) {
            } else {
                // Create new entity, persist, say thank you
                $wmc = new Wmc();
                $wmc->setTitle($title);
                $wmc->setOwner($owner);
                $wmc->setPublic($public);
                $wmc->setDocument($wmcDocument);
                $wmc->setCrs($crs);

                try {
                    $em->persist($wmc);
                    $em->flush();
                    $status['message'] = sprintf('Your WMC document was stored with id %d.', $wmc->getId());
                } catch (\Exception $e) {
                    $status['code'] = 'error';
                    $status['message'] = $e->getMessage();
                }
            }
            $response->setContent(json_encode($status));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
            break;

        case 'list':
            //TODO: owner shall be a reference to a UserInterface
            $owner = $this->get('security.context')->getToken()->getUser()->getUsername();
            $params = $request->get('params', array());
            if(!is_array($params)) {
                throw new \Exception('The params parameter must be an array.');
            }
            $params = array_merge($params, array('owner' => $owner));
            $qb = $repository->createQueryBuilder('w');

            $where = $qb->expr()->andx(
                $qb->expr()->eq('w.crs', ':crs'));

            if($params['private'] === 'true') {
                $where->add($qb->expr()->eq('w.owner', ':owner'));
            } else {
                $where->add(
                    $qb->expr()->orx(
                        $qb->expr()->eq('w.owner', ':owner'),
                        $qb->expr()->eq('w.public', 'true')
                    )
                );
            }

            $qb->add('where', $where);

            $qb->setParameter('crs',  $params['crs']);
            $qb->setParameter('owner', $owner);

            $list = $qb->getQuery()->getResult();

            foreach($list as &$item) {
                $item = array(
                    'id' => $item->getId(),
                    'title' => $item->getTitle()
                );
            }
            $response->setContent(json_encode($list));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
            break;

        case 'load':
            //TODO: owner shall be a reference to a UserInterface
            $owner = $this->get('security.context')->getToken()->getUser()->getUsername();
            $params = $request->get('params');
            if(!is_array($params)) {
                throw new \Exception('The params parameter must be an array.');
            }

            $qb = $repository->createQueryBuilder('w');

            $where = $qb->expr()->andx(
                $qb->expr()->orx(
                    $qb->expr()->eq('w.owner', ':owner'),
                    $qb->expr()->eq('w.public', 'true')
                )
            );
            foreach($params as $key => $value) {
                $where->add(new Comparison("w.$key", '=', ":$key"));
                $qb->setParameter($key, $value);
            }

            $qb->add('where', $where);

            $qb->setParameter('owner', $owner);

            $wmc = $qb->getQuery()->getSingleResult();
            if(!$wmc) {
                throw new \Exception('No WMC found for your paramters.');
            }
            $response->setContent($wmc->getDocument());
            $response->headers->set('Content-Type', 'application/xml');
            return $response;
            break;
        case 'delete':
            //TODO: owner shall be a reference to a UserInterface
            $owner = $this->get('security.context')->getToken()->getUser()->getUsername();
            $id = intval($request->get('id'));
            $wmc = $repository->findOneBy(array(
                'id' => $id,
                'owner' => $owner
            ));

            $em->remove($wmc);
            $em->flush();
            $response->setContent($id);
            return $response;
            break;
        }
        return parent::httpAction($action);
    }

    public function render() {
        return $this->container->get('templating')
            ->render('MapbenderWmcBundle:Element:wmcstorage.html.twig', array(
                'id' => $this->getId(),
                'configuration' => $this->getConfiguration()));
    }
}

