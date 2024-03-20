<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Like Entity
 *
 * @property int $id
 *
 * @property \App\Model\Entity\Like[] $likes
 */
class Like extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
