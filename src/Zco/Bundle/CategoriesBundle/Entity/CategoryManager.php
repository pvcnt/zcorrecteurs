<?php

namespace Zco\Bundle\CategoriesBundle\Entity;

class CategoryManager
{
    public function get($id)
    {
        return InfosCategorie($id);
    }

    public function getChildren($id)
    {
        return ListerEnfants($id);
    }

    public function getCurrentId()
    {
        return GetIDCategorieCourante();
    }

    public function getModuleId($name)
    {
        return GetIDCategorie($name);
    }
}