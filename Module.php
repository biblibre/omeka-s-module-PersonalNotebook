<?php

namespace PersonalNotebook;

use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Omeka\Form\SiteSettingsForm;
use Omeka\Module\AbstractModule;
use PersonalNotebook\Form\SiteSettingsFieldset;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $services = $this->getServiceLocator();

        $acl = $services->get('Omeka\Acl');
        $acl->allow(null, 'PersonalNotebook\Api\Adapter\NoteAdapter');
        $acl->allow(null, 'PersonalNotebook\Controller\Note');
        $acl->allow(null, 'PersonalNotebook\Controller\Site\Note');
        $acl->allow(null, 'PersonalNotebook\Entity\Note');

        $em = $services->get('Omeka\EntityManager');
        $em->getFilters()->enable('personalnotebook_note_visibility');
        $em->getFilters()->getFilter('personalnotebook_note_visibility')->setServiceLocator($services);
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $services = $this->getServiceLocator();
        $siteSettings = $services->get('Omeka\Settings\Site');

        $sharedEventManager->attach(
            SiteSettingsForm::class,
            'form.add_elements',
            [$this, 'onSiteSettingsFormAddElements']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Site\Item',
            'view.show.after',
            [$this, 'onSiteItemViewShowAfter']
        );

        $sharedEventManager->attach(
            'Omeka\Controller\Site\Media',
            'view.show.after',
            [$this, 'onSiteMediaViewShowAfter']
        );
    }

    public function onSiteSettingsFormAddElements(Event $event)
    {
        $services = $this->getServiceLocator();
        $forms = $services->get('FormElementManager');
        $siteSettings = $services->get('Omeka\Settings\Site');

        $fieldset = $forms->get(SiteSettingsFieldset::class);
        $fieldset->populateValues([
            'personalnotebook_show_after_item' => $siteSettings->get('personalnotebook_show_after_item'),
            'personalnotebook_show_after_media' => $siteSettings->get('personalnotebook_show_after_media'),
        ]);

        $form = $event->getTarget();
        $form->add($fieldset);
    }

    public function onSiteItemViewShowAfter(Event $event)
    {
        $services = $this->getServiceLocator();
        $siteSettings = $services->get('Omeka\Settings\Site');
        if (!$siteSettings->get('personalnotebook_show_after_item')) {
            return;
        }

        $view = $event->getTarget();

        echo $view->personalNotebook()->form($view->item);
    }

    public function onSiteMediaViewShowAfter(Event $event)
    {
        $services = $this->getServiceLocator();
        $siteSettings = $services->get('Omeka\Settings\Site');
        if (!$siteSettings->get('personalnotebook_show_after_media')) {
            return;
        }

        $view = $event->getTarget();

        echo $view->personalNotebook()->form($view->media);
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $connection->exec("CREATE TABLE personalnotebook_note (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, resource_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', modified DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_545580397E3C61F9 (owner_id), INDEX IDX_5455803989329D25 (resource_id), UNIQUE INDEX personalnotebook_note_owner_resource (owner_id, resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $connection->exec('ALTER TABLE personalnotebook_note ADD CONSTRAINT FK_545580397E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $connection->exec('ALTER TABLE personalnotebook_note ADD CONSTRAINT FK_5455803989329D25 FOREIGN KEY (resource_id) REFERENCES resource (id) ON DELETE SET NULL');
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $connection = $serviceLocator->get('Omeka\Connection');
        $connection->exec('DROP TABLE personalnotebook_note');
    }

    public function getConfig()
    {
        return require __DIR__ . '/config/module.config.php';
    }
}
