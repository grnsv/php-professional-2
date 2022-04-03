<?php

namespace App\Factories;

use App\Decorator\LikeDecorator;
use App\Entities\Like\LikeInterface;

interface LikeFactoryInterface extends FactoryInterface
{
    public function create(LikeDecorator $commentDecorator): LikeInterface;
}
