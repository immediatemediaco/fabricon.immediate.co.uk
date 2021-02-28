<?php

namespace App\Controller\Admin;

use App\Admin\Field\DateIntervalField;
use App\Entity\Talk;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
                return $action->setIcon('fas fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action) {
                return $action->setIcon('far fa-trash-alt')->setLabel(false);
            });
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Talk Details')
            ->setIcon('fa fa-chalkboard-teacher');
        yield TextField::new('title');
        yield TextareaField::new('description')->hideOnIndex();
        yield DateIntervalField::new('duration');
        yield AssociationField::new('organiser')
            ->setCustomOption(AssociationField::OPTION_QUERY_BUILDER_CALLABLE, [$this, 'organisers']);
        yield AssociationField::new('speakers')
            ->setCustomOption(AssociationField::OPTION_QUERY_BUILDER_CALLABLE, [$this, 'speakers']);
        yield FormField::addPanel('Q&A Details')
            ->setIcon('fa fa-question');
        yield AssociationField::new('moderator')
            ->setCustomOption(AssociationField::OPTION_QUERY_BUILDER_CALLABLE, [$this, 'moderators']);
        yield DateIntervalField::new('qAndADuration', 'Q&A Duration');
        yield TextField::new('slackChannel');
        yield TextField::new('slackChannelUrl');
        yield TextField::new('teamsUrl');
    }

    public function organisers(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $this->role($queryBuilder, 'isOrganiser');
    }

    public function speakers(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $this->role($queryBuilder, 'isSpeaker');
    }

    public function moderators(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $this->role($queryBuilder, 'isModerator');
    }

    private function role(QueryBuilder $queryBuilder, string $role): QueryBuilder
    {
        return $queryBuilder
            ->andWhere(sprintf('entity.%s = :%s', $role, $role))
            ->setParameter($role, true);
    }
}
