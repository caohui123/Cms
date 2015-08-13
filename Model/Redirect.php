<?php

namespace Opifer\RedirectBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class Redirect
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=255)
     */
    protected $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=255)
     */
    protected $target;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permanent", type="boolean")
     */
    protected $permanent;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="Opifer\RedirectBundle\Model\Requirement", mappedBy="redirect", cascade={"persist", "remove"}, fetch="EAGER")
     */
    protected $requirements;

    function __construct()
    {
        $this->requirements = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set origin
     *
     * @param string $origin
     *
     * @return Redirect
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return Redirect
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     *
     * @return Redirect
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * Is permanent
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @param Requirement $requirement
     *
     * @return Redirect
     */
    public function addRequirement(Requirement $requirement)
    {
        $requirement->setRedirect($this);

        if (false === $this->requirements->contains($requirement)) {
            $this->requirements->add($requirement);
        }

        return $this;
    }

    /**
     * @param Requirement $requirement
     *
     * @return Redirect
     */
    public function removeRequirement(Requirement $requirement)
    {
        $this->requirements->removeElement($requirement);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRequirements()
    {
        return $this->requirements;
    }
}

