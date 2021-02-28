<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class DateIntervalField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_WITH_YEARS = 'with_years';
    public const OPTION_WITH_MONTHS = 'with_months';
    public const OPTION_WITH_DAYS = 'with_days';
    public const OPTION_WITH_MINUTES = 'with_minutes';
    public const OPTION_MINUTES = 'minutes';

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/date_interval.html.twig')
            ->setFormType(DateIntervalType::class)
            ->setFormTypeOptions([
                self::OPTION_WITH_YEARS => false,
                self::OPTION_WITH_MONTHS => false,
                self::OPTION_WITH_DAYS => false,
                self::OPTION_WITH_MINUTES => true,
                self::OPTION_MINUTES => self::minutesValues(),
            ]);
    }

    private static function minutesValues(): array
    {
        $values = range(5, 60, 5);

        return array_combine($values, $values);
    }
}
