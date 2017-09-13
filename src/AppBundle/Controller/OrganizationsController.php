<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Organizations;
use AppBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Organization controller.
 *
 */
class OrganizationsController extends Controller
{
    /**
     * Lists all organization entities.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $organizations = $em->getRepository('AppBundle:Organizations')->findAll();

        return $this->render('AppBundle::organizations/index.html.twig', array(
            'organizations' => $organizations,
        ));
    }

    /**
     * Creates a new organization entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $organization = new Organizations();
        $form = $this->createForm('AppBundle\Form\OrganizationsType', $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            return $this->redirectToRoute('organizations_show', array('id' => $organization->getId()));
        }

        return $this->render('AppBundle::organizations/new.html.twig', array(
            'organization' => $organization,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a organization entity.
     * @param Organizations $organization
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Organizations $organization)
    {
        $em = $this->getDoctrine()->getRepository(Users::class);

        $users = $em->findBy(array('organization' => $organization->getId()));

        return $this->render('AppBundle::organizations/show.html.twig', array(
            'organization' => $organization,
            'users' => $users
        ));
    }

    /**
     * Displays a form to edit an existing organization entity.
     * @param Request $request
     * @param Organizations $organization
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Organizations $organization)
    {
        $editForm = $this->createForm('AppBundle\Form\OrganizationsType', $organization);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organizations_edit', array('id' => $organization->getId()));
        }

        return $this->render('AppBundle::organizations/edit.html.twig', array(
            'organization' => $organization,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request) {
        $organizationId = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $organization = $em->getRepository(Organizations::class)->find($organizationId);
        if ($organization) {
            $users = $organization->getUsers();
            if ($users) {
                try {
                    foreach ($users as $user) {
                        $em->remove($user);
                    }
                } catch (Exception $e) {
                    $this->addFlash('error', 'Ошибка при удалении сотрудников: ' . $e->getMessage());
                }
            }
            try {
                $em->remove($organization);
                $em->flush();
            } catch (Exception $e) {
                $this->addFlash('error', 'Ошибка при удалении организации: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Нет такой организации');
        }
        return $this->redirectToRoute('organizations_index');
    }
}
