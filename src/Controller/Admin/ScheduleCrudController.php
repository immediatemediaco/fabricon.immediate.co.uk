<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Entity\Slot;
use App\Repository\SettingsRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class ScheduleCrudController extends AbstractCrudController
{
    private static array $breaks = [
        'Drinks break',
        'Lunch break',
        'Pizza and drinks',
    ];

    private string $indexPageTitle = '';

    public function __construct(
        private SettingsRepository $settings,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Slot::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, [$this, 'indexPageTitle'])
            ->setDefaultSort(['startTime' => 'ASC'])
            ->setTimeFormat('H:mm');
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder->innerJoin(Settings::class, 's', Join::WITH, 'entity.conference = s.currentConference');

        return $queryBuilder;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Conference Details')
            ->setIcon('fa fa-calendar-day');
        yield AssociationField::new('conference', 'Conference')
            ->setQueryBuilder(fn (QueryBuilder $qb) => $qb->addOrderBy('entity.id', 'DESC'))
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

    public function indexPageTitle(): string
    {
        if ($this->indexPageTitle) {
            return $this->indexPageTitle;
        }

        $currentConference = ($this->settings->find(1))?->getCurrentConference();

        $this->indexPageTitle = 'Schedule - ' . $currentConference?->getName();

        return $this->indexPageTitle;
    }
}
