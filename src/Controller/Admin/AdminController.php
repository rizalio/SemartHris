<?php

namespace KejawenLab\Application\SemartHris\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdmin;
use KejawenLab\Application\SemartHris\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@kejawenlab.com>
 */
class AdminController extends EasyAdmin
{
    const DEFAULT_ROLE = 'ROLE_EMPLOYEE';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @param Request $request
     */
    protected function initialize(Request $request)
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface && $this->isGranted(self::ROLE_SUPER_ADMIN)) {
            $this->getDoctrine()->getManager()->getFilters()->disable('semart_soft_delete');
        }

        parent::initialize($request);

        $this->denyAccessUnlessGranted($this->entity['role'] ?? self::DEFAULT_ROLE);
        $action = $request->query->get('action', 'list');
        $this->denyAccessUnlessGranted($this->entity[$action]['role'] ?? self::DEFAULT_ROLE);
    }

    protected function editAction()
    {
        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));

            if ('undefined' === mb_strtolower($property)) {
                $property = 'deletedAt';
            }

            $easyadmin = $this->request->attributes->get('easyadmin');
            $entity = $easyadmin['item'];
            $this->updateEntityProperty($entity, $property, null);

            return new Response((int) $newValue);
        }

        return parent::editAction();
    }
}
