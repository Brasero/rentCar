<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;

/**
 * @Table(name="vehicule")
 * @Entity
 */
class Vehicule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="modele", length="55")
     * @var string
     */
    private string $model;

    /**
     * @ORM\ManyToOne(targetEntity="Marque", inversedBy="vehicules")
     * @ORM\JoinColumn(name="marque_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Marque
     */
    private Marque $marque;


    /**
     * @ORM\Column(type="string", length="10")
     * @var string
     */
    private string $couleur;

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
     * Set model.
     *
     * @param string $model
     *
     * @return Vehicule
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }


    /**
     * Set couleur.
     *
     * @param string $couleur
     *
     * @return Vehicule
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur.
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set marque.
     *
     * @param Marque|null $marque
     *
     * @return Vehicule
     */
    public function setMarque(Marque $marque = null)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return Marque|null
     */
    public function getMarque()
    {
        return $this->marque;
    }
}
