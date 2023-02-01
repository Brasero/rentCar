<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="vehicule")
 * @ORM\Entity
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
     * @ORM\Column(type="string", length="55")
     * @var string
     */
    private string $marque;

    /**
     * @ORM\Column(type="string", length="10")
     * @var string
     */
    private string $couleur;
}