<?php

namespace App\Controller\Admin;

use App\Entity\Slot;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class ScheduleCrudController extends AbstractCrudController
{
    private static array $breaks = [
        'Drinks break',
        'Lunch break',
    ];

    public static function getEntityFqcn(): string
    {
        return Slot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Schedule')
            ->setDefaultSort(['startTime' => 'ASC'])
            ->setTimeFormat('H:mm');
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Conference Details')
            ->setIcon('fa fa-calendar-day');
        yield AssociationField::new('conference', 'Conference')
            ->setCustomOption(AssociationField::OPTION_QUERY_BUILDER_CALLABLE, [$this, 'conference'])
            ->setRequired(true)
            ->hideOnIndex();
        yield FormField::addPanel('Slot Details')
            ->setIcon('fa fa-clock');
        yield TimeField::new('startTime');
        yield TimeField::new('endTime');
        yield FormField::addPanel('Talk Details')
            ->setIcon('fa fa-chalkboard-teacher');
        yield AssociationField::new('track1', 'Track 1');
        yield AssociationField::new('track2', 'Track 2');
        yield FormField::addPanel('Break Details')
            ->setIcon('fa fa-mug-hot');
        yield ChoiceField::new('breakDetails')->setChoices(array_combine(self::$breaks, self::$breaks));
    }

    public function conference(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->addOrderBy('entity.id', 'DESC');
    }
}
