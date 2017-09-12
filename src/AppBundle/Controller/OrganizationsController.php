<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Organizations;
use AppBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Organization controller.
 *
 */
class OrganizationsController extends Controller
{
    /**
     * Lists all organization entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $organizations = $em->getRepository('AppBundle:Organizations')->findAll();

        return $this->render('organizations/index.html.twig', array(
            'organizations' => $organizations,
        ));
    }

    /**
     * Creates a new organization entity.
     *
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

        return $this->render('organizations/new.html.twig', array(
            'organization' => $organization,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a organization entity.
     *
     */
    public function showAction(Organizations $organization)
    {
        $deleteForm = $this->createDeleteForm($organization);

        $em = $this->getDoctrine()->getRepository(Users::class);

        $users = $em->findBy(array('organization' => $organization->getId()));

        return $this->render('organizations/show.html.twig', array(
            'organization' => $organization,
            'delete_form' => $deleteForm->createView(),
            'users' => $users
        ));
    }

    /**
     * Displays a form to edit an existing organization entity.
     *
     */
    public function editAction(Request $request, Organizations $organization)
    {
        $deleteForm = $this->createDeleteForm($organization);
        $editForm = $this->createForm('AppBundle\Form\OrganizationsType', $organization);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organizations_edit', array('id' => $organization->getId()));
        }

        return $this->render('organizations/edit.html.twig', array(
            'organization' => $organization,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a organization entity.
     *
     */
    public function deleteAction(Request $request, Organizations $organization)
    {
        $form = $this->createDeleteForm($organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($organization);
            $em->flush();
        }

        return $this->redirectToRoute('organizations_index');
    }

    /**
     * Creates a form to delete a organization entity.
     *
     * @param Organizations $organization The organization entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Organizations $organization)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('organizations_delete', array('id' => $organization->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
