<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category", options={"expose": true})
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        // $em = $this->getDoctrine()->getManager();

        // $entities = $em->getRepository('AppBundle:Category')->findAll();


        // $repository = $this->getDoctrine()
        //     ->getRepository('AppBundle:Category');
        
        // // Query
        // $qb = $repository->createQueryBuilder('c');
        // $query = $qb
        //     ->select(array(
        //             'c.id',
        //             'c.name',
        //             'ccc.id AS parent'
        //         )
        //     )
        //     ->addSelect('(SELECT COUNT(cc.id) FROM AppBundle:Category cc WHERE c.id = cc.category) AS children')            
        //     ->leftJoin('AppBundle:Category', 'ccc', 'WITH', 'ccc.id = c.category')
        //     ->addOrderBy('c.name', 'ASC');

        // $result = $query->getQuery()->getResult();

        // return array(
        //     'title'    => 'Categorias',
        //     'result' => $result,
        // );


        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Category');
        
        // Query
        $qb = $repository->createQueryBuilder('c');
        $query = $qb
            ->select(array(
                    'c.id',
                    'c.name',
                )
            )
            ->addOrderBy('c.name', 'ASC');

        $search = $this->get('infinity.search')
            ->addButton(array(
                'label' => 'Novo',
                'icon' => 'add',
                'type' => 'fab',
                'action_type' => 'route',
                'action' => 'category_new',
                )
            )
            ->addColumn(array('name' => 'name', 'label' => 'Nome', 'type' => 'string', 'width' => '97%', 'non_numeric' => true))
            ->addColumn(array('name' => 'actions', 'label' => 'Ações', 'type' => 'actions', 'width' => '3%', 'actions' => array(
                    array('icon' => 'edit', 'label' => 'Editar', 'type' => 'route', 'route_name' => 'category_edit', 'arguments' => array('id' => ':id')),
                )
            ))
            ->setTranslatePrefix('category');

        $result = $query->getQuery()->getResult();

        return $this->render('AppBundle:Core:search.html.twig', array(
            'title' => 'Categorias',
            'search' => $search,
            'result' => $result,
        ));
    }
    /**
     * Creates a new Category entity.
     *
     * @Route("/", name="category_create")
     * @Method("POST")
     * @Template("AppBundle:Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/new", name="category_new")
     * @Method("GET")
     * @Template("AppBundle:Core:form-basic.html.twig")
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createCreateForm($entity);

        return array(
            'title'  => 'Nova categoria',
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method("GET")
     * @Template("AppBundle:Core:form-basic.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Category entity.
    *
    * @param Category $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Category entity.
     *
     * @Route("/{id}", name="category_update")
     * @Method("PUT")
     * @Template("AppBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    /**
     * Creates a form to delete a Category entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
