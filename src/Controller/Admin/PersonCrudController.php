<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class PersonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Person::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'People');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isModerator', 'Moderators'))
            ->add(BooleanFilter::new('isOrganiser', 'Organisers'))
            ->add(BooleanFilter::new('isSpeaker', 'Speakers'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield BooleanField::new('isModerator', 'Moderator')->onlyOnForms();
        yield BooleanField::new('isOrganiser', 'Organiser')->onlyOnForms();
        yield BooleanField::new('isSpeaker', 'Speaker')->onlyOnForms();
        yield TextField::new('roles')->onlyOnIndex();
    }
}
