<?php

namespace UserBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use UserBundle\Exception\BadDesignationException;
use UserBundle\Service\NessApiManager;

class NessUserAuthenticator implements SimpleFormAuthenticatorInterface
{

    /** @var NessApiManager */
    private $nessApiManager;

    /** @var EntityManager */
    private $em;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(NessApiManager $nessApiManager, EntityManager $entityManager, EncoderFactoryInterface $encoderFactory)
    {
        $this->nessApiManager = $nessApiManager;
        $this->em = $entityManager;
        $this->encoderFactory = $encoderFactory;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $credential = $token->getCredentials();

        if ($token->getUser() != null) {
            return $token;
        }

        $user = $this->isAuthenticate($credential['username'], $credential['password']);

        if (null !== $ex = $this->handleExceptions($user)) {
            throw $ex;
        }

        return new UsernamePasswordToken(
            $user,
            $credential,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new PreAuthenticatedToken(
            '',
            array(
                'username' => $username,
                'password' => $password
            ),
            $providerKey
        );
    }

    /**
     * @param $user
     * @return AuthenticationException|null
     */
    private function handleExceptions(UserInterface $user = null)
    {
        if (null == $user) {
            return new BadCredentialsException();
        }

        if (!$user->isEnabled()) {
            return new DisabledException('User account disabled');
        }

        /*if ($this->isPrivilegedUser($user)) {
            return new InsufficientAuthenticationException('Invalid username or password');
        }*/

        return null;
    }

    private function isAuthenticate($username, $password)
    {
        $user = $this->em->getRepository('RbsUserBundle:User')->findOneBy(array('usernameCanonical' => strtolower($username)));
        if (!$user) {
            return null;
        }

        if (!$user->isNessUser() && $this->isPasswordValid($user, $password)) {
            return $user;
        }

        if ($user->isNessUser() && $nessUser = $this->nessApiManager->getUserFormatted($username, $password, $user->getOffice()->getOrganizationId())) {

            if ($user->getProfile()->getDesignation() != $nessUser['designation']) {
                throw new BadDesignationException('Designation not match');
            }

            return $user;
        }

        return null;
    }

    public function isPasswordValid(UserInterface $user, $raw)
    {
        // Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->isPasswordValid($user->getPassword(), $raw, $user->getSalt());
    }
}
