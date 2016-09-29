<?php

namespace Jeloo\LaraMigrations;

interface GeneratorInterface
{

    public function generateUp();

    public function generateDown();

}