<?php

namespace Gos\Bundle\NotificationBundle\Loader;

class LoaderResolver implements LoaderResolverInterface
{
    /** @var  LoaderInterface[] */
    protected $loaders;

    /**
     * @param LoaderInterface $loader
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * @param mixed $type
     *
     * @return bool|LoaderInterface
     */
    public function resolve($type)
    {
        foreach($this->loaders as $loader){
            if($loader->supports($type)){
                return $loader;
            }
        }

        return false;
    }
}