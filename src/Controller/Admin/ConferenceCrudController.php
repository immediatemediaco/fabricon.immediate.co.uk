<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConferenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conference::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Conferences')
            ->setDefaultSort(['date' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield SlugField::new('slug')->setTargetFieldName('name');
        yield TextField::new('siteTitle');
        yield DateField::new('date');
        yield TextEditorField::new('about');
        yield TextField::new('slackChannel')->hideOnIndex();
        yield TextField::new('slackChannelUrl')->hideOnIndex();
        yield TextField::new('feedbackFormUrl')->hideOnIndex();
        yield BooleanField::new('holdingPageEnabled');
        yield AssociationField::new('theme')
            ->setQueryBuilder(fn(QueryBuilder $qb) => $this->sortThemes($qb))
            ->hideOnIndex();
    }

    private function sortThemes(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder->orderBy('entity.id', 'DESC');
    }
}
