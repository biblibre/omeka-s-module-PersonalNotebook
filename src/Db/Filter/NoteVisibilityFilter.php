<?php

namespace PersonalNotebook\Db\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PersonalNotebook\Entity\Note;

class NoteVisibilityFilter extends SQLFilter
{
    protected $serviceLocator;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $this->serviceLocator->get('Omeka\Logger')->err('aaaa');
        if ($targetEntity->getName() !== Note::class) {
            return '';
        }

        $acl = $this->serviceLocator->get('Omeka\Acl');
        if ($acl->userIsAllowed(Note::class, 'view-all')) {
            return '';
        }

        $identity = $this->serviceLocator->get('Omeka\AuthenticationService')->getIdentity();
        if (!$identity) {
            return '0';
        }

        return sprintf('%s.owner_id = %d', $targetTableAlias, $identity->getId());
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
