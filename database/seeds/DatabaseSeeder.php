<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            BrandsTableSeeder::class,
            DepartmentsTableSeeder::class,
            DestinationsTableSeeder::class,
            OrganizationsTableSeeder::class,
            DivisionsTableSeeder::class,
            TermsTableSeeder::class,
            ColorsTableSeeder::class,
            SizesTableSeeder::class,
            StoreTableSeeder::class,
            PrefsTableSeeder::class,
            SalesTypesTableSeeder::class,
            TopContentsMasterSeeder::class,

            // EventsSeeder::class,
            ItemsTableSeeder::class,
            ItemImagesTableSeeder::class,
            // UrgentNoticesTableSeeder::class,
            // TopContentsTableSeeder::class,
            // HelpsTableSeeder::class,
            // InformationsTableSeeder::class,
            // PagesTableSeeder::class,
            // OrdersTableSeeder::class,
            // OrderDetailsTableSeeder::class,
            OrdersTableSeeder::class,
            PlansTableSeeder::class,
        ]);
    }
}
