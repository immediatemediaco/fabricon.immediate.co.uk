<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Admins');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('email');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('email');
    }
}
