<?php
namespace Core\Framework\AbstractClass;

/**
 * Un module représente un ensemble de page qui sont chargé d'une responsabilité particulière
 * (Exemple : CarModule est chargé à tout ce qui touche au véhicule ajout, modification, suppression, accès etc)
 * Chaque module que l'on souhaite charger dans l'application doit être déclarer dans $modules dans /public/index.php
 */
abstract class AbstractModule
{
    /**
     * Chemin du fichier de configuration déstinée à PHP DI
     */
    public const DEFINITIONS = null;
}