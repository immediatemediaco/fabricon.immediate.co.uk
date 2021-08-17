<?php

namespace App\Controller\Admin;

use App\Entity\Suggestion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SuggestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Suggestion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Name(s)');
        yield TextField::new('type', 'Type');
        yield TextEditorField::new('topic');
        yield DateTimeField::new('date');
    }
}
