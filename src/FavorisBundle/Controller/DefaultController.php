<?php

namespace FavorisBundle\Controller;

use FavorisBundle\Entity\Directory;
use FavorisBundle\Entity\Favoris;
use FavorisBundle\Entity\User;
use FavorisBundle\FavorisBundle;
use FavorisBundle\Form\DirectoryAddType;
use FavorisBundle\Form\FavorisAddType;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class DefaultController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function homeAction(Request $request)
    {
        $parameters = array(
            'tab'=>'1',
        );

        $em = $this->getDoctrine()->getManager();

        $favoris = $em->getRepository('FavorisBundle:Favoris')
            ->findAll();

        $parameters['favoris'] = $favoris;

        $directories = $em->getRepository('FavorisBundle:Directory')
            ->findAll();

        $parameters['directory'] = $directories;

        $user = $em->getRepository('FavorisBundle:User')
            ->findAll();

        $parameters['user'] = $user;

        return $this->render('FavorisBundle:home:home.html.twig',$parameters);
    }

    /**
     * @Route("/favoris/delete/{favoris}", name="delete_favoris")
     */
    public function deleteAction(Request $request, Favoris $favoris)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($favoris);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/directory/delete/{directory}", name="delete_directory")
     */
    public function deleteDirAction(Request $request, Directory $directory)
    {
        if($directory->getUser_dir() != $this->getUser()){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();

        $em->remove($directory);
        $em->flush();

        return $this->redirectToRoute('home');
    }

//    /**
//     * @Route("/{backgroundSet}", name="bgSetting")
//     */
//    public function bgSettingAction(Request $request, $backgroundSet)
//    {
//        $background = array(
//            'bg'=>'0'
//        );
//
//        if( $backgroundSet == 'blue'){
//            $background = array(
//                'bg'=>'0'
//            );
//        }
//        elseif ($backgroundSet == 'silver'){
//            $background = array(
//                'bg'=>'1'
//            );
//        }
//
//
//        return $this->render('FavorisBundle:home:base.html.twig', $background);
//    }

    /**
     * @Route("/settings", name="settings")
     */
    public function settingsAction(Request $request)
    {
        $parameters = array(
            'tab'=>'2'
        );

        $user = $this->getUser();

        $parameters['User'] = $user;

        return $this->render('FavorisBundle:settings:settings.html.twig', $parameters);
    }

    /**
     * @Route("/add", name="add")
     */
    public function addsAction(Request $request)
    {
        $parameters = array(
            'tab'=>'3'
        );

        $favoris = new Favoris();
        /**
         *
        $advert = new Advert();
        $tokenStorage = $this->get('security.token_storage');
        $form = $this->createForm(new AdvertType($tokenStorage), $advert);
         */

        $formFavorisAdd = $this->createForm(FavorisAddType::class,$favoris);

        $formFavorisAdd->handleRequest($request);

        if ($formFavorisAdd->isSubmitted() && $formFavorisAdd->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($favoris);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        $parameters['formFavorisAdd'] = $formFavorisAdd->createView();

        $directory = new Directory();

        $formDirectoryAdd = $this->createForm(DirectoryAddType::class,$directory);

        $formDirectoryAdd->handleRequest($request);

        if ($formDirectoryAdd->isSubmitted() && $formDirectoryAdd->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $directory->setUser_dir($this->getUser());
            $em->persist($directory);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        $parameters['formDirectoryAdd'] = $formDirectoryAdd->createView();

        return $this->render('FavorisBundle:add:add.html.twig', $parameters);
    }

    /**
     * @Route("/get/url/info", name="url_crawl")
     */
    public function urlCrawlAction(Request $request)
    {

        $url = $request->get('data');
        $parts = parse_url($url);
        $client = new \GuzzleHttp\Client(['defaults' => [
            'verify' => false
        ]]);
//        $this->client = new GuzzleClient(['defaults' => [
//            'verify' => false
//        ]]);
        $response = $client->request('GET', $url);
        // $metas = $response->getBody()->getMetadata();
        //$title = $response->getBody()->getMetadata('title');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler();
        $crawler->addContent($html);



        $title = $crawler->filter('head > title')->text();

        $descriptionNode = ($crawler->filterXPath('//meta[@property="og:description"]') ) ? $crawler->filterXPath('//meta[@property="og:description"]') ->extract(array('content')):null;

        if (!$descriptionNode) {
            $descriptionNode = ($crawler->filterXPath('//meta[@name="description"]') ) ? $crawler->filterXPath('//meta[@name="description"]') ->extract(array('content')):null;
        }

        $favicon = ($crawler->filterXPath('//link[@rel="apple-touch-icon"]') ) ? $crawler->filterXPath('//link[@rel="apple-touch-icon"]') ->extract(array('href')):null;

        if (!$favicon){
            $favicon = ($crawler->filterXPath('//link[@rel="fluid-icon"]') ) ? $crawler->filterXPath('//link[@rel="fluid-icon"]') ->extract(array('href')):null;
        }
        if (!$favicon){
            $favicon = ($crawler->filterXPath('//link[@rel="shortcut icon"]') ) ? $crawler->filterXPath('//link[@rel="shortcut icon"]') ->extract(array('href')):null;
        }
        if (!$favicon){
            $favicon = ($crawler->filterXPath('//link[@rel="icon"]') ) ? $crawler->filterXPath('//link[@rel="icon"]') ->extract(array('href')):null;
        }
       // $description = ($crawler->filterXPath('//meta[contains(@name, "description")]')) ? $crawler->filterXPath('head > meta[contains(@name, "description")]')->text() : null;
        $faviconResponse = null;
        if(count($favicon) > 0){
            if(preg_match('#^(https?:\/\/){1}#', $favicon[0])) $faviconResponse = $favicon[0];
            elseif(preg_match('#^/{2}#', $favicon[0])) $faviconResponse = $parts['scheme'].':'.$favicon[0];
            elseif(preg_match('#^/{1}#', $favicon[0])) $faviconResponse = $parts['scheme'].'://'.$parts['host'].$favicon[0];
            else $faviconResponse = $parts['scheme'].'://'.$parts['host'].'/'.$favicon[0];
        }

        $data = array(
            'title' => $title,
            'description'=>(count($descriptionNode) > 0) ?$descriptionNode[0]: null,
            'favicon'=> $faviconResponse,
        );

//        and preg_match("#(((https|http)://(w{3}\.))?([a-zA-Z0-9]|-)+\.([a-z]{2,4}))#",$favicon)
        $response = new JsonResponse();
        $response->setData($data);

        return $response;

        //return $this->render('FavorisBundle:add:add.html.twig');
    }

//    public function formAction(Request $request)
//    {
//        $favoris = new Favoris();
//
//        $formBuilder = $this->get('form.factory')->createBuilder('form', $favoris);
//
//        $formBuilder
//            ->add('url',      'string')
//            ->add('titre',     'string')
//            ->add('description',   'text')
//            ->add('position',    'integer')
//            ->add('date', 'date')
//            ->add('favicon',      'string')
//            ->add('capture',      'string')
//            ->add('id_user',      'integer')
//            ->add('save',      'submit');
//
//        $form = $formBuilder->getForm();
//
//        return $this->render('FavorisBundle:Favoris:formulaire.html.twig', array('form' => $form->createView(),));
//
//    }
}