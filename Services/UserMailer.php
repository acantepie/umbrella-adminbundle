<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 24/10/17
 * Time: 20:06
 */

namespace Umbrella\AdminBundle\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Umbrella\AdminBundle\Entity\BaseUser;

/**
 * Class UserMailer
 */
class UserMailer
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * UserMailer constructor.
     *
     * @param Environment           $twig
     * @param RouterInterface       $router
     * @param \Swift_Mailer         $mailer
     * @param ParameterBagInterface $parameters
     */
    public function __construct(Environment $twig, RouterInterface $router, \Swift_Mailer $mailer, ParameterBagInterface $parameters)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    public function sendPasswordRequestEmail(BaseUser $user): void
    {
        $message = new \Swift_Message();
        $message
            ->setSubject('Changement de mot de passe')
            ->setFrom($this->parameters->get('umbrella_admin.user.mailer.from_email'), $this->parameters->get('umbrella_admin.user.mailer.from_name'))
            ->setTo($user->email)
            ->setBody(
                $this->twig->render('@UmbrellaAdmin/Mail/password_request.html.twig', [
                    'user' => $user,
                    'reset_url' => $this->router->generate('umbrella_admin_security_passwordreset', ['token' => $user->confirmationToken], UrlGenerator::ABSOLUTE_URL),
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
