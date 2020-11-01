<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 23/10/17
 * Time: 17:22
 */

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Umbrella\AdminBundle\Entity\BaseUser;
use Umbrella\AdminBundle\Services\UserManager;
use Umbrella\CoreBundle\Controller\BaseController;

/**
 * Class AccountController
 *
 * @Route("/profile")
 */
class ProfileController extends BaseController
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * ProfileController constructor.
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
        $user = $this->getUser();

        if (!$user || !is_a($user, BaseUser::class)) {
            throw new AccessDeniedException();
        }

        $settingsForm = $this->createForm($this->getParameter('umbrella_admin.user.profile_crud.form'), $user);
        $settingsForm->handleRequest($request);

        if ($settingsForm->isSubmitted() && $settingsForm->isValid()) {
            $this->userManager->update($user);

            $this->toastSuccess('message.account_updated');

            return $this->redirectToRoute('umbrella_admin_profile_index');
        }

        return $this->render('@UmbrellaAdmin/Profile/index.html.twig', [
            'user' => $user,
            'settings_form' => $settingsForm->createView(),
        ]);
    }
}
