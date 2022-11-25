<?php
namespace PersonalNotebook\Api\Representation;

use DateTimeImmutable;
use Omeka\Api\Representation\AbstractEntityRepresentation;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Api\Representation\UserRepresentation;

class NoteRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-personal-notebook:Note';
    }

    public function getJsonLd()
    {
        $note = $this->resource;
        $owner = $this->owner();
        $resource = $this->resource();

        return [
            'o-module-personal-notebook:owner' => $owner->getReference(),
            'o-module-personal-notebook:resource' => $resource ? $resource->getReference() : null,
            'o-module-personal-notebook:content' => $note->getContent(),
            'o-module-personal-notebook:created' => $note->getCreated(),
            'o-module-personal-notebook:modified' => $note->getModified(),
        ];
    }

    public function owner(): UserRepresentation
    {
        $userAdapter = $this->getAdapter('users');
        $owner = $this->resource->getOwner();

        return $userAdapter->getRepresentation($owner);
    }

    public function resource(): ?AbstractResourceEntityRepresentation
    {
        $resource = $this->resource->getResource();
        if (!$resource) {
            return null;
        }

        $adapter = $this->getAdapter($resource->getResourceName());

        return $adapter->getRepresentation($resource);
    }

    public function content(): string
    {
        return $this->resource->getContent();
    }

    public function created(): DateTimeImmutable
    {
        return $this->resource->getCreated();
    }

    public function modified(): DateTimeImmutable
    {
        return $this->resource->getModified();
    }
}
