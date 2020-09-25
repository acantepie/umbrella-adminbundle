<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 07/05/17
 * Time: 19:25.
 */

namespace Umbrella\AdminBundle\Controller;

use Umbrella\AdminBundle\Services\UserMailer;
use Symfony\Component\HttpFoundation\Request;
use Umbrella\AdminBundle\Services\UserManager;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\CoreBundle\Controller\BaseController;
use Umbrella\AdminBundle\Form\UserPasswordConfirmType;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;

/**
 * Class SecurityController.
 * @Route("/")
 */
class SecurityController extends BaseController
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * SecurityController constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/login", name="umbrella_admin_login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, Request $request)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            if ($error instanceof AuthenticationServiceException) {
                $error = new BadCredentialsException();
            }
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@UmbrellaAdmin/Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/password_request")
     */
    public function passwordRequestAction(UserMailer $userMailer, Request $request)
    {
        // form submitted
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $this->userManager->findUserByEmail($email);

            if (null !== $user) {
                $user->generateConfirmationToken();
                $user->passwordRequestedAt = new \DateTime();
                $this->userManager->update($user);
                $userMailer->sendPasswordRequestEmail($user);
            }

            return $this->redirectToRoute('umbrella_admin_security_passwordrequestsuccess', [
                'email' => $email
            ]);
        }

        return $this->render('@UmbrellaAdmin/Security/password_request.html.twig');
    }

    /**
     * @Route("/password_request_success")
     */
    public function passwordRequestSuccessAction(Request $request)
    {
        return $this->render('@UmbrellaAdmin/Security/password_request_success.html.twig', [
            'email' => $request->query->get('email')
        ]);
    }

    /**
     * @Route("/password_reset/{token}")
     */
    public function passwordResetAction(Request $request, $token)
    {
        $user = $this->userManager->findUserByConfirmationToken($token);
        $this->throwNotFoundExceptionIfNull($user);

        $form = $this->createForm(UserPasswordConfirmType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->update($user);

            $this->toastSuccess('message.password_resetted');
            return $this->redirectToRoute('umbrella_admin_login');
        }

        return $this->render('@UmbrellaAdmin/Security/password_reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
