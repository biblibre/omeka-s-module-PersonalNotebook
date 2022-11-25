<?php

namespace PersonalNotebook;

use Omeka\Module\AbstractModule;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
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
