<?php

namespace AppBundle\Controller;

use AppBundle\Event\ContactEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Site:index.html.twig');
    }

    /**
     * @Route("/about-me", name="aboutMe")
     */
    public function aboutMeAction()
    {
        return $this->render('AppBundle:Site:aboutMe.html.twig');
    }

    /**
     * @Route("/gallery", name="gallery")
     */
    public function galleryAction()
    {
        $finder = new Finder();
        $finder->files()->in($this->get('kernel')->getRootDir() . '/../web/images/gallery');

        $images = array();
        foreach ($finder as $file) {

            $images[] = array(
                'name' => $file->getRelativePathname(),
                'title' => basename($file->getRelativePathname())
            );
        }

        return $this->render('AppBundle:Site:gallery.html.twig', array('images' => $images));
    }

    /**
     * @Route("/testimonials", name="testimonials")
     */
    public function testimonialsAction()
    {
        return $this->render('AppBundle:Site:testimonials.html.twig');
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faqAction()
    {
        return $this->render('AppBundle:Site:faq.html.twig');
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @Route("/contact", name="contact")
     * @Method("GET")
     *
     * @return Response
     */
    public function newAction()
    {
        $entity = new Contact();

        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Site:contact.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Contact entity.
     *
     * @Route("/contact", name="contact_create")
     * @Method("POST")
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createCreateForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->get('event_dispatcher')->dispatch('contact.event.created', new ContactEvent($contact));

            $this->get('session')->set('contact_id', $contact->getId());

            return $this->redirect($this->generateUrl('contact_success'));
        }

        return $this->render('AppBundle:Site:contact.html.twig', array(
            'entity' => $contact,
            'form'   => $form->createView(),
        ));
    }

    /**
     * @Route("/thankyou", name="contact_success")
     * @Method("GET")
     * @return Response
     */
    public function contactSuccessAction()
    {
        if(null === $contactId = $this->get('session')->get('contact_id'))
        {
            throw new NotFoundHttpException('');
        }
        if(null === $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($contactId))
        {
            throw new NotFoundHttpException('');
        }

        return $this->render('AppBundle:Site:thankYou.html.twig', array(
            'contact' => $contact
        ));
    }

    /**
     * Creates a form to create a Contact entity.
     *
     * @param Contact $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Contact $entity)
    {
        $form = $this->createForm(new ContactType(), $entity, array(
            'action' => $this->generateUrl('contact_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }


}
