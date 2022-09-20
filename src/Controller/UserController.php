<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\form;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/registration/form", name="registration_form")
     */
    public function formRegistration(): Response
    {
        $request = Request::createFromGlobals(); //récupérer les informations dans l'url
        $error = false;
        if ($request->query->get('error')) {
            $error = $request->query->get('error');
        }

        return $this->render(
            'registration.html.twig',
            [
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/user/compte/form", name="compte_form")
     */
    public function formCompte(): Response
    {
        $request = Request::createFromGlobals(); //récupérer les informations dans l'url
        $error = false;
        if ($request->query->get('error')) {
            $error = $request->query->get('error');
        }
        return $this->render(
            'user.html.twig',
            [
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/user/compte/list", name="Users_all")
     */
    public function usersAll(ManagerRegistry $doctrine, Security $security): Response
    {
        $request = Request::createFromGlobals(); //récupérer les informations dans l'url
        $entityManager = $doctrine->getManager();
        $connect = $security->getUser();
        if ($connect) {
            $idconnect = $connect->getId();


            if ($connect->getRoles()[0] == "ROLE_SUPER_ADMIN") {
                $user = $entityManager->getRepository('App\Entity\User')->findAll();
                return $this->render('show_users.html.twig', [
                    'listeUsers' => $user,
                ]);
            } else {
                $user = $entityManager->getRepository('App\Entity\User')->find($idconnect);
                //dd($user);
                return $this->render('show_users.html.twig', [
                    'listeUsers' => $user,
                ]);
            }
        }
    }
    /**
     * @Route("/user/compte/showdelete/{id}", name="del_user")
     */
    public function delUser(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository('App\Entity\User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'Aucun utilisateur trouvé avec l\`identifiant : ' . $id
            );
        }
        return $this->render(
            'del_user.html.twig',
            [
                'id' => $id,
            ]
        );
    }
    /**
     * @Route("/user/compte/delete/{id}", name="destroy")
     */
    public function destroy(ManagerRegistry $doctrine, Request $request, int $id): Response
    {

        $request = Request::createFromGlobals();

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository('App\Entity\User')->find($id);

        $entityManager->remove($user);
        $entityManager->flush();


        return new JsonResponse(['success' => 'L\'utilisateur numéro ' . $id . ' a bien été supprimé !']);
    }

    /**
     * @Route("/user/compte/edit/{id}", name="edit_user")
     */
    public function editUser(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository('App\Entity\User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'Aucun utilisateur trouvé avec l\`identifiant : ' . $id
            );
        }
        return new JsonResponse(['name' => $user->getFullName(), 'email' => $user->getEmail(), 'roles' => $user->getRoles()]);
    }

    /**
     * @Route("/user/compte/store/{id}", name="store_user")
     */
    public function storeUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository('App\Entity\User')->find($id);

        $user->setFullName($request->query->get('name'));

        $user->setEmail($request->query->get('email'));

        if ($request->query->get('role') == 'ROLE_USER') {
            return new JsonResponse(['error' => 'Vous devez obligatoirement sélectionner un rôle dans la zone de liste...']);
        } else if ($request->query->get('role') == 'ROLE_SUPER_ADMIN') {
            $roleName = 'administrateur';
        } else if ($request->query->get('role') == 'ROLE_MOD') {
            $roleName = 'modérateur';
        } else if ($request->query->get('role') == 'ROLE_CLI') {
            $roleName = 'client';
        }

        $user->setRoles([$request->query->get('role')]);
        $user->setRoleName($roleName);

        $currentPassword = $request->query->get('motDePasse');
        $plaintextPassword = $request->query->get('NewmotDePasse');

        if ($passwordHasher->isPasswordValid($user, $currentPassword)) {

            if ($currentPassword != $plaintextPassword) {


                if ($request->query->get('NewmotDePasse') == $request->query->get('repeatMotDePasse')) {
                    // hash the password (based on the security.yaml config for the $user class)
                    $containsLetter  = preg_match('/[a-zA-Z]/',    $plaintextPassword);
                    $containsDigit   = preg_match('/\d/',          $plaintextPassword);
                    $containsSpecial = preg_match('/[^a-zA-Z\d]/', $plaintextPassword);

                    if ($containsLetter != 0 and $containsDigit != 0 and $containsSpecial != 0) {
                        $hashedPassword = $passwordHasher->hashPassword(
                            $user,
                            $plaintextPassword
                        );

                        $user->setPassword($hashedPassword);

                        $entityManager->persist($user);

                        $entityManager->flush();

                        return new JsonResponse(['success' => 'L\'utilisateur numéro ' . $id . ' a bien été modifié !']);
                    } else {
                        return new JsonResponse(['error' => 'Votre nouveau mot de passe doit contenir au moins 1 chiffre, 1 lettre, 1 caractère spécial']);
                    }
                } else {
                    return new JsonResponse(['error' => 'Veuillez entrez deux fois le même mot de passe !']);
                }
            } else {
                return new JsonResponse(['error' => 'Votre nouveau mot de passe est incorrect !']);
            }
        } else {
            return new JsonResponse(['error' => 'Votre mot de passe est incorrect !']);
        }
    }
    /**
     * @Route("/user/compte/add", name="addNewUser")
     */
    public function addNewUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals(); //récupérer les informations dans l'url
        $user = new User(); // création du nouvel utilisateur dans la BDD

        if ($request->query->get('name') == "") {
            return new JsonResponse(['error' => 'Veuillez saisir votre nom svp...']);
        } else {
            $user->setFullName($request->query->get('name'));
        }
        if ($request->query->get('email') == "") {
            return new JsonResponse(['error' => 'Veuillez saisir votre e-mail svp...']);
        } else {
            $user->setEmail($request->query->get('email'));
        }

        if ($request->query->get('role') == 'ROLE_USER') {
            return new JsonResponse(['error' => 'Vous devez obligatoirement sélectionner un rôle dans la zone de liste...']);
        } else if ($request->query->get('role') == 'ROLE_SUPER_ADMIN') {
            $roleName = 'administrateur';
        } else if ($request->query->get('role') == 'ROLE_MOD') {
            $roleName = 'modérateur';
        } else if ($request->query->get('role') == 'ROLE_CLI') {
            $roleName = 'client';
        }
        $user->setRoles([$request->query->get('role')]);
        $user->setRoleName($roleName);
        if ($request->query->get('motDePasse') == $request->query->get('repeatMotDePasse')) {
            $plaintextPassword = $request->query->get('motDePasse');
            $containsLetter  = preg_match('/[a-zA-Z]/',    $plaintextPassword);
            $containsDigit   = preg_match('/\d/',          $plaintextPassword);
            $containsSpecial = preg_match('/[^a-zA-Z\d]/', $plaintextPassword);

            if ($containsLetter != 0 and $containsDigit != 0 and $containsSpecial != 0) {
                // hash the password (based on the security.yaml config for the $user class)
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plaintextPassword
                );
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();


                return new JsonResponse(['success' => 'L\'utilisateur a bien été ajouté !']);
            } else {
                return new JsonResponse(['error' => 'Votre nouveau mot de passe doit contenir au moins 1 chiffre, 1 lettre, 1 caractère spécial']);
            }
        } else {
            return new JsonResponse(['error' => 'Veuillez entrez deux fois le même mot de passe !']);
        }
    }

    /**
     * @Route("/user/compte/password/{id}/{mdp}", name="compte_info", requirements={"mdp"=".+"})
     */
    public function infoCompte(ManagerRegistry $doctrine, int $id, string $mdp, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository('App\Entity\User')->find($id);

        $plaintextPassword = $mdp;

        if (!$passwordHasher->isPasswordValid($user, $plaintextPassword)) // vérification que le mot de passe est correct
            $verification = false;
        else
            $verification = true;

        return new JsonResponse($verification);
    }

    /**
     * @Route("/user/compte/update", name="compte_update")
     */
    public function updateUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals(); //récupérer les informations dans l'url
        $user = $entityManager->getRepository('App\Entity\User')->find($request->query->get('idUser'));


        $currentPassword = $request->query->get('currentMotDePasse');
        $plaintextPassword = $request->query->get('newMotDePasse');

        if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
            if ($request->query->get('newMotDePasse') == $request->query->get('repeatNewMotDePasse')) {
                // hash the password (based on the security.yaml config for the $user class)
                $containsLetter  = preg_match('/[a-zA-Z]/',    $plaintextPassword);
                $containsDigit   = preg_match('/\d/',          $plaintextPassword);
                $containsSpecial = preg_match('/[^a-zA-Z\d]/', $plaintextPassword);

                if ($containsLetter != 0 and $containsDigit != 0 and $containsSpecial != 0) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $plaintextPassword
                    );

                    $user->setPassword($hashedPassword);

                    $entityManager->persist($user);

                    $entityManager->flush();

                    return $this->redirectToRoute(
                        'connexion_form',
                        [
                            'error' => 'Modification réussie, veuillez vous connecter !',
                        ]
                    );
                } else {
                    return $this->redirectToRoute(
                        'compte_form',
                        [
                            'error' => 'Votre nouveaux mot de passe doit contenir au moins 1 chiffres, 1 lettre, 1 caractère spécial',
                        ]
                    );
                }
            } else {
                return $this->redirectToRoute(
                    'compte_form',
                    [
                        'error' => 'Veuillez entrez deux fois le même mot de passe !',
                    ]
                );
            }
        } else {
            return $this->redirectToRoute(
                'compte_form',
                [
                    'error' => 'Votre mot de passe est incorrect !',
                ]
            );
        }
    }

    /**
     * @Route("/user/registration/add", name="registration_add")
     */
    public function addUser(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $doctrine->getManager();
        $request = Request::createFromGlobals(); //récupérer les informations dans l'url

        if ($request->query->get('role') == 'ROLE_USER') {
            $roleName = 'utilisateur';
        } else if ($request->query->get('role') == 'ROLE_SUPER_ADMIN') {
            $roleName = 'administrateur';
        } else if ($request->query->get('role') == 'ROLE_MOD') {
            $roleName = 'modérateur';
        } else if ($request->query->get('role') == 'ROLE_CLI') {
            $roleName = 'client';
        }

        $user = new User(); // création du nouvel utilisateur dans la BDD
        $user->setFullName($request->query->get('name'));
        $user->setEmail($request->query->get('mail'));
        $user->setRoles([$request->query->get('role')]);
        $user->setRoleName($roleName);

        if ($request->query->get('motDePasse') == $request->query->get('repeatMotDePasse')) {
            $plaintextPassword = $request->query->get('motDePasse');
            $containsLetter  = preg_match('/[a-zA-Z]/',    $plaintextPassword);
            $containsDigit   = preg_match('/\d/',          $plaintextPassword);
            $containsSpecial = preg_match('/[^a-zA-Z\d]/', $plaintextPassword);

            if ($containsLetter != 0 and $containsDigit != 0 and $containsSpecial != 0) {
                // hash the password (based on the security.yaml config for the $user class)
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plaintextPassword
                );
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);

                $entityManager->flush();

                return $this->redirectToRoute(
                    'app_login',
                    [
                        'error' => 'Ajout réussie !',
                    ]
                );
            } else {
                return $this->redirectToRoute(
                    'registration_form',
                    [
                        'error' => 'Votre mot de passe doit contenir au moins 1 chiffres, 1 lettre, 1 caractère spécial',
                    ]
                );
            }
        } else {
            return $this->redirectToRoute(
                'registration_form',
                [
                    'error' => 'Veuillez entrez deux fois le même mot de passe !',
                ]
            );
        }
    }

    // /**
    //  * @Route("/", name="homepage")
    //  */
    // public function homePage(): Response
    // {
    //     return $this->redirectToRoute('connexion_form');
    // }

    // 
    // public function formConnexion(AuthenticationUtils $utils): Response
    // {
    //     // get the login error if there is one
    //     $error = $utils->getLastAuthenticationError();
    //     // last username entered by the user
    //     $lastUsername = $utils->getLastUsername();
    //     dd($utils->getLastUsername());

    //     if ($this->isGranted('ROLE_USER')) {

    //         // REDIRECTION SI CONNECTER
    //         return $this->render('home.html.twig', []);
    //     }

    //     $request = Request::createFromGlobals(); //récupérer les informations dans l'url
    //     $success = false;
    //     if ($request->query->get('error')) {
    //         $success = $request->query->get('error');
    //     }

    //     return $this->render(
    //         'signIn.html.twig',
    //         [
    //             'last_username' => $lastUsername,
    //             'error'         => $error,
    //             'success'       => $success

    //         ]
    //     );
    // }
}
