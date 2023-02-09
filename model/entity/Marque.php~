<?php
namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="marque")
 */
class Marque
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length="80")
     * @var string
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="Vehicule", mappedBy="marque")
     */
    private $vehicules;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Marque
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vehicules = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add vehicule.
     *
     * @param \Model\Entity\Vehicule $vehicule
     *
     * @return Marque
     */
    public function addVehicule(\Model\Entity\Vehicule $vehicule)
    {
        $this->vehicules[] = $vehicule;

        return $this;
    }

    /**
     * Remove vehicule.
     *
     * @param \Model\Entity\Vehicule $vehicule
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVehicule(\Model\Entity\Vehicule $vehicule)
    {
        return $this->vehicules->removeElement($vehicule);
    }

    /**
     * Get vehicules.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVehicules()
    {
        return $this->vehicules;
    }
}
