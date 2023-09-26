<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use App\Entity\Person;
use App\Entity\Question;
use App\Entity\Settings;
use App\Entity\Slot;
use App\Entity\Suggestion;
use App\Entity\Talk;
use App\Entity\Theme;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $urlGenerator,
    ) {
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $scheduleUrl = $this->urlGenerator
            ->setController(ScheduleCrudController::class)
            ->generateUrl();

        return $this->redirect($scheduleUrl);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Fabric Conference');
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->showEntityActionsInlined();
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
                return $action->setIcon('fas fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action) {
                return $action->setIcon('far fa-trash-alt')->setLabel(false);
            });
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Go to site', 'fa fa-arrow-alt-circle-right', 'app_home');
        yield MenuItem::section('Menu');
        yield MenuItem::linkToCrud('Schedule', 'fas fa-clock', Slot::class);
        yield MenuItem::linkToCrud('Conferences', 'fa fa-calendar-day', Conference::class);
        yield MenuItem::linkToCrud('Talks', 'fa fa-chalkboard-teacher', Talk::class)
            ->setQueryParameter('filters[isArchived]', '0');
        yield MenuItem::linkToCrud('People', 'fa fa-users', Person::class);
        yield MenuItem::linkToCrud('Questions', 'fas fa-question-circle', Question::class);
        yield MenuItem::linkToCrud('Suggestions', 'fas fa-exclamation-circle', Suggestion::class);
        yield MenuItem::linkToCrud('Themes', 'fa-solid fa-palette', Theme::class);
        yield MenuItem::linkToCrud('Settings', 'fa fa-gear', Settings::class)
            ->setAction(Action::DETAIL)
            ->setEntityId(1);
    }
}
