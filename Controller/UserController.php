<?php

/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 08/05/17
 * Time: 19:38.
 */

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\AdminBundle\Services\UserManager;
use Umbrella\CoreBundle\Controller\BaseController;

/**
 * Class UserController.
 *
 * @Route("/user")
 */
class UserController extends BaseController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * UserController constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("")
     */
    public function indexAction(Request $request)
    {
        $table = $this->createTable($this->getParameter('umbrella_admin.user.user_crud.table'));
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return new JsonResponse($table->getApiResults());
        }

        return $this->render('@UmbrellaAdmin/DataTable/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     *
     * @param mixed|null $id
     */
    public function editAction(Request $request, $id = null)
    {
        if (null === $id) {
            $entity = $this->userManager->createUser();
        } else {
            $entity = $this->userManager->find($id);
            $this->throwNotFoundExceptionIfNull($entity);
        }

        $form = $this->createForm($this->getParameter('umbrella_admin.user.user_crud.form'), $entity, [
            'password_required' => !$entity->id,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->update($entity);

            return $this->jsResponseBuilder()
                ->closeModal()
                ->reloadTable()
                ->toastSuccess('message.entity_updated');
        }

        return $this->jsResponseBuilder()
            ->openModalView('@UmbrellaAdmin/User/edit.html.twig', [
                'form' => $form->createView(),
                'title' => $entity->id ? $this->trans('action.edit_user') : $this->trans('action.add_user'),
                'entity' => $entity,
            ]);
    }

    /**
     * @Route("/toggle-active/{id}/{value}", requirements={"id": "\d+"})
     *
     * @param mixed $id
     * @param mixed $value
     */
    public function toggleActiveAction($id, $value)
    {
        $entity = $this->userManager->find($id);
        $this->throwNotFoundExceptionIfNull($entity);

        $entity->active = 1 == $value;
        $this->userManager->update($entity);

        return $this->jsResponseBuilder();
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     *
     * @param mixed $id
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->userManager->find($id);
        $this->throwNotFoundExceptionIfNull($entity);
        $this->userManager->remove($entity);

        return $this->jsResponseBuilder()
            ->closeModal()
            ->reloadTable()
            ->toastSuccess('message.entity_deleted');
    }
}
