<?php

namespace Energ\Helpers;

class CommunityValidator
{
    public static function list()
    {
        return [
            'Oil & Gas' => [
                'Upstream',
                'Pipelines',
                'Refining',
                'Petrochemicals',
                'CGD',
                'LPG',
                'Retail',
                'Oil Markets',
            ],
            'Power Generation' => [
                'Thermal',
                'Nuclear',
            ],
            'Renewables' => [
                'Solar',
                'Wind',
                'Hydro',
                'Biopower',
                'Cogeneration',
                'Waste-to-Energy',
            ],
            'Transmission' => [
                'Smart Grid',
            ],
            'Distribution' => [
                'Smart Meters & AMI',
                'EV Charging',
                'Data Centres',
                'Smart Cities',
                'Railways & Metros',
            ],
            'Electricity Markets' => [
                'Power Markets',
                'Carbon Markets',
                'RCO',
            ],
            'New Energies' => [
                'Green Hydrogen',
                'E-Fuels',
            ],
            'Energy Storage Systems' => [
                'BESS',
                'Pumped Hydro',
                'CAES',
                'Thermal',
                'Flywheel',
            ],
            'Sustainability' => [
                'Energy Efficiency',
                'Occupational Health',
                'Industrial & Process Safety',
                'Environment',
            ],
        ];
    }

    public static function isValid($community, $sub)
    {
        $list = self::list();
        return isset($list[$community]) && in_array($sub, $list[$community], true);
    }
}