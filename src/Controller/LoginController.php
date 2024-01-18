<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    /**
     * @Route("/login", name="usuario_login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils){

        $error = $authenticationUtils->getLastAuthenticationError();

        $msg = '';
        $msg_type = '';
        if($error){
            $msg = 'Usuario y/o contraseña incorrectos';
            $msg_type = 'danger';
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', array(
            'last_username' => $lastUsername,
            'msg' => $msg,
            'msg_type' => $msg_type
        ));

    }

    /**
     * @Route("/logout", name="usuario_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/olvide_pass", name="olvide_pass", methods={"GET"})
     */
    public function olvide_pass(){

        return $this->render('login/olvide_pass.html.twig', array(
            'msg' => '',
            'msg_type' => ''
        ));
    }

    /**
     * @Route("/enviar_email", name="enviar_email", methods={"GET", "POST"})
     */
    public function enviar_email(Request $request, \Swift_Mailer $mailer){

        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('App:Usuario')->findOneBy(array('email' => $email));

        if($usuario){
            $link = "http://getel.cecmed.local/cambiar_pass?_username=".$usuario->getUsername()."&_salt=".$usuario->getSalt();

            $html = '<h3><b>Hola '. $usuario->getUsername() .'</b></h3><br>'.
                'Se ha realizado una solicitud de resetear contraseña en tu usuario del sistema de Gestión de Telefónica del CECMED.<br><br>'.

                'Si cree que esto se ha hecho por error, elimine y descarte este correo electrónico. Su cuenta aún es segura y nadie ha 
                tenido acceso a ella. No está bloqueado y su contraseña no se ha restablecido. Alguien podría haber ingresado 
                erróneamente su dirección de correo electrónico.<br><br>'.

                'Siga el enlace a continuación para ir al sistema y cambiar su contraseña.<br><br>'.

                '<b><a href='.$link.'>Haz Click Aqui</a></b>';


            $message = (new \Swift_Message('Recuperando contraseña'))
                ->setFrom('webmaster@cecmed.cu')
                ->setTo($usuario->getEmail())
                ->setBody($html, 'text/html');

            $mailer->send($message);

            $msg = 'Espere el correo con la confirmación de su cuenta.';
            $msg_type = 'success';
        }else{
            $msg = 'El correo no se encuentra en la base de datos.';
            $msg_type = 'danger';
        }


        return $this->render('login/olvide_pass.html.twig', array(
            'msg' => $msg,
            'msg_type' => $msg_type
        ));

    }

    /**
     * @Route("/cambiar_pass", name="cambiar_pass", methods={"GET"}, options={"expose" : "true"})
     */
    public function cambiar_pass(Request $request){

        $username = $request->get('_username');
        $salt = $request->get('_salt');

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('App:Usuario')->findOneBy(array('username' => $username, 'salt' => $salt));

        return $this->render('login/cambiar_pass.html.twig', array(
            'usuario' => $usuario,
            'msg' => '',
            'msg_type' => ''
        ));
    }

    /**
     * @Route("/actualizar_pass", name="actualizar_pass", methods={"GET", "POST"}, options={"expose" : "true"})
     */
    public function actualizar_pass(Request $request){

        $id = $request->get('id');
        $password = $request->get('password');

        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('App:Usuario')->find($id);

        if($usuario){

            $passwordCodificado = $this->container->get('security.password_encoder')->encodePassword($usuario, $password);
            $usuario->setPassword($passwordCodificado);

            $em->flush();

        }else{
            throw $this->createNotFoundException();
        }

        //Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($usuario, $usuario->getPassword(), 'frontend', $usuario->getRoles());
        $this->get('security.token_storage')->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->get('session')->set('_security_frontend', serialize($token));

        // Fire the login event manually
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        return $this->redirectToRoute('app_homepage');

    }

}
