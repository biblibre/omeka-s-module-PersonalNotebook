<?php

namespace PersonalNotebook\Entity;

use DateTimeImmutable;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Resource;
use Omeka\Entity\User;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(
 *  name="personalnotebook_note",
 *  uniqueConstraints={
 *      @UniqueConstraint(name="personalnotebook_note_owner_resource", columns={"owner_id","resource_id"})
 *  }
 * )
 */
class Note extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="\Omeka\Entity\User")
     * @JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @ManyToOne(targetEntity="\Omeka\Entity\Resource")
     * @JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $resource;

    /**
     * @Column(type="text")
     */
    protected $content = '';

    /**
     * @Column(type="datetime_immutable")
     */
    protected $created;

    /**
     * @Column(type="datetime_immutable")
     */
    protected $modified;

    public function getId()
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource)
    {
        $this->resource = $resource;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(DateTimeImmutable $created)
    {
        $this->created = $created;
    }

    public function getModified(): DateTimeImmutable
    {
        return $this->modified;
    }

    public function setModified(DateTimeImmutable $modified)
    {
        $this->modified = $modified;
    }

    /**
     * @PrePersist
     */
    public function prePersist()
    {
        $now = new DateTimeImmutable();
        $this->setCreated($now);
        $this->setModified($now);
    }

    /**
     * @PreUpdate
     */
    public function preUpdate()
    {
        $now = new DateTimeImmutable();
        $this->setModified($now);
    }
}
