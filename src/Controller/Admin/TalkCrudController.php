<?php

namespace App\Controller\Admin;

use App\Admin\Field\DateIntervalField;
use App\Entity\Talk;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class TalkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Talk::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Talks');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isArchived', 'Archived?'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Talk Details')
            ->setIcon('fa fa-chalkboard-teacher');
        yield TextField::new('title');
        yield TextEditorField::new('description')->hideOnIndex();
        yield DateIntervalField::new('duration');
        yield AssociationField::new('organiser')->setQueryBuilder(
            fn (QueryBuilder $qb) => $this->role($qb, 'isOrganiser')
        );
        yield AssociationField::new('speakers')->setQueryBuilder(
            fn (QueryBuilder $qb) => $this->role($qb, 'isSpeaker')
        );
        yield FormField::addPanel('Q&A Details')
            ->setIcon('fa fa-question');
        yield AssociationField::new('moderator')->setQueryBuilder(
            fn (QueryBuilder $qb) => $this->role($qb, 'isModerator')
        );
        yield DateIntervalField::new('qAndADuration', 'Q&A Duration')->hideOnIndex();
        yield TextField::new('slackChannel')->hideOnIndex();
        yield TextField::new('slackChannelUrl')->hideOnIndex();
        yield TextField::new('teamsUrl')->hideOnIndex();
        yield TextField::new('slidoText')->hideOnIndex();
        yield TextField::new('slidoUrl')->hideOnIndex();
        yield BooleanField::new('isArchived', 'Archived?')->onlyOnIndex();
    }

    private function role(QueryBuilder $queryBuilder, string $role): QueryBuilder
    {
        return $queryBuilder
            ->andWhere(sprintf('entity.%s = :%s', $role, $role))
            ->setParameter($role, true);
    }
}
