<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class JwtSubscriber implements EventSubscriberInterface
{
    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtManager, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtManager;
        $this->userRepository = $userRepository;
    }

    public function onLexikJwtAuthenticationOnAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $token = $event->getData();

        $data = $this->jwtEncoder->decode($token['token']);
        
        $user = $this->userRepository->findByEmail($data['username']);

        if (!empty($user)) {
            $event->setData(['user' => $user, 'token' => $token, 'tokenData' => $data]);
        } else {
            $event->setData(['user' => null]);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onLexikJwtAuthenticationOnAuthenticationSuccess',
        ];
    }
}
