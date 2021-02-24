<?php
namespace RRCHNMDonate;

use Omeka\Module\AbstractModule;
use Omeka\Permissions\Assertion\HasSitePermissionAssertion;
use Laminas\Form\Fieldset;
use Laminas\EventManager\Event;
use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\SharedEventManagerInterface;

class Module extends AbstractModule
{
    
    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Form\SiteSettingsForm',
            'form.add_elements',
            [$this, 'addSiteEnableCheckbox']
        );

        $controllers = [
            'Omeka\Controller\Site\Item',
            'Omeka\Controller\Site\ItemSet',
            'Omeka\Controller\Site\Media',
            'Omeka\Controller\Site\Page',
        ];

        foreach ($controllers as $controller) {          
            $sharedEventManager->attach($controller, 'view.layout', [$this, 'addBannerHead']);
        }
    }

    public function addSiteEnableCheckbox($event)
    {
        $siteSettings = $this->getServiceLocator()->get('Omeka\Settings\Site');
        $form = $event->getTarget();

        $fieldset = new Fieldset('rrchnm_donate');
        $fieldset->setLabel('Donate to RRCHNM'); // @translate

        $enabled = $siteSettings->get('donate_banner_enable');
        $fieldset->add([
            'name' => 'donate_banner_enable',
            'type' => 'checkbox',
            'options' => [
                'label' => 'Enable RRCHNM donation banner', // @translate
            ],
            'attributes' => [
                'value' => $enabled
            ]
        ]);

        $form->add($fieldset);
    }
    
    public function addBannerHead($event)
    {
        $siteSettings = $this->getServiceLocator()->get('Omeka\Settings\Site');
        $enabled = $siteSettings->get('donate_banner_enable', 'view.show.before');
        $eventName = $event->getName();
        $view = $event->getTarget();
        if ($enabled == 1) {
            $view->headLink()->appendStylesheet('https://rrchnm.org/donate/donate-style.css');
            $view->headScript()->appendFile('https://rrchnm.org/donate/donate-positioning.js');
            $view->htmlElement('body')->appendAttribute('class', 'rrchnm-donate-omeka-s');
        }       
    }
}

?>
