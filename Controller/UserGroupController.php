<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 13/05/17
 * Time: 18:19.
 */

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\AdminBundle\Services\UserGroupManager;

/**
 * Class UserGroupController.
 *
 * @Route("/usergroup")
 */
class UserGroupController extends BaseController
{

    /**
     * @var UserGroupManager
     */
    private $groupManager;

    /**
     * UserGroupController constructor.
     * @param UserGroupManager $groupManager
     */
    public function __construct(UserGroupManager $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    /**
     * @Route("")
     */
    public function indexAction(Request $request)
    {
        $table = $this->createTable($this->getParameter('umbrella_admin.user.group_crud.table'));
        $table->handleRequest($request);

        if ($table->isCallback()) {
            return new JsonResponse($table->getApiResults());
        }

        return $this->render('@UmbrellaAdmin/DataTable/index.html.twig', [
            'table' => $table
        ]);
    }

    /**
     * @Route("/edit/{id}", requirements={"id"="\d+"})
     */
    public function editAction(Request $request, $id = null)
    {
        if ($id === null) {
            $entity = $this->groupManager->createGroup();
        } else {
            $entity = $this->groupManager->find($id);
            $this->throwNotFoundExceptionIfNull($entity);
        }

        $form = $this->createForm($this->getParameter('umbrella_admin.user.group_crud.form'), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->groupManager->update($entity);

            return $this->jsResponseBuilder()
                ->closeModal()
                ->reloadTable('umbrella_usergrouptable')
                ->toastSuccess('message.entity_updated');
        }

        return $this->jsResponseBuilder()
            ->openModalView('@UmbrellaAdmin/UserGroup/edit.html.twig', [
                'form' => $form->createView(),
                'title' => $entity->id ? $this->trans('action.edit_group') : $this->trans('action.add_group'),
                'entity' => $entity,
            ]);
    }

    /**
     * @Route("/delete/{id}", requirements={"id"="\d+"})
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->groupManager->find($id);
        $this->throwNotFoundExceptionIfNull($entity);

        $this->groupManager->remove($entity);

        return $this->jsResponseBuilder()
            ->closeModal()
            ->reloadTable('umbrella_usergrouptable')
            ->toastSuccess('message.entity_deleted');
    }
}
