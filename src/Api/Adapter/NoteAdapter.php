<?php

namespace PersonalNotebook\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Entity\Item;
use Omeka\Entity\ItemSet;
use Omeka\Entity\Media;
use Omeka\Stdlib\ErrorStore;
use PersonalNotebook\Api\Representation\NoteRepresentation;
use PersonalNotebook\Entity\Note;

class NoteAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'id' => 'id',
        'created' => 'created',
        'modified' => 'modified',
    ];

    public function getResourceName()
    {
        return 'personalnotebook_notes';
    }

    public function getRepresentationClass()
    {
        return NoteRepresentation::class;
    }

    public function getEntityClass()
    {
        return Note::class;
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $this->hydrateOwner($request, $entity);
        $this->hydrateResource($request, $entity);

        if ($this->shouldHydrate($request, 'o-module-personal-notebook:content')) {
            $entity->setContent($request->getValue('o-module-personal-notebook:content', ''));
        }
    }

    public function hydrateOwner(Request $request, EntityInterface $entity)
    {
        $data = $request->getContent();
        $owner = $entity->getOwner();
        if ($this->shouldHydrate($request, 'o-module-personal-notebook:owner')) {
            if (array_key_exists('o-module-personal-notebook:owner', $data)
                && is_array($data['o-module-personal-notebook:owner'])
                && array_key_exists('o:id', $data['o-module-personal-notebook:owner'])
            ) {
                $newOwnerId = $data['o-module-personal-notebook:owner']['o:id'];
                $newOwnerId = is_numeric($newOwnerId) ? (int) $newOwnerId : null;

                $oldOwnerId = $owner ? $owner->getId() : null;

                if ($newOwnerId !== $oldOwnerId) {
                    $this->authorize($entity, 'change-owner');
                    $owner = $newOwnerId
                        ? $this->getAdapter('users')->findEntity($newOwnerId)
                        : null;
                }
            }
        }
        if (!$owner instanceof User
            && Request::CREATE == $request->getOperation()
        ) {
            $owner = $this->getServiceLocator()
                ->get('Omeka\AuthenticationService')->getIdentity();
        }
        $entity->setOwner($owner);
    }

    public function hydrateResource(Request $request, EntityInterface $entity)
    {
        $data = $request->getContent();
        $resource = $entity->getResource();
        if ($this->shouldHydrate($request, 'o-module-personal-notebook:resource')) {
            if (array_key_exists('o-module-personal-notebook:resource', $data)
                && is_array($data['o-module-personal-notebook:resource'])
                && array_key_exists('o:id', $data['o-module-personal-notebook:resource'])
            ) {
                $newResourceId = $data['o-module-personal-notebook:resource']['o:id'];
                $newResourceId = is_numeric($newResourceId) ? (int) $newResourceId : null;

                $oldResourceId = $resource ? $resource->getId() : null;

                if ($newResourceId !== $oldResourceId) {
                    $this->authorize($entity, 'change-resource');

                    if ($newResourceId) {
                        $em = $this->getEntityManager();
                        $resource = $em->find(Item::class, $newResourceId);
                        if (!$resource) {
                            $resource = $em->find(Media::class, $newResourceId);
                        }
                        if (!$resource) {
                            $resource = $em->find(ItemSet::class);
                        }
                    }
                }
            }
        }

        $entity->setResource($resource);
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (!empty($query['owner_id'])) {
            $userAlias = $this->createAlias();
            $qb->innerJoin('Omeka\Entity\User', $userAlias, 'WITH', $userAlias . ' = omeka_root.owner');
            $qb->andWhere(
                $qb->expr()->eq(
                    "$userAlias",
                    $this->createNamedParameter($qb, $query['owner_id'])
                )
            );
        }

        if (!empty($query['resource_id'])) {
            $qb->andWhere(
                $qb->expr()->eq(
                    'omeka_root.resource',
                    $this->createNamedParameter($qb, $query['resource_id'])
                )
            );
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        if (!$entity->getOwner()) {
            $errorStore->addError('o-module-personal-notebook:owner', 'A note must have an owner.'); // @translate
        }
    }
}
