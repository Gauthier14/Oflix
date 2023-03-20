<?php 

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger {

    private $slugger;

    private $toLower ;

    public function __construct(SluggerInterface $SluggerInterface, bool $toLower )
    {
        $this->slugger = $SluggerInterface;
        $this->toLower  = $toLower;

    }

    public function slugify(string $var)
    {
        if ($this->toLower === true) {
            $slug = $this->slugger->slug($var)->lower();

            return $slug ; 
        }

        $slug = $this->slugger->slug($var);
        return $slug ; 
    }
}